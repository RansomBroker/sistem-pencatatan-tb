@extends('master')
@section('title', 'Web App Installer V.1.0')
@section('content')
    <div class="container d-flex flex-column">
        <div class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
                <div class="d-table-cell align-middle">

                    <div class="text-center mt-4">
                        <h1 class="h2">Advance Receive System V.1.0</h1>
                        <p class="lead">
                            Web Application Installer V.1.0
                        </p>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="m-sm-4">
                                <div class="text-center mb-3">
                                    <img src="{{ asset('public/assets/img/photos/tokyo_belle_logo.png') }}" alt="tokyo belle" class="img-fluid" width="128" />
                                </div>

                                <p>Selamat datang di Web Application Installer V.1.0. Halaman ini merupakan tahap pertama dalam instalasi Aplikasi Web anda. <span class="fw-bold">Mohon untuk mengikuti instruksi secara seksama</span>.</p>

                                <div class="border mb-3"></div>

                                <h4>Langkah 4 (Create Admin Account)</h4>

                                <div class="mb-3">
                                    <span>Note : Akun admin berfungsi untuk login pada aplikasi  </span>
                                </div>

                                @if(Session::has('success'))
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                        <div>
                                            {{ Session::get('success') }}
                                        </div>
                                    </div>
                                @endif

                                @if(Session::has('error'))
                                    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                        <div>
                                            {{ Session::get('error') }}
                                        </div>
                                    </div>
                                @endif

                                <form action="{{ route('install.step.four.process') }}" method="POST">
                                    @csrf
                                    <input type="hidden" value="0" name="role">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Username <sup class="text-danger">*</sup></label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" name="name" id="name" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">Password <sup class="text-danger">*</sup></label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required>
                                    </div>
                                    <button class="btn btn-primary w-100 mb-3">Next</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
