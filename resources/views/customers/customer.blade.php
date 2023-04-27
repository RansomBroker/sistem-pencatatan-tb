@extends('master')
@section('title', 'Customer')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf
        <input type="hidden" name="role" value="{{ \Illuminate\Support\Facades\Auth::user()->role }}">

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Customer</h2>
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

            <a href="{{ URL::to('customer/customer-add') }}" class="col-12 col-lg-3 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add New Customer</a>
            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Customer</h3>
                <h5 class="d-inline align-middle"> Filter Berdasarkan:</h5>
                <form class="mb-3" id="filter-form">
                    <div class="row g-2">
                        <div class="col-lg-4 col-6">
                            <label class="form-label">ID Customer</label>
                            <input class="form-control" name="customer-id" placeholder="Ketikan ID Customer">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">Nama</label>
                            <input class="form-control" name="name" placeholder="Ketikan Nama customer">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">Nickname</label>
                            <input class="form-control" name="nickname" placeholder="Ketikan nickname">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">Address</label>
                            <input class="form-control" name="address" placeholder="Ketikan Alamat">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="birth-date" placeholder="pilih tanggal">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">No. Telp</label>
                            <input class="form-control" name="tel" placeholder="Ketikan Alamat">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">No. KTP</label>
                            <input class="form-control" name="identity" placeholder="Ketikan No KTP">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">No. Rekening</label>
                            <input type="text" class="form-control" name="payment-number" placeholder="Ketikan No Rekening">
                        </div>
                        <div class="col-lg-4 col-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Ketikan Email">
                        </div>
                        <div class="col-lg-12 col-12 row mt-3">
                            <button type="submit" class="btn btn-info btn-submit col-lg-2 col-12 me-1 align-self-end"><i class='bx bx-search' ></i> Cari</button>
                            <button type="submit" class="btn btn-danger btn-reset col-lg-2 col-12 align-self-end"><i class='bx bx-reset'></i> Reset Filter</button>
                            <button type="submit" class="btn-export btn btn-success col-lg-2 col-12 mx-1 align-self-end" data-type="excel"><i class='bx bx-spreadsheet'></i> Export As Excel File</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- table --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Data Customer </h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover w-100 text-nowrap" id="customer-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Nickname</th>
                            <th>Alamat</th>
                            <th>Tanggal Lahir</th>
                            <th>No.Telp</th>
                            <th>No. KTP</th>
                            <th>No. Rekening</th>
                            <th>Email</th>
                            <th>Action</th>
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
                /* initiate table */
                let customerTable = $("#customer-table").DataTable({
                    bProcessing: true,
                    bServerSide: true,
                    ajax: {
                        url: "{{ URL::to('customer/data-get') }}",
                        headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                        type: 'POST',
                    },
                    language: {
                        processing: `<div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Loading...</span>
                        </div>`,
                    },
                    columns: [
                        {
                            sortable: false,
                            "render": function(data, type, full, meat) {
                                return ``
                            }
                        },
                        {"data": "customer_id"},
                        {"data": "name"},
                        {"data": "nickname"},
                        {"data": "address"},
                        {"data": "birth_date"},
                        {"data": "phone"},
                        {"data": "identity_number"},
                        {"data": "payment_number"},
                        {"data": "email"},
                        {
                            sortable: false,
                            "render": function(data, type, full, meat) {
                                return `<a href="{{ URL::to('customer/customer-edit') }}/${full.id}" class="btn btn-success"><i class='bx bxs-edit'></i> Edit</a>
                                <button class="btn-delete btn btn-danger" data-id="${full.id}" data-name="${full.name}"><i class='bx bxs-trash=alt'></i> Delete</button>`
                            }
                        }
                    ],
                    initComplete: function () {
                        let table = this.api();
                        let role = parseInt($('[name=role]').val())

                        if (role === 1) {
                            table.column(10).visible(false)
                        }

                    },
                })

                $(document).on('click', '.btn-delete', function(e) {
                    let customerID = $(this).attr("data-id");
                    let customerName = $(this).attr("data-name");
                    swal.fire({
                        icon: 'question',
                        title: 'Konfirmasi Penghapusan Customer',
                        text: 'Apakah anda yakin akan menghapus customer ' + customerName,
                        showCancelButton: true,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ URL::to('customer/customer-delete') }}" + '/' + customerID,
                                method: "GET",
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
                                    console.log(response)
                                    if (response.status === "success") {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil Menghapus Customer',
                                            text: response.message,
                                            showConfirmButton: false,
                                            allowOutsideClick: false,
                                            allowEscapeKey: false
                                        })
                                        setTimeout(function () {
                                            window.location.reload();
                                        }, 1250)
                                    }

                                    if (response.status === "failed") {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal Menghapus Customer',
                                            text: response.message,
                                            showConfirmButton: false,
                                            allowOutsideClick: false,
                                            allowEscapeKey: false
                                        })
                                        setTimeout(function () {
                                            window.location.reload();
                                        }, 1250)
                                    }
                                }
                            })
                        }
                    })
                });

                $("#customer-table_filter").hide()
                customerTable.on( 'draw.dt', function () {
                    var PageInfo = $('#customer-table').DataTable().page.info();
                    customerTable.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                        cell.innerHTML = i + 1 + PageInfo.start;
                    } );
                });
                /* filter data */
                $('.btn-submit').on('click', function (e) {
                    e.preventDefault()
                    customerTable.columns().search('').draw();
                    let idFilter = $("[name=customer-id]").val();
                    let nameFilter = $("[name=name]").val();
                    let nicknameFilter = $("[name=nickname]").val();
                    let addressFilter = $("[name=address]").val();
                    let birthFilter = $("[name=birth-date]").val();
                    let phoneFIlter = $("[name=tel]").val();
                    let identityFilter = $("[name=identity]").val();
                    let paymentFilter = $("[name=payment-number]").val();
                    let emailFilter = $("[name=email]").val();


                    if (idFilter.length > 0 ) {
                        console.log(idFilter)
                        customerTable.columns(1).search(idFilter).draw()
                    }

                    if (nameFilter.length > 0) {
                        console.log(nameFilter)
                        customerTable.columns(2).search(nameFilter).draw()
                    }

                    if (nicknameFilter.length > 0 ) {
                        console.log(nicknameFilter)
                        customerTable.columns(3).search(nicknameFilter).draw()
                    }

                    if (addressFilter.length > 0) {
                        console.log(addressFilter)
                        customerTable.columns(4).search(addressFilter).draw()
                    }

                    if (birthFilter.length > 0) {
                        console.log(birthFilter)
                        customerTable.columns(5).search(birthFilter).draw()
                    }

                    if (phoneFIlter.length > 0 ) {
                        console.log(phoneFIlter)
                        customerTable.columns(6).search(phoneFIlter).draw()
                    }

                    if (identityFilter.length > 0 ) {
                        console.log(identityFilter)
                        customerTable.columns(7).search(identityFilter).draw()
                    }

                    if (paymentFilter.length > 0) {
                        console.log(paymentFilter)
                        customerTable.columns(8).search(paymentFilter).draw()
                    }

                    if (emailFilter.length > 0 ) {
                        console.log(emailFilter)
                        customerTable.columns(9).search(emailFilter).draw()
                    }
                })

                $('.btn-reset').on('click', function (e) {
                    e.preventDefault();
                    $("#filter-form")[0].reset();
                    customerTable.columns().search('').clear().draw();
                })

                /* btn export */
                $('.btn-export').on('click', function(e) {
                    e.preventDefault();
                    let idFilter = $("[name=customer-id]").val();
                    let nameFilter = $("[name=name]").val();
                    let nicknameFilter = $("[name=nickname]").val();
                    let addressFilter = $("[name=address]").val();
                    let birthFilter = $("[name=birth-date]").val();
                    let phoneFIlter = $("[name=tel]").val();
                    let identityFilter = $("[name=identity]").val();
                    let paymentFilter = $("[name=payment-number]").val();
                    let emailFilter = $("[name=email]").val();

                    $.ajax({
                        url: "{{ URL::to('customer/customer-export/excel') }}",
                        headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            'id-filter': idFilter,
                            'name-filter': nameFilter,
                            'nickname-filter': nicknameFilter,
                            'address-filter': addressFilter,
                            'birth-filter': birthFilter,
                            'phone-filter': phoneFIlter,
                            'identity-filter': identityFilter,
                            'payment-filter': paymentFilter,
                            'email-filter': emailFilter
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
                                        url: "{{ URL::to('customer/customer-export/check') }}" +"/" + data.batchID + "/" + data.name,
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
                            } else {
                                Swal.fire(
                                    'Error proses export',
                                    'Terjadi Error Saat Proses Export',
                                    'error'
                                )
                            }
                        }
                    })



                })

            }
        )
    </script>
@endsection

