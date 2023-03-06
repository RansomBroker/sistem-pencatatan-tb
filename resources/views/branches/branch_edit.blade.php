@extends('master')
@section('title', 'Edit Branch Baru')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Tambah Branch Baru</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            <form method="POST" action="{{ URL::to('branch/branch-edit/edit') }}">
                {{-- csrf --}}
                @csrf
                {{-- id --}}
                <input type="hidden" name="id" value="{{$branch->id}}">
                <div class="row">
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Name <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Ketikan nama " name="name" value="{{ $branch->name }}"/>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Branch <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('branch') is-invalid @enderror" placeholder="Ketikan branch" name="branch" value="{{ $branch->branch }}"/>
                        @error('branch')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Address <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" placeholder="Ketikan Alamat" name="address" value="{{ $branch->address }}"/>
                        @error('address')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">No.telp <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('tel') is-invalid @enderror" placeholder="Ketikan Nomor telephone" name="tel" value="{{ $branch->telephone }}"/>
                        @error('tel')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">NPWP <sup class="text-danger">(Boleh Kosong)</sup></label>
                        <input type="text" class="form-control @error('npwp') is-invalid @enderror" placeholder="Ketikan NPWP" name="npwp" value="{{ $branch->npwp }}"/>
                        @error('npwp')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Company <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('company') is-invalid @enderror" placeholder="Ketikan company" name="company" value="{{ $branch->company }}"/>
                        @error('company')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <button type="submit" class="btn btn-secondary w-100">Edit Branch</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

