@extends('master')
@section('title', 'Advance Receive')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf
        <input type="hidden" name="role" value="{{ \Illuminate\Support\Facades\Auth::user()->role }}">

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Advance Receive</h2>
        </div>

        {{-- card --}}
        <div class="card card-body row d-flex flex-column flex-wrap">

            @if($message = Session::get('message'))
                @if($status = Session::get('status'))
                    <div class="alert alert-{{ $status}} alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endif

            <a href="{{ URL::to('advance-receive/advance-receive-add') }}" class="col-12 col-lg-3 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add New Advance Receive</a>
            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Advance Receive</h3>
                <h5 class="d-inline align-middle"> Filter Berdasarkan:</h5>
                <form class="mb-3" id="filter-form">
                    <div class="row g-2">
                        <div class="col-lg-4 col-12">
                            <label class="form-label">ID Customer</label>
                            <input class="form-control" name="id" placeholder="Ketikan ID customer">
                        </div>
                        <div class="col-lg-4 col-12">
                            <label class="form-label">Nama</label>
                            <input class="form-control" name="name" placeholder="Ketikan Customer">
                        </div>
                        <div class="col-lg-4 col-12">
                            <label class="form-label">Cabang</label>
                            <select class="form-select" name="branch">
                                    <option value="">--- Pilih Cabang ---</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->name }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-12  row p-0 ms-1 mt-3">
                            <label class="form-label p-0 fw-bold">Tanggal Penjualan</label>
                            <div class="col-lg-6 col-12 p-0">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" name="buy-date-start">
                            </div>
                            <div class="col-lg-6 col-12 p-0">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="buy-date-end">
                            </div>
                        </div>
                        <div class="col-lg-12 col-12 row mt-3 g-2">
                            <button type="submit" class="btn btn-info btn-submit col-lg-2 col-12 me-1 align-self-end"><i class='bx bx-search' ></i> Cari</button>
                            <button type="reset" class="btn btn-danger btn-reset col-lg-2 col-12 align-self-end"><i class='bx bx-reset'></i> Reset Filter</button>
                            <button type="submit" class="btn-export btn btn-success col-lg-2 col-12 mx-1 align-self-end" data-type="excel"><i class='bx bx-spreadsheet'></i> Export As Excel File</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- table --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Data Advance Receive </h3>
                <div class="mb-3 table-responsive mt-2">
                    <table class="table table-striped table-hover text-nowrap w-100">
                        <thead>
                            <tr>
                                <th>Sales</th>
                                <th>Net.Sales(Adv.Receive)</th>
                                <th>Consumption</th>
                                <th>Expired</th>
                                <th>Refund</th>
                                <th>Outstanding</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="report-tr">

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-nowrap w-100" id="advance-receive-table">
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom-js')
    <script src="{{ asset('public/assets/js/main/advance_receive.js') }}"></script>
@endsection
