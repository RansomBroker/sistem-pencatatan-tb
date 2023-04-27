@extends('master')
@section('title', 'Edit User')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Edit User</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            <form method="POST" action="{{ URL::to('user/user-edit/edit') }}">
                {{-- csrf --}}
                @csrf
                <input type="hidden" name="id" value="{{ $user['id'] }}">
                <div class="row">
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Username <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" placeholder="Ketikan username" name="username" value="{{ $user['name'] }}"/>
                        @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Hak Akses (Role) <sup class="text-danger">(Required)</sup></label>
                        <select name="role" class="form-control @error('role') is-invalid @enderror">
                            <option value="0" @if($user['role'] == 0) selected @endif>Admin</option>
                            <option value="1" @if($user['role'] == 1) selected @endif>Users</option>
                        </select>
                        @error('role')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Password (Biarkan kosong jika tidak merubah password)</label>
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

