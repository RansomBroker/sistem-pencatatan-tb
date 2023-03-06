@extends('master')
@section('title', 'Advance Receive')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

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
                        <div class="col-lg-12 col-12 row mt-3">
                            <button type="submit" class="btn btn-info btn-submit col-lg-2 col-12 me-1 align-self-end"><i class='bx bx-search' ></i> Cari</button>
                            <button type="reset" class="btn btn-danger btn-reset col-lg-2 col-12 align-self-end"><i class='bx bx-reset'></i> Reset Filter</button>
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
    <script>
        $(document).ready(function () {
                /* inisialisasi data columns */
                $.ajax({
                    url: "{{ URL::to('/advance-receive/get-column') }}",
                    method: 'GET',
                    success: function(result) {

                        /* Inisialiasi tabel */
                        let advanceReceiveTable = $("#advance-receive-table").DataTable({
                            bProcessing: true,
                            bServerSide: true,
                            ajax: {
                                url: "{{ URL::to('/advance-receive/data-get') }}",
                                type: 'POST',
                                headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                                dataSrc: function (data) {
                                    /* Menampilkan data report */
                                    $(".report-tr").empty();
                                    $(".report-tr").append(`
                                        <td>${data.report[0].sales}</td>
                                        <td>${data.report[0].advanceReceive}</td>
                                        <td>${data.report[0].consumption}</td>
                                        <td>${data.report[0].expired}</td>
                                        <td>${data.report[0].refund}</td>
                                        <td>${data.report[0].outstanding}</td>
                                    `)
                                        return data.data
                                    }
                            },
                            language: {
                                processing: `<div class="spinner-border text-secondary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                             </div>`,
                            },
                            columns: result.columns,
                            columnDefs: [
                                {
                                    target: 0,
                                    searchable: false,
                                    orderable: false,
                                    render: function (data, type, full, meta){
                                        return `
                                            <a href="{{ URL::to('advance-receive/advance-receive-edit') }}/${full.id}" class="btn btn-success"><i class='bx bxs-edit'></i> Edit</a>
                                            <button class="btn-delete btn btn-danger" data-advance="${ full.id}}">Delete</buton>
                                        `;
                                    }
                                },
                                {
                                    targets: '_all',
                                    orderable:false
                                }
                            ]
                        })

                        $("#advance-receive-table_filter").hide()

                        $(document).on('click', '.btn-delete', function(e) {

                        });

                        /* filter data */
                        $('.btn-submit').on('click', function (e) {
                            e.preventDefault()
                            let idFilter = $("[name=id]").val();
                            let nameFilter = $("[name=name]").val();
                            let branchFilter = $("[name=branch]").val();
                            /* period buy_date*/
                            let startBuyDate = $("[name=buy-date-start]").val();
                            let endBuyDate = $("[name=buy-date-end]").val();

                            let buyDateFilter = "";
                            let expiredDateFilter = "";
                            let startDate = "1980-01-01";
                            let endDate = "2999-01-01";


                            /* search filter */
                            if (startBuyDate.length > 0 || endBuyDate.length > 0  ) {
                                if (startBuyDate.length < 1 ) {
                                    buyDateFilter = startDate + "||" + endBuyDate
                                }

                                if (endBuyDate.length < 1 ) {
                                    buyDateFilter = startBuyDate + "||" + endDate
                                }

                                if (startBuyDate.length < 1 && endBuyDate.length < 1) {
                                    buyDateFilter = startDate + "||" + endDate
                                }

                                if (startBuyDate.length > 0 && endBuyDate.length > 0) {
                                    buyDateFilter = startBuyDate + "||" + endBuyDate
                                }
                                advanceReceiveTable.columns(2).search(buyDateFilter).draw();
                            }

                            if (idFilter.length > 0 ) {
                                advanceReceiveTable.columns(4).search(idFilter).draw();
                            }

                            if (nameFilter.length > 0) {
                                advanceReceiveTable.columns(5).search(nameFilter).draw();
                            }

                            if (branchFilter.length > 0) {
                                advanceReceiveTable.columns(1).search(branchFilter).draw();
                            }

                        })

                        $('.btn-reset').on('click', function (e) {
                            e.preventDefault();
                            $("#filter-form")[0].reset();
                            advanceReceiveTable.columns().search('').clear().draw();
                        })
                    }
                })

            }
        )
    </script>
@endsection
