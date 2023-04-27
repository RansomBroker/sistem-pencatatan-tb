@extends('master')
@section('title', 'Consumption')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf
        <input type="hidden" name="role" value="{{ \Illuminate\Support\Facades\Auth::user()->role }}">

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Consumption</h2>
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

            <a href="{{ URL::to('consumption/consumption-add') }}" class="col-12 col-lg-3 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add Consumption</a>
            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Consumption</h3>
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
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-12  row p-0 ms-1 mt-3">
                            <label class="form-label p-0 fw-bold">Tanggal Consumption</label>
                            <div class="col-lg-6 col-12 p-0">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" name="consumption-date-start">
                            </div>
                            <div class="col-lg-6 col-12 p-0">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="consumption-date-end">
                            </div>
                        </div>
                        <div class="col-lg-12 col-12 row mt-3">
                            <button type="submit" class="btn btn-info btn-submit col-lg-2 col-12 me-1 align-self-end"><i class='bx bx-search' ></i> Cari</button>
                            <button type="reset" class="btn btn-danger btn-reset col-lg-2 col-12 align-self-end"><i class='bx bx-reset'></i> Reset Filter</button>
                            <button type="submit" class="btn-export btn btn-success col-lg-2 col-12 mx-1 align-self-end" data-type="excel"><i class='bx bx-spreadsheet'></i> Export As Excel File</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- table --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Perhitungan Data Consumption </h3>
                <div class="mb-3 table-responsive mt-2">
                    <table class="table table-striped table-hover text-nowrap w-100">
                        <thead>
                        <tr>
                            <th>QTY Total Consumption Advance Receive</th>
                            <th>IDR Total Consumption Advance Receive</th>
                            <th>QTY Total Sisa Advance Receive</th>
                            <th>QTY Total Sisa Advance Receive</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="report-tr">
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover w-100 text-nowrap" id="consumption-table">
                        <thead>
                            <tr></tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom-js')
    <script>
        $(document).ready(function () {
            const column = [
                {
                    'data' : 'action',
                    'title' : 'Action'
                },
                {
                    'data' : 'branch',
                    'title' : 'Cabang penjualan'
                },
                {
                    'data' : 'buy_date',
                    'title' : 'Tanggal Penjualan'
                },
                {
                    'data' : 'expired_date',
                    'title' : 'Expired Date'
                },
                {
                    'data' : 'customer_id',
                    'title' : 'ID Customer'
                },
                {
                    'data' : 'customer_name',
                    'title' : 'Nama Customer'
                },
                {
                    'data' : 'type',
                    'title' : 'Tipe'
                },
                {
                    'data' : 'buy_price',
                    'title' : 'IDR Harga Beli'
                },
                {
                    'data' : 'net_sales',
                    'title' : 'IDR Net Sales'
                },
                {
                    'data' : 'tax',
                    'title' : 'IDR PPN'
                },
                {
                    'data' : 'payment',
                    'title' : 'Pembayaran'
                },
                {
                    'data' : 'qty',
                    'title' : 'Qty Produk'
                },
                {
                    'data' : 'unit_price',
                    'title' : 'IDR Harga Satuan'
                },
                {
                    'data' : 'product',
                    'title' : 'Produk'
                },
                {
                    'data' : 'memo',
                    'title' : 'Memo/Model'
                },
                {
                    'data' : 'category',
                    'title' : 'Kategory Produk'
                },
                {
                    'data' : 'notes',
                    'title' : 'Notes'
                },
                {
                    'data' : 'consumption-date-1',
                    'title' : 'Tanggal Pemakaian Ke-1'
                },
                {
                    'data' : 'consumption-branch-1',
                    'title' : 'Tempat Pemakaian Ke-1'
                },
                {
                    'data' : 'consumption-date-2',
                    'title' : 'Tanggal Pemakaian Ke-2'
                },
                {
                    'data' : 'consumption-branch-2',
                    'title' : 'Tempat Pemakaian Ke-2'
                },
                {
                    'data' : 'consumption-date-3',
                    'title' : 'Tanggal Pemakaian Ke-3'
                },
                {
                    'data' : 'consumption-branch-3',
                    'title' : 'Tempat Pemakaian Ke-3'
                },
                {
                    'data' : 'consumption-date-4',
                    'title' : 'Tanggal Pemakaian Ke-4'
                },
                {
                    'data' : 'consumption-branch-4',
                    'title' : 'Tempat Pemakaian Ke-4'
                },
                {
                    'data' : 'consumption-date-5',
                    'title' : 'Tanggal Pemakaian Ke-5'
                },
                {
                    'data' : 'consumption-branch-5',
                    'title' : 'Tempat Pemakaian Ke-5'
                },
                {
                    'data' : 'consumption-date-6',
                    'title' : 'Tanggal Pemakaian Ke-6'
                },
                {
                    'data' : 'consumption-branch-6',
                    'title' : 'Tempat Pemakaian Ke-6'
                },
                {
                    'data' : 'consumption-date-7',
                    'title' : 'Tanggal Pemakaian Ke-7'
                },
                {
                    'data' : 'consumption-branch-7',
                    'title' : 'Tempat Pemakaian Ke-7'
                },
                {
                    'data' : 'consumption-date-8',
                    'title' : 'Tanggal Pemakaian Ke-8'
                },
                {
                    'data' : 'consumption-branch-8',
                    'title' : 'Tempat Pemakaian Ke-8'
                },
                {
                    'data' : 'consumption-date-9',
                    'title' : 'Tanggal Pemakaian Ke-9'
                },
                {
                    'data' : 'consumption-branch-9',
                    'title' : 'Tempat Pemakaian Ke-9'
                },
                {
                    'data' : 'consumption-date-10',
                    'title' : 'Tanggal Pemakaian Ke-10'
                },
                {
                    'data' : 'consumption-branch-10',
                    'title' : 'Tempat Pemakaian Ke-10'
                },
                {
                    'data' : 'consumption-date-11',
                    'title' : 'Tanggal Pemakaian Ke-11'
                },
                {
                    'data' : 'consumption-branch-11',
                    'title' : 'Tempat Pemakaian Ke-11'
                },
                {
                    'data' : 'consumption-date-12',
                    'title' : 'Tanggal Pemakaian Ke-12'
                },
                {
                    'data' : 'consumption-branch-12',
                    'title' : 'Tempat Pemakaian Ke-12'
                },
                {
                    'data' : 'qty_total',
                    'title' : 'QTY Total Consumption Advance Receive'
                },
                {
                    'data' : 'idr_total',
                    'title' : 'IDR Total Consumption Advance Receive'
                },
                {
                    'data' : 'qty_expired',
                    'title' : 'QTY Expired Advance Receive'
                },
                {
                    'data' : 'qty_refund',
                    'title' : 'QTY Refund Advance Receive'
                },
                {
                    'data' : 'qty_remains',
                    'title' : 'QTY Total Sisa Advance Receive'
                },
                {
                    'data' : 'idr_remains',
                    'title' : 'IDR Total Sisa Advance Receive'
                },
            ]

                let consumptionTable = $("#consumption-table").DataTable({
                    bProcessing: true,
                    bServerSide: true,
                    ajax: {
                        url: "{{ URL::to('consumption/data-get') }}",
                        headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                        type: 'POST',
                        dataSrc: function (data) {
                            console.log(data)
                            $(".report-tr").empty();
                            $(".report-tr").append(`
                                            <td>${data.report[0].qtyTotal}</td>
                                            <td>${data.report[0].idrTotal}</td>
                                            <td>${data.report[0].qtyRemains}</td>
                                            <td>${data.report[0].idrRemains}</td>
                                        `)
                            return data.data
                        }
                    },
                    language: {
                        processing: `<div class="spinner-border text-secondary" role="status">
                            <span class="visually-hidden">Loading...</span>
                            </div>`,
                    },
                    columnDefs: [
                        {
                            target: 0,
                            searchable: false,
                            orderable: false,
                            render: function (data, type, full, meta){
                                return `<a href="{{ URL::to('consumption/consumption-edit') }}/${full.id}" class="btn btn-success"><i class='bx bxs-edit'></i> Edit</a>`;
                            }
                        }
                    ],
                    columns: column,
                    initComplete: function () {
                        let table = this.api();
                        let role = parseInt($('[name=role]').val())

                        if (role === 1) {
                            table.column(0).visible(false)
                        }

                    },
                })

                $("#consumption-table_filter").hide()

                /* filter data */
                $('.btn-submit').on('click', function (e) {
                    e.preventDefault()
                    let idFilter = $("[name=id]").val();
                    let nameFilter = $("[name=name]").val();
                    let branchFilter = $("[name=branch]").val();
                    /* period buy_date*/
                    let startConsumptionDate = $("[name=consumption-date-start]").val();
                    let endConsumptionDate = $("[name=consumption-date-end]").val();

                    let consumptionDateFilter = "";
                    let startDate = "1980-01-01";
                    let endDate = "2999-01-01";


                    if (idFilter.length > 0 ) {
                        consumptionTable.columns(4).search(idFilter).draw();
                    }

                    if (nameFilter.length > 0) {
                        consumptionTable.columns(5).search(nameFilter).draw();
                    }

                    if (branchFilter.length > 0) {
                        consumptionTable.columns(1).search(branchFilter).draw();
                    }

                    /* search filter */
                    if (startConsumptionDate.length > 0 || endConsumptionDate.length > 0  ) {
                        if (startConsumptionDate.length < 1 ) {
                            consumptionDateFilter = startDate + "||" + endConsumptionDate
                        }

                        if (endConsumptionDate.length < 1 ) {
                            consumptionDateFilter = startConsumptionDate+ "||" + endDate
                        }

                        if (startConsumptionDate.length < 1 && endConsumptionDate.length < 1) {
                            consumptionDateFilter = startDate + "||" + endDate
                        }

                        if (startConsumptionDate.length > 0 && endConsumptionDate.length > 0) {
                            consumptionDateFilter = startConsumptionDate + "||" + endConsumptionDate
                        }
                        consumptionTable.columns(2).search(consumptionDateFilter).draw();
                    }

                })

                /* reset filter */
                $('.btn-reset').on('click', function (e) {
                    e.preventDefault();
                    $("#filter-form")[0].reset();
                    consumptionTable.columns().search('').clear().draw();
                })

                /* export excel */
                $('.btn-export').on('click', function(e) {
                e.preventDefault();
                let idFilter = $("[name=id]").val();
                let nameFilter = $("[name=name]").val();
                let branchFilter = $("[name=branch]").val();
                /* period buy_date*/
                let startConsumptionDate = $("[name=consumption-date-start]").val();
                let endConsumptionDate = $("[name=consumption-date-end]").val();

                if (startConsumptionDate.length === 0 || endConsumptionDate.length === 0) {
                    Swal.fire(
                        'Error proses export',
                        'Rentang tanggal maksimal 3 Tahun transaksi',
                        'error'
                    )
                    return 0;
                }

                if (startConsumptionDate.length > 0 || endConsumptionDate.length > 0) {
                    let dateStart = new Date(startConsumptionDate);
                    let dateEnd = new Date(endConsumptionDate);
                    let diff = dateEnd.getTime() - dateStart.getTime();
                    let totalDays = Math.round(diff / (1000 * 3600 * 24));

                    if (totalDays > 1095 ) {
                        Swal.fire(
                            'Error proses export',
                            'Rentang tanggal maksimal 3 Tahun transaksi',
                            'error'
                        )
                        return 0;
                    }

                    $.ajax({
                        url: "{{ URL::to('consumption/consumption-export/excel') }}",
                        headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            'id-filter': idFilter,
                            'name-filter': nameFilter,
                            'branch-filter': branchFilter,
                            'start-consumption-date' : startConsumptionDate,
                            'end-consumption-date': endConsumptionDate
                        },
                        beforeSend: function () {
                            Swal.fire({
                                html: `
                                            <div class="d-flex justify-content-center fs-4 ">
                                                  <span class="spinner-border spinner-border-sm text-primary fs-4" role="status" aria-hidden="true"></span>
                                                    Loading...
                                            </div>
                                        `,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            })
                        },
                        success: function(data)
                        {
                            if (data.status === "success") {
                                Swal.fire({
                                    html: `
                                            <div class="d-flex justify-content-center fs-4 ">
                                                  <span class="spinner-border spinner-border-sm text-primary fs-4" role="status" aria-hidden="true"></span>
                                                    Silahkan Tunggu beberapa saat, mohon untuk tidak refresh halaman ini sampai proses selesai
                                            </div>
                                        `,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                })

                                // check status every sec
                                let exportExcel = setInterval(function () {
                                    $.ajax({
                                        async:false,
                                        url: "{{ URL::to('consumption/consumption-export/check') }}" +"/" + data.batchID + "/" + data.name,
                                        method: 'GET',
                                        success: function (response) {
                                            if (response.status === "success") {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Export Success',
                                                    confirmButtonText: 'Download',
                                                    allowOutsideClick: false
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.open(response.exportURL, '_blank');
                                                    }
                                                })
                                                clearInterval(exportExcel)
                                            }else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Export failed',
                                                    confirmButtonText: 'cancel',
                                                })
                                                clearInterval(exportExcel)
                                            }
                                        }
                                    })
                                }, 1500);

                                clearInterval();
                            }
                        }
                    })
                }



            })

            }
        )
    </script>
@endsection
