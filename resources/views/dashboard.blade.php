@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Selamat datang, {{ Auth::user()->name }}! 🎉</h5>
                            <p class="mb-4">
                                Wah, bengkel <span class="fw-bold">{{ Auth::user()->name }}</span> sudah terdaftar!
                                Selesaikan checklist di bawah ini untuk mulai mengelola bengkel Anda secara digital.
                            </p>



                            <div class="alert alert-info d-flex align-items-center p-3 mt-4" role="alert">
                                <span class="badge badge-center rounded-pill bg-info me-3"><i
                                        class="bx bx-bulb fs-4"></i></span>
                                <div>
                                    💡 <strong>Tips:</strong> Ini akan membantu Anda memahami alur kerja aplikasi secara
                                    langsung dan cepat!
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}"
                                height="140" alt="View Badge User"
                                data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
@endpush