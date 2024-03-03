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
                            <button type="submit" class="btn-export btn btn-success col-lg-2 col-12 mx-1 align-self-end" data-type="excel"><i class='bx bx-spreadsheet'></i> Export As Excel File</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- table --}}
            <div class="card card-body shadow-lg">
                <h3 class="summary-text d-none d-inline align-middle">Perhitungan Data Outstanding Advance Receive </h3>
                <div class="summary-table d-none mb-3 table-responsive mt-2">
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
                    type: 'POST',
                    headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                    dataSrc: function (data) {
                        $(".report-tr").empty();
                        $(".report-tr").append(`
                                        <td>${data.report.qty_remains}</td>
                                        <td>${formatCurrencyPrice(data.report.idr_remains.toString().split('.')[0])}</td>
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
                $('.summary-table').removeClass("d-none")
                $('.summary-text').removeClass("d-none")
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

                outstandingAdvanceReceive.columns(3).search(idFilter).draw();
                outstandingAdvanceReceive.columns(4).search(nameFilter).draw();
                outstandingAdvanceReceive.columns(0).search(branchFilter).draw();
            })

            $('.btn-reset').on('click', function (e) {
                e.preventDefault();
                $('.summary-table').addClass("d-none")
                $('.summary-text').addClass("d-none")
                $("#filter-form")[0].reset();
                outstandingAdvanceReceive.columns().search('').clear().draw();
            })

            $("#outstanding-advance-receive-table_filter").hide()

            /* btn export */
            $('.btn-export').on('click', function(e) {
                e.preventDefault();
                let idFilter = $("[name=id]").val();
                let nameFilter = $("[name=name]").val();
                let branchFilter = $("[name=branch]").val();
                /* period buy_date*/
                let startBuyDate = $("[name=buy-date-start]").val();
                let endBuyDate = $("[name=buy-date-end]").val();

                if (startBuyDate.length === 0 || endBuyDate.length === 0) {
                    Swal.fire(
                        'Error proses export',
                        'Rentang tanggal maksimal 3 Tahun transaksi',
                        'error'
                    )
                    return 0;
                }

                if (startBuyDate.length > 0 || endBuyDate.length > 0) {
                    let dateStart = new Date(startBuyDate);
                    let dateEnd = new Date(endBuyDate);
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
                        url: "{{ URL::to('outstanding/outstanding-export/excel') }}",
                        headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            'id-filter': idFilter,
                            'name-filter': nameFilter,
                            'branch-filter': branchFilter,
                            'start-buy-date' : startBuyDate,
                            'end-buy-date': endBuyDate
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
                                        url: "{{ URL::to('outstanding/outstanding-export/check') }}" +"/" + data.batchID + "/" + data.name,
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
                                            } else {
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
                            } else {
                                Swal.fire(
                                    'Error proses export',
                                    'Terjadi Error Saat Proses Export',
                                    'error'
                                )
                            }
                        }
                    })
                }

            })

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
