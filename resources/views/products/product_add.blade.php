@extends('master')
@section('title', 'Tambah Product Baru')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Tambah Product Baru</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            <form method="POST" action="{{ URL::to('product/product-add/add') }}">
                {{-- csrf --}}
                @csrf
                <div class="row">
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">ID Product <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('product-id') is-invalid @enderror" placeholder="Ketikan nama " name="product-id" value="{{ old('product-id') }}"/>
                        @error('product-id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Category <sup class="text-danger">(Required)</sup></label>
                        <select class="form-select @error('category') is-invalid @enderror" name="category" >
                            @if(old('category') == null)
                                <option value="" selected>---- Select Category ----</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            @else
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @if(old('category') == $category->id) selected @endif>{{ $category->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('category')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
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
                        <button type="submit" class="btn btn-secondary col-12 col-lg-12 mb-3">Tambah Product Baru</button>
                        <button type="reset" class="btn btn-danger col-12 col-lg-12"> reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
