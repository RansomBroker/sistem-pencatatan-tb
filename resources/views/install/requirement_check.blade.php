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

                                <div class="border mb-3"></div>

                                <h4>Requirement Check</h4>

                                <p>Note 1:Pastikan versi PHP dan ekstensi telah di-enable</p>

                                <p>Note 2: jika menggunakan xampp folder ekstensi terdapat di <code>xampp/php/php.ini</code></p>

                                <h4>Required Spec</h4>

                                <div class="mb-3">

                                    @if(Session::has('error'))
                                        <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                            <div>
                                                {{ Session::get('error') }}
                                            </div>
                                        </div>
                                    @endif

                                    <ul>
                                        <li><span>PHP Version <span class="fw-bold">{{$requirement['core']['minPhpVersion']}}</span></span></li>
                                        <li>PHP Extention Enbale</li>
                                        <ul>
                                            @if(Session::has('error_list'))
                                                @foreach(Session::get('error_list') as $i => $error)
                                                    <li>{{ $i }} @if(!$error) <svg class="bi flex-shrink-0 me-2 text-danger" width="12" height="12" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                                        @else <svg class="bi flex-shrink-0 me-2 text-success" width="12" height="12" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg> @endif</li>
                                                @endforeach
                                            @else
                                                @foreach($requirement['requirements']['php'] as $ext)
                                                    <li>{{ $ext }}</li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <li>Apache</li>
                                        <ul>
                                            @foreach($requirement['requirements']['apache'] as $ext)
                                                <li>{{ $ext }}</li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>

                                <form action="{{ route('install.requirement.check.process') }}" method="POST">
                                    @csrf
                                    <button class="btn btn-primary w-100 mb-3">Next</button>
                                    <a href="{{ route('install.welcome') }}" class="btn btn-secondary mb-3 w-100">Back</a>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
