@extends('master')
@section('title', 'Tambah Category Baru')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Tambah Category Baru</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            <form method="POST" action="{{ URL::to('category/category-add/add') }}">
                {{-- csrf --}}
                @csrf
                <div class="row">
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Name <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Ketikan nama " name="name" value="{{ old('name') }}"/>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 row">
                        <button type="submit" class="btn btn-secondary col-12 col-lg-12 mb-3">Tambah Category Baru</button>
                        <button type="reset" class="btn btn-danger col-lg-12 col-12">Clear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

