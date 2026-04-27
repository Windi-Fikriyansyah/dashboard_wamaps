<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Display the search leads page.
     */
    public function index()
    {
        return view('leads.index');
    }

    /**
     * Perform lead search using local cache or Google Maps API via SearchAPI.io.
     */
    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string',
            'location_name' => 'required|string',
            'radius' => 'required|numeric',
            'max_results' => 'integer|min:1|max:100',
        ]);

        $keyword = $request->keyword;
        $locationName = $request->location_name;
        $radius = (float) $request->radius;
        $maxResults = (int) ($request->max_results ?? 20);

        $user = Auth::user();

        // 1. Identify all unique leads already stored for this criteria in the database
        // We join leads with searches via the search_leads pivot table
        $existingLeads = DB::table('leads')
            ->join('search_leads', 'leads.id', '=', 'search_leads.lead_id')
            ->join('searches', 'searches.id', '=', 'search_leads.search_id')
            ->whereRaw('LOWER(searches.keyword) = ?', [strtolower($keyword)])
            ->whereRaw('LOWER(searches.location_name) = ?', [strtolower($locationName)])
            ->where('searches.radius', $radius)
            ->select('leads.*')
            ->distinct()
            ->get();

        $userSavedLeadIds = DB::table('user_saved_leads')
            ->where('user_id', $user->id)
            ->pluck('lead_id')
            ->toArray();

        if ($existingLeads->count() >= $maxResults) {
            $leads = $existingLeads->take($maxResults)->map(function ($lead) use ($userSavedLeadIds) {
                $lead->is_saved = in_array($lead->id, $userSavedLeadIds);
                return $lead;
            });

            return response()->json([
                'success' => true,
                'source' => 'cache',
                'total' => $existingLeads->count(),
                'leads' => $leads
            ]);
        }

        // 2. Fetch from API if cache doesn't have enough results
        $apiKey = $user->search_api_key;
        if (!$apiKey) {
            return response()->json([
                'error' => 'Search API Key belum diisi. Silakan isi Search API Key Anda di menu profil/pengaturan terlebih dahulu.'
            ], 400);
        }

        // Geocoding using Nominatim (OpenStreetMap)
        try {
            $geoResponse = Http::withHeaders([
                'User-Agent' => 'WAMaps_Laravel_Dashboard/1.0'
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $locationName,
                        'format' => 'json',
                        'limit' => 1
                    ]);

            if (!$geoResponse->successful() || empty($geoResponse->json())) {
                return response()->json(['error' => 'Lokasi tidak ditemukan. Pastikan nama kota/daerah benar.'], 400);
            }

            $geoData = $geoResponse->json()[0];
            $lat = $geoData['lat'];
            $lng = $geoData['lon'];
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal melakukan geocoding: ' . $e->getMessage()], 500);
        }

        // SearchAPI.io call preparation
        $startPage = (int) (floor($existingLeads->count() / 20) + 1);
        $gap = $maxResults - $existingLeads->count();

        $searchApiUrl = "https://www.searchapi.io/api/v1/search";
        $radiusMeters = (int) ($radius * 1000);
        $llParam = "@{$lat},{$lng},{$radiusMeters}m";

        $allNewPlaces = [];
        $currentPage = $startPage;

        // Fetch missing pages from API
        while (count($allNewPlaces) < $gap) {
            try {
                $apiResponse = Http::timeout(30)->get($searchApiUrl, [
                    'engine' => 'google_maps',
                    'q' => $keyword,
                    'll' => $llParam,
                    'api_key' => $apiKey,
                    'page' => $currentPage
                ]);

                if (!$apiResponse->successful())
                    break;

                $data = $apiResponse->json();
                $localResults = $data['local_results'] ?? [];
                if (empty($localResults))
                    break;

                foreach ($localResults as $place) {
                    $allNewPlaces[] = $place;
                    if (count($allNewPlaces) >= $gap)
                        break;
                }

                if (count($localResults) < 20)
                    break;
                $currentPage++;
                if ($currentPage > $startPage + 5)
                    break; // Safety cap
            } catch (\Exception $e) {
                break;
            }
        }

        // 3. Create a new search record for this request
        $searchId = DB::table('searches')->insertGetId([
            'user_id' => $user->id,
            'keyword' => $keyword,
            'location_name' => $locationName,
            'radius' => $radius,
            'max_results' => $maxResults,
            'timestamp' => now()
        ]);

        // 4. Process and Save NEW leads and link them to the search
        $finalLeadsList = $existingLeads->toArray();
        $seenPlaceIds = $existingLeads->pluck('google_place_id')->toArray();

        foreach ($allNewPlaces as $place) {
            $googlePlaceId = $place['data_id'] ?? $place['place_id'] ?? null;
            if (!$googlePlaceId)
                continue;

            // Check if Lead already exists in the leads table
            $lead = DB::table('leads')->where('google_place_id', $googlePlaceId)->first();

            if (!$lead) {
                $leadId = DB::table('leads')->insertGetId([
                    'google_place_id' => $googlePlaceId,
                    'name' => $place['title'] ?? 'Unknown',
                    'address' => $place['address'] ?? 'N/A',
                    'phone' => $place['phone'] ?? null,
                    'website' => $place['website'] ?? null,
                    'rating' => $place['rating'] ?? null,
                    'category' => $place['type'] ?? ($place['types'][0] ?? null)
                ]);
                $lead = DB::table('leads')->where('id', $leadId)->first();
            }

            // Link Lead to this specific Search in the pivot table
            DB::table('search_leads')->insertOrIgnore([
                'search_id' => $searchId,
                'lead_id' => $lead->id
            ]);

            if (!in_array($googlePlaceId, $seenPlaceIds)) {
                $finalLeadsList[] = $lead;
                $seenPlaceIds[] = $googlePlaceId;
            }
        }

        // Also link existing cached leads to this new search record for history
        foreach ($existingLeads as $existingLead) {
            DB::table('search_leads')->insertOrIgnore([
                'search_id' => $searchId,
                'lead_id' => $existingLead->id
            ]);
        }

        // Add is_saved status to each lead for the UI
        $finalLeadsList = collect($finalLeadsList)->map(function ($lead) use ($userSavedLeadIds) {
            $lead->is_saved = in_array($lead->id, $userSavedLeadIds);
            return $lead;
        });

        return response()->json([
            'success' => true,
            'source' => 'api',
            'total' => count($finalLeadsList),
            'leads' => $finalLeadsList->take($maxResults)
        ]);
    }

    /**
     * Display the database leads page or return JSON for AJAX requests using Yajra DataTables.
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $leads = DB::table('leads')
                ->join('user_saved_leads', 'leads.id', '=', 'user_saved_leads.lead_id')
                ->where('user_saved_leads.user_id', Auth::id())
                ->select('leads.*', 'user_saved_leads.category as category');

            return \Yajra\DataTables\Facades\DataTables::of($leads)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="form-check-input lead-checkbox" value="' . $row->id . '">';
                })
                ->addColumn('name_html', function ($row) {
                    return '<span class="business-name">' . e($row->name) . '</span>';
                })
                ->addColumn('address_html', function ($row) {
                    return '<span class="text-wrap-custom d-inline-block" title="' . e($row->address) . '">' . e($row->address) . '</span>';
                })
                ->addColumn('contact_html', function ($row) {
                    $phoneHtml = $row->phone ? '
                        <div class="d-flex align-items-center gap-1 mb-1">
                            <i class="bx bx-phone bx-xs text-primary"></i>
                            <span class="small">' . e($row->phone) . '</span>
                        </div>' : '';

                    $websiteHtml = $row->website ? '
                        <div class="d-flex align-items-center gap-1">
                            <i class="bx bx-globe bx-xs text-info"></i>
                            <a href="' . e($row->website) . '" target="_blank" class="small text-info text-decoration-underline">Website</a>
                        </div>' : '';

                    if (!$phoneHtml && !$websiteHtml)
                        return '<span class="text-muted small">-</span>';
                    return '<div class="d-flex flex-column gap-1">' . $phoneHtml . $websiteHtml . '</div>';
                })
                ->addColumn('rating_html', function ($row) {
                    return '<div class="rating-badge"><i class="bx bxs-star"></i> ' . ($row->rating ?? '0') . '</div>';
                })
                ->addColumn('category_html', function ($row) {
                    return '<span class="badge bg-label-primary rounded-pill" style="font-size: 10px;">' . e($row->category ?? 'N/A') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $query = urlencode($row->name . ' ' . $row->address);
                    return '
                        <div class="action-btns">
                            <a href="https://www.google.com/maps/search/?api=1&query=' . $query . '" 
                               target="_blank" class="btn btn-sm btn-icon btn-label-secondary" title="Buka di Maps">
                                <i class="bx bx-link-external"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger" onclick="deleteLead(' . $row->id . ')" title="Hapus Lead">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>';
                })
                ->rawColumns(['checkbox', 'name_html', 'address_html', 'contact_html', 'rating_html', 'category_html', 'action'])
                ->make(true);
        }

        return view('leads.data');
    }

    /**
     * Save a single lead to user's saved leads.
     */
    public function save(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|integer',
            'category' => 'nullable|string'
        ]);

        $userId = Auth::id();
        $leadId = $request->lead_id;
        $category = $request->category ?? 'General';

        // Check if lead exists
        $lead = DB::table('leads')->where('id', $leadId)->exists();
        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        // Check if already saved
        $exists = DB::table('user_saved_leads')
            ->where('user_id', $userId)
            ->where('lead_id', $leadId)
            ->first();

        if ($exists) {
            DB::table('user_saved_leads')
                ->where('user_id', $userId)
                ->where('lead_id', $leadId)
                ->update([
                    'category' => $category,
                    'timestamp' => now()
                ]);
        } else {
            DB::table('user_saved_leads')->insert([
                'user_id' => $userId,
                'lead_id' => $leadId,
                'category' => $category,
                'timestamp' => now()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Lead saved successfully']);
    }

    /**
     * Save multiple leads at once.
     */
    public function saveBatch(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'category' => 'nullable|string'
        ]);

        $userId = Auth::id();
        $leadIds = $request->lead_ids;
        $category = $request->category ?? 'General';
        $count = 0;

        foreach ($leadIds as $leadId) {
            $exists = DB::table('user_saved_leads')
                ->where('user_id', $userId)
                ->where('lead_id', $leadId)
                ->exists();

            if (!$exists) {
                DB::table('user_saved_leads')->insert([
                    'user_id' => $userId,
                    'lead_id' => $leadId,
                    'category' => $category,
                    'timestamp' => now()
                ]);
                $count++;
            } else {
                DB::table('user_saved_leads')
                    ->where('user_id', $userId)
                    ->where('lead_id', $leadId)
                    ->update([
                        'category' => $category,
                        'timestamp' => now()
                    ]);
            }
        }

        return response()->json(['success' => true, 'message' => "$count new leads saved successfully"]);
    }

    /**
     * Delete a lead from the database.
     */
    public function destroy($id)
    {
        $deleted = DB::table('user_saved_leads')
            ->where('user_id', Auth::id())
            ->where('lead_id', $id)
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Lead deleted successfully']);
    }

    /**
     * Delete multiple leads from the database.
     */
    public function destroyBatch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $deleted = DB::table('user_saved_leads')
            ->where('user_id', Auth::id())
            ->whereIn('lead_id', $request->ids)
            ->delete();

        return response()->json(['success' => true, 'message' => "$deleted leads deleted successfully"]);
    }

    /**
     * Get all leads for export.
     */
    public function export()
    {
        $query = DB::table('leads')
            ->join('user_saved_leads', 'leads.id', '=', 'user_saved_leads.lead_id')
            ->where('user_saved_leads.user_id', Auth::id())
            ->select('leads.*', 'user_saved_leads.category as category');

        try {
            // Try to sort by timestamp if it exists
            $leads = $query->orderBy('user_saved_leads.timestamp', 'desc')->get();
        } catch (\Exception $e) {
            // Fallback to ID if timestamp column doesn't exist
            $leads = $query->orderBy('leads.id', 'desc')->get();
        }

        return response()->json($leads);
    }
}
