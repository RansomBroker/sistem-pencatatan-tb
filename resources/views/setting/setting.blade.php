@extends('master')
@section('title', 'Setting Aplikasi')
@section('content')
    <div class="container-fluid p-o">

        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Setting Aplikasi</h2>
        </div>

        <div class="card card-body row d-flex flex-column flex-wrap">
            <h3 class="d-inline align-middle"> Import Data Advance Rceive</h3>

            @if($message = Session::get('message'))
                @if($status = Session::get('status'))
                    <div class="alert alert-{{ $status}} alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endif

            <h5 class="d-inline align-middle"> Import Data Advance Receive dari File Excel(XLS/XLSX) </h5>
            <form action="{{ URL::to("setting/import-excel/process") }}" method="POST" enctype="multipart/form-data" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Pilih File Excel (xls/xlsx) <sup class="text-danger">*</sup></label>
                    <input class="form-control @error('excel-file') is-invalid @enderror" type="file"  name="excel-file" required>
                    @error('excel-file')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <button class="btn btn-secondary">Import Data</button>
            </form>
        </div>

    </div>
@endsection
@section('custom-js')
    <script>
        $(document).ready(function () {
        })
    </script>
@endsection
