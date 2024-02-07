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
                                <p>Selamat datang di Web Application Installer V.1.0. <span class="fw-bold">Mohon untuk mengikuti instruksi secara seksama</span>.</p>

                                <div class="mb-3">
                                    <p class="text-danger fw-bold">Warning :</p>
                                    <ul>
                                        <li>Ketika anda melanjutkan proses instalasi, maka Application Installer akan membuat database baru.</li>
                                        <li>Perlu diingat untuk membackup data sebelumnya. Karena seluruh data akan hilang.</li>
                                        <li>Perlu diperhatikan untuk tidak menekan tombol back pada browser. kecuali button back yang telah disediakan dalam installasi.</li>
                                        <li>Pastikan anda tidak mengedit file <code>.env</code> yang ada diluar proses instalasi.</li>
                                    </ul>
                                </div>

                                <div class="border mb-3"></div>

                                @error('check')
                                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                    <div>
                                        {{ $message }}
                                    </div>
                                </div>
                                @enderror

                                <form action="{{ route('install.welcome.process') }}" method="POST">
                                    @csrf
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="flexCheckDefault" name="check">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            Saya telah membaca <span class="fw-bold">Warning Note</span> dan setuju dan Saya telah membackup data penting.
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
