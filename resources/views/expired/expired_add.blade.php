@extends('master')
@section('title', 'Add Expired Advance Receive')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Add Expired Advance Receive</h2>
        </div>

        {{-- card --}}
        <div class="card card-body row d-flex flex-column flex-wrap">

            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Expired</h3>
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
                            <label class="form-label p-0 fw-bold">Tanggal Expired</label>
                            <div class="col-lg-6 col-12 p-0">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" name="expired-date-start">
                            </div>
                            <div class="col-lg-6 col-12 p-0">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="expired-date-end">
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
                <h3 class="d-inline align-middle">Data Advance Receive</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-nowrap w-100" id="expired-advance-receive-table">
                        <thead>
                        <tr>
                            <th>Action</th>
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
                            <th>QTY Expired Advance Receive</th>
                            <th>IDR Expired Advance Receive</th>
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
        $(document).ready(function() {
            let expiredAdvanceReceiveTable = $('#expired-advance-receive-table').DataTable({
                bProcessing: true,
                bServerSide: true,
                ajax: {
                    url: "{{ URL::to('/expired/data-get-available') }}",
                    type: 'GET',
                    dataSrc: function (data) {
                        return data.data
                    }
                },
                language: {
                    processing: `<div class="spinner-border text-secondary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                             </div>`,
                },
                columns: [
                    {},
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
                    {"data" : "qty_expired"},
                    {
                        sortable: false,
                        "render": function(data, type, full, meat) {
                            if (full.idr_expired === null) {
                                return '-'
                            }
                            return formatCurrencyPrice(full.idr_expired.split('.')[0])
                        }
                    },
                    {"data" : "qty_remains"},
                    {
                        sortable: false,
                        "render": function(data, type, full, meat) {
                            if (full.idr_remains === null) {
                                return '-'
                            }
                            return formatCurrencyPrice(full.idr_remains.split('.')[0])
                        }
                    },
                ],
                columnDefs: [
                    {
                        target: 0,
                        searchable: false,
                        orderable: false,
                        render: function (data, type, full, meta){
                            return `
                                            <button class="btn-expired btn btn-danger" data-advance-receive="${full.id+'||'+full.customers[0].name}">Tambah Expired</buton>
                                        `;
                        }
                    },
                    {
                        targets: '_all',
                        orderable:false
                    }
                ]
            })

            $("#expired-advance-receive-table_filter").hide()

            /* filter data */
            $('.btn-submit').on('click', function (e) {
                e.preventDefault()
                let idFilter = $("[name=id]").val();
                let nameFilter = $("[name=name]").val();
                let branchFilter = $("[name=branch]").val();
                /* period buy_date*/
                let startExpiredDate = $("[name=expired-date-start]").val();
                let endExpiredDate = $("[name=expired-date-end]").val();

                let expiredDateFilter = "";
                let startDate = "1980-01-01";
                let endDate = "2999-01-01";


                /* search filter */
                if (startExpiredDate.length > 0 || endExpiredDate.length > 0  ) {
                    if (startExpiredDate.length < 1 ) {
                        expiredDateFilter = startDate + "||" + endExpiredDate
                    }

                    if (endExpiredDate.length < 1 ) {
                        expiredDateFilter = startExpiredDate + "||" + endDate
                    }

                    if (startExpiredDate.length < 1 && endExpiredDate.length < 1) {
                        expiredDateFilter = startDate + "||" + endDate
                    }

                    if (startExpiredDate.length > 0 && endExpiredDate.length > 0) {
                        expiredDateFilter = startExpiredDate + "||" + endExpiredDate
                    }
                    expiredAdvanceReceiveTable.columns(2).search(expiredDateFilter).draw();
                }

                if (idFilter.length > 0 ) {
                    expiredAdvanceReceiveTable.columns(3).search(idFilter).draw();
                }

                if (nameFilter.length > 0) {
                    expiredAdvanceReceiveTable.columns(4).search(nameFilter).draw();
                }

                if (branchFilter.length > 0) {
                    expiredAdvanceReceiveTable.columns(0).search(branchFilter).draw();
                }

            })

            /* reset form */
            $('.btn-reset').on('click', function (e) {
                e.preventDefault();
                $("#filter-form")[0].reset();
                expiredAdvanceReceiveTable.columns().search('').clear().draw();
            })

            /* btn tambah expired */
            $(document).on('click', '.btn-expired', function (e) {
                let id = $(this).attr('data-advance-receive').split('||')[0];
                let name = $(this).attr('data-advance-receive').split('||')[1];

                swal.fire({
                    icon: 'question',
                    title: 'Konfirmasi Expired Advance Receive',
                    text: 'Apakah anda yakin akan expired/mengkadaluarsakan advance receive customer ' + name,
                    showCancelButton: true,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ URL::to('expired/add-expired/') }}' + '/' + id,
                            method: 'GET',
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
                            success: function (response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil Expired Advance receive',
                                        text: response.message,
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                        }
                                    })
                                    window.location.reload();
                                }
                            }
                        })
                    }
                })

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
