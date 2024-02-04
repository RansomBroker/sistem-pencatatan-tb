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
                                <h4>Langkah 2 (Connecting Database Server)</h4>
                                @if(Session::has('error'))
                                    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                        <div>
                                            {{ Session::get('error') }}
                                        </div>
                                    </div>
                                @endif

                                <form action="{{ route('install.step.two.process') }}" method="POST">
                                    @csrf
                                    {{-- db host ip --}}
                                    <div class="form-group mb-3">
                                        <label for="DBhost" class="form-label">DB Host <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="DBhostname" name="db_host" required value="localhost">
                                    </div>
                                    {{-- driver select --}}
                                    <div class="form-group mb-3">
                                        <label for="dbConnection" class="form-label">DB Connection <span class="text-danger">*</span></label>
                                        <select name="db_connection" id="dbConnection" class="form-control" required>
                                            <option value="">--- Pilih Driver Database ---</option>
                                            <option value="pgsql" selected>PgSQL</option>
                                        </select>
                                    </div>
                                    {{-- port --}}
                                    <div class="form-group mb-3">
                                        <label for="DBPort" class="form-label">DB Port <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="DBPort" name="db_port" value="5432" required>
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
