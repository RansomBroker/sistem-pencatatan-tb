@extends('master')
@section('title', 'Outstanding Advance Receives')
@section('content')
    <div class="container-fluid p-o">

        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Outstanding Advance Receive</h2>
        </div>

        <div class="card card-body row d-flex flex-column flex-wrap">

            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Outstanding</h3>
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
                <h3 class="d-inline align-middle">Perhitungan Data Outstanding Advance Receive </h3>
                <div class="mb-3 table-responsive mt-2">
                    <table class="table table-striped table-hover text-nowrap w-100">
                        <thead>
                        <tr>
                            <th>QTY Total Sisa Advance Receive</th>
                            <th>IDR Total Sisa Advance Receive</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="report-tr">
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-nowrap w-100" id="outstanding-advance-receive-table">
                        <thead>
                        <tr>
                            <th>Cabang Penjualan</th>
                            <th>Tanggal Penjualan</th>
                            <th>Expired Date</th>
                            <th>ID Customer</th>
                            <th>Nama Customer</th>
                            <th>Tipe</th>
                            <th>QTY Produk</th>
                            <th>Produk</th>
                            <th>Kategori Paket</th>
                            <th>NOTES</th>
                            <th>QTY Total Sisa Advance Receive</th>
                            <th>IDR Total Sisa Advance Receive</th>
                        </tr>
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
            let outstandingAdvanceReceive = $('#outstanding-advance-receive-table').DataTable({
                bProcessing: true,
                bServerSide: true,
                ajax: {
                    url: "{{ URL::to('/outstanding/data-get') }}",
                    type: 'GET',
                    dataSrc: function (data) {
                        $(".report-tr").empty();
                        $(".report-tr").append(`
                                        <td>${data.report.qty_remains}</td>
                                        <td>${formatCurrencyPrice(data.report.idr_remains)}</td>
                        `)
                        /* Menampilkan data report */
                        return data.data
                    }
                },
                language: {
                    processing: `<div class="spinner-border text-secondary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                             </div>`,
                },
                columns: [
                    {"data" : "branches[0].name"},
                    {"data" : "buy_date"},
                    {"data" : "expired_date"},
                    {"data" : "customers[0].customer_id"},
                    {"data" : "customers[0].name"},
                    {
                        sortable: false,
                        "render": function(data, type, full, meat) {
                            return full.type.charAt(0).toUpperCase() + full.type.slice(1).toLowerCase();
                        }
                    },
                    {"data" : "qty"},
                    {"data" : "products[0].name"},
                    {"data" : "products[0].categories[0].name"},
                    {"data" : "notes"},
                    {
                        sortable: false,
                        "render": function(data, type, full, meat) {
                            if (full.qty_remains != null) {
                                return full.qty_remains
                            }
                            return "0"
                        }
                    },
                    {
                        sortable: false,
                        "render": function(data, type, full, meat) {
                            if (full.idr_remains != null) {
                                return formatCurrencyPrice(full.idr_remains.split('.')[0])
                            }
                            return "0"
                        }
                    },
                ],
                columnDefs: [
                    {
                        targets: '_all',
                        orderable:false
                    }
                ]
            })

            $('.btn-submit').on('click', function (e) {
                e.preventDefault()
                let idFilter = $("[name=id]").val();
                let nameFilter = $("[name=name]").val();
                let branchFilter = $("[name=branch]").val();
                /* period buy_date*/
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
                    outstandingAdvanceReceive.columns(2).search(buyDateFilter).draw();
                }

                if (idFilter.length > 0 ) {
                    outstandingAdvanceReceive.columns(3).search(idFilter).draw();
                }

                if (nameFilter.length > 0) {
                    outstandingAdvanceReceive.columns(4).search(nameFilter).draw();
                }

                if (branchFilter.length > 0) {
                    outstandingAdvanceReceive.columns(0).search(branchFilter).draw();
                }

            })

            $('.btn-reset').on('click', function (e) {
                e.preventDefault();
                $("#filter-form")[0].reset();
                outstandingAdvanceReceive.columns().search('').clear().draw();
            })

            $("#outstanding-advance-receive-table_filter").hide()

            function formatNumberPrice(n) {
                // format number 1000000 to 1,234,567
                return n.toString().replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            }

            function formatCurrencyPrice(input) {
                // appends $ to value, validates decimal side
                // and puts cursor back in right position.

                // get input value
                var input_val = input;

                // don't validate empty input
                if (input_val === "") { return; }

                input_val = formatNumberPrice(input_val);
                input_val = input_val;

                // send updated string to input
                return input_val;
            }
        })
    </script>
@endsection
