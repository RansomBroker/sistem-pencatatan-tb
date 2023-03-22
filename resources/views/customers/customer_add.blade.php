@extends('master')
@section('title', 'Tambah Customer Baru')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Tambah Customer Baru</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            @if($message = Session::get('message'))
                @if($status = Session::get('status'))
                    <div class="alert alert-{{ $status}} alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endif
            <form method="POST" action="{{ URL::to('customer/customer-add/add') }}">
                {{-- csrf --}}
                @csrf
                <div class="row">
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Customer ID <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('customer-id') is-invalid @enderror" placeholder="Ketikan ID Customer " name="customer-id" value="{{ old('customer-id') }}"/>
                        @error('customer-id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Nama <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Ketikan Nama" name="name" value="{{ old('name') }}"/>
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Nickname <sup class="text-danger">(Optional)</sup></label>
                        <input type="text" class="form-control @error('nickname') is-invalid @enderror" placeholder="Ketikan Nickname" name="nickname" value="{{ old('nickname') }}"/>
                        @error('nickname')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">No.telp <sup class="text-danger">(Optional)</sup></label>
                        <input type="text" class="form-control @error('tel') is-invalid @enderror" placeholder="Ketikan Nomor telephone" name="tel" value="{{ old('tel') }}"/>
                        @error('tel')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">No.KTP <sup class="text-danger">(Optional)</sup></label>
                        <input type="text" class="form-control @error('identity-number') is-invalid @enderror" placeholder="Ketikan No KTP" name="identity-number" value="{{ old('identity-number') }}"/>
                        @error('identity-number')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Tanggal Lahir <sup class="text-danger">(Optional)</sup></label>
                        <input type="date" class="form-control @error('birth-date') is-invalid @enderror" placeholder="Pilih Tanggal" name="birth-date" value="{{ old('birth-date') }}"/>
                        @error('birth-date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Alamat <sup class="text-danger">(Optional)</sup></label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" placeholder="Ketikan Alamat" name="address" value="{{ old('address') }}"/>
                        @error('address')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Email <sup class="text-danger">(Optional)</sup></label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" placeholder="Ketikan Email" name="email" value="{{ old('email') }}"/>
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">No. Rekening <sup class="text-danger">(Optional)</sup></label>
                        <input type="text" class="form-control @error('payment-number') is-invalid @enderror" placeholder="Ketikan no rekening" name="payment-number" value="{{ old('payment-number') }}"/>
                        @error('payment-number')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <button type="submit" class="btn btn-secondary w-100 mb-3">Tambah Customer Baru</button>
                        <button type="reset" class="btn btn-danger w-100">Clear</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
