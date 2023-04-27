@extends('master')
@section('title', 'Tambah User')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Tambah User Baru</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            <form method="POST" action="{{ URL::to('user/user-add/add') }}">
                {{-- csrf --}}
                @csrf
                <div class="row">
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Username <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" placeholder="Ketikan username" name="username" value="{{ old('username') }}"/>
                        @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Hak Akses (Role) <sup class="text-danger">(Required)</sup></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror">
                            @if(old('role') == null)
                                <option value="" selected>-- Pilih Hak Akses (Role) --</option>
                                <option value="0">Admin</option>
                                <option value="1">Users</option>
                            @else
                                <option value="0" @if(old('role') == 0) selected @endif>Admin</option>
                                <option value="1" @if(old('role') == 1) selected @endif>Users</option>
                            @endif
                        </select>
                        @error('role')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Password <sup class="text-danger">(Required)</sup></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Ketikan password" name="password"/>
                        @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="mb-3 col-12 col-lg-12">
                        <button type="submit" class="btn btn-secondary w-100">Tambah User Baru</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

