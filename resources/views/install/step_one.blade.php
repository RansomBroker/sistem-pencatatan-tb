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
                                <h4>Langkah 1</h4>
                                @if(Session::has('success'))
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                        <div>
                                            {{ Session::get('success') }}
                                        </div>
                                    </div>
                                @endif
                                @error('check')
                                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                    <div>
                                        {{ $message }}
                                    </div>
                                </div>
                                @enderror
                                <ul>
                                    <li>rename file <code>.env.example</code> menjadi <code>.env</code></li>
                                </ul>

                                <div class="alert alert-primary d-flex align-items-center" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                                    <div class="fw-bold">
                                        Silahkan skip step ini dengan men-check checkbox di bawah lalu klik tombol next.
                                    </div>
                                </div>
                                <form action="{{ route('install.step.one.process') }}" method="POST">
                                    @csrf
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" name="check">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Saya telah mengikuti step 1.
                                        </label>
                                    </div>
                                    <button class="btn btn-primary w-100">Next</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
