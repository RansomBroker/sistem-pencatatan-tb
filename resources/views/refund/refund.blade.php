@extends('master')
@section('title', 'Refund Advance Receives')
@section('content')
    <div class="container-fluid p-o">

        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Refund Advance Receive</h2>
        </div>

        <div class="card card-body row d-flex flex-column flex-wrap">

            @if($message = Session::get('message'))
                @if($status = Session::get('status'))
                    <div class="alert alert-{{ $status}} alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endif

                <a href="{{ URL::to('refund/add-refund') }}" class="col-12 col-lg-3 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add New Refund Advance Receive</a>
                {{-- search form --}}
                <div class="card card-body shadow-lg">
                    <h3 class="d-inline align-middle"> Cari Refund</h3>
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
                                <label class="form-label p-0 fw-bold">Tanggal Refund Advance Receive</label>
                                <div class="col-lg-6 col-12 p-0">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" name="refund-date-start">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" name="refund-date-end">
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
                    <h3 class="d-inline align-middle">Perhitungan Data Refund Advance Receive </h3>
                    <div class="mb-3 table-responsive mt-2">
                        <table class="table table-striped table-hover text-nowrap w-100">
                            <thead>
                            <tr>
                                <th>QTY Refund Advance Receive</th>
                                <th>IDR Refund Advance Receive</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="report-tr">
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-nowrap w-100" id="refund-advance-receive-table">
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
                                <th>Tanggal Refund </th>
                                <th>Cabang Refund</th>
                                <th>QTY Refund Advance Receive</th>
                                <th>IDR Refund Advance Receive</th>
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
            let refundAdvanceReceiveTable = $('#refund-advance-receive-table').DataTable({
                bProcessing: true,
                bServerSide: true,
                ajax: {
                    url: "{{ URL::to('/refund/data-get') }}",
                    type: 'GET',
                    dataSrc: function (data) {
                        $(".report-tr").empty();
                        $(".report-tr").append(`
                                        <td>${data.report.qty_refund}</td>
                                        <td>${data.report.idr_refund}</td>
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
                    {"data" : "refund_date"},
                    {"data" : "refund_branches[0].name"},
                    {"data" : "qty_refund"},
                    {
                        sortable: false,
                        "render": function(data, type, full, meat) {
                            return formatCurrencyPrice(full.idr_refund.split('.')[0])
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
                let startRefundDate = $("[name=refund-date-start]").val();
                let endRefundDate = $("[name=refund-date-end]").val();

                let refundDateFilter = "";
                let startDate = "1980-01-01";
                let endDate = "2999-01-01";


                /* search filter */
                if (startRefundDate.length > 0 || endRefundDate.length > 0  ) {
                    if (startRefundDate.length < 1 ) {
                        refundDateFilter = startDate + "||" + endRefundDate
                    }

                    if (endRefundDate.length < 1 ) {
                        refundDateFilter = startRefundDate + "||" + endDate
                    }

                    if (startRefundDate.length < 1 && endRefundDate.length < 1) {
                        refundDateFilter = startDate + "||" + endDate
                    }

                    if (startRefundDate.length > 0 && endRefundDate.length > 0) {
                        refundDateFilter = startRefundDate + "||" + endRefundDate
                    }
                    refundAdvanceReceiveTable.columns(2).search(refundDateFilter).draw();
                }

                if (idFilter.length > 0 ) {
                    refundAdvanceReceiveTable.columns(3).search(idFilter).draw();
                }

                if (nameFilter.length > 0) {
                    refundAdvanceReceiveTable.columns(4).search(nameFilter).draw();
                }

                if (branchFilter.length > 0) {
                    refundAdvanceReceiveTable.columns(0).search(branchFilter).draw();
                }

            })

            $('.btn-reset').on('click', function (e) {
                e.preventDefault();
                $("#filter-form")[0].reset();
                refundAdvanceReceiveTable.columns().search('').clear().draw();
            })

            $("#refund-advance-receive-table_filter").hide()

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
