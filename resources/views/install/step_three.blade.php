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

                                <h4>Langkah 3 (Configure new database)</h4>

                                <div class="mb-3">
                                    <span>Note 1: untuk nama database harus tanpa spasi jika dipisah maka bisa gunakan simbol '-' atau '_' </span>
                                    <span>contoh penamaan database <code>db_advance_receive</code></span>
                                    <span>contoh penamaan username <code>user_1</code> atau <code>user_db_advance_receive</code>.</span>
                                </div>

                                <div class="mb-3">
                                    <span>Note 2: untuk input <span class="fw-bold">Database Admin Username</span> dan <span class="fw-bold">Database Admin Password</span> merupakan akun pada saat instalasi Database Server</span>
                                </div>

                                @if(Session::has('success'))
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                                        <div>
                                            {{ Session::get('success') }}
                                        </div>
                                    </div>
                                @endif

                                <form action="{{ route('install.step.three.process') }}" method="POST">
                                    @csrf
                                    {{-- db name--}}
                                    <div class="form-group mb-3">
                                        <label for="DBName" class="form-label">New Database Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control  @error('db_name') is-invalid @enderror" id="DBName" name="db_name" placeholder="ex: db_advance_receive" required>
                                        @error('db_name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="DBUsername" class="form-label">Database Admin Username<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control  @error('db_username') is-invalid @enderror" id="DBUsername" name="db_username" placeholder="ex: root" required>
                                        @error('db_username')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="DBPassword" class="form-label">Database Admin Password<span class="text-danger">*</span></label>
                                        <input type="password" class="form-control  @error('db_password') is-invalid @enderror" id="DBPassword" name="db_password" placeholder="ex: root" required>
                                        @error('db_password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
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
