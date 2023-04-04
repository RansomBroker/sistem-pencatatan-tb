@extends('master')
@section('title', 'Branch')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Branch</h2>
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

            <a href="{{ URL::to('branch/branch-add') }}" class="col-12 col-lg-2 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add New Branch</a>
            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Branch</h3>
                <h5 class="d-inline align-middle"> Filter Berdasarkan:</h5>
                <form class="mb-3" id="filter-form">
                    <div class="row g-1">
                        <div class="col-lg-2 col-6">
                            <label class="form-label">Nama</label>
                            <input class="form-control" name="name" placeholder="Ketikan Nama Branch">
                        </div>
                        <div class="col-lg-2 col-6">
                            <label class="form-label">Branch</label>
                            <input class="form-control" name="branch" placeholder="Ketikan Branch">
                        </div>
                        <div class="col-lg-2 col-6">
                            <label class="form-label">Address</label>
                            <input class="form-control" name="address" placeholder="Ketikan Alamat">
                        </div>
                        <div class="col-lg-2 col-6">
                            <label class="form-label">No. Telp</label>
                            <input class="form-control" name="tel" placeholder="Ketikan Alamat">
                        </div>
                        <div class="col-lg-2 col-6">
                            <label class="form-label">NPWP</label>
                            <input class="form-control" name="npwp" placeholder="Ketikan NPWP">
                        </div>
                        <div class="col-lg-2 col-6">
                            <label class="form-label">Company</label>
                            <input class="form-control" name="company" placeholder="Ketikan Company">
                        </div>
                        <button type="submit" class="btn btn-info btn-submit col-lg-2 col-12 me-1"><i class='bx bx-search' ></i> Cari</button>
                        <button type="submit" class="btn btn-danger btn-reset col-lg-2 col-12"><i class='bx bx-reset'></i> Reset Filter</button>
                    </div>
                </form>
            </div>

            {{-- table --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Data Branch </h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover w-100" id="branch-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Address</th>
                            <th>No.Telp</th>
                            <th>NPWP</th>
                            <th>Company</th>
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
                let branchTable = $("#branch-table").DataTable({
                    bProcessing: true,
                    bServerSide: true,
                    ajax: {
                        url: "{{ URL::to('branch/data-get') }}",
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
                        {"data": "name"},
                        {"data": "branch"},
                        {"data": "address"},
                        {"data": "telephone"},
                        {"data": "npwp"},
                        {"data": "company"},
                        {
                            sortable: false,
                            "render": function(data, type, full, meat) {
                                return `<a href="{{ URL::to('branch/branch-edit/') }}/${full.id}" class="btn btn-success"><i class='bx bxs-edit'></i> Edit</a>
                                <button class="btn-delete btn btn-danger" data-id="${full.id}" data-name="${full.name}"><i class='bx bxs-trash=alt'></i> Delete</button>`
                            }
                        }
                    ]
                })

                $(document).on('click', '.btn-delete', function(e) {
                    let branchID = $(this).attr("data-id");
                    let branchName = $(this).attr("data-name");
                    swal.fire({
                        icon: 'question',
                        title: 'Konfirmasi Penghapusan Cabang',
                        text: 'Apakah anda yakin akan menghapus cabang ' + branchName,
                        showCancelButton: true,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ URL::to('branch/branch-delete') }}" + '/' + branchID,
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
                                            title: 'Berhasil Menghapus Branch',
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
                                            title: 'Gagal Menghapus Branch',
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

                $("#branch-table_filter").hide()
                branchTable.on( 'draw.dt', function () {
                    var PageInfo = $('#branch-table').DataTable().page.info();
                    branchTable.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                        cell.innerHTML = i + 1 + PageInfo.start;
                    } );
                });
                /* filter data */
                $('.btn-submit').on('click', function (e) {
                    e.preventDefault()
                    branchTable.columns().search('').draw();
                    let nameFilter = $("[name=name]").val();
                    let branchFilter = $("[name=branch]").val();
                    let addressFilter = $("[name=address]").val();
                    let telephoneFilter = $("[name=tel]").val();
                    let npwpFilter = $("[name=npwp]").val();
                    let companyFilter = $("[name=company]").val();


                    if (nameFilter.length > 0 ) {
                        branchTable.columns(1).search(nameFilter).draw()
                    }
                    if (branchFilter.length > 0 ) {
                        branchTable.columns(2).search(branchFilter).draw()
                    }
                    if (addressFilter.length > 0) {
                        branchTable.columns(3).search(addressFilter).draw()
                    }
                    if (telephoneFilter.length > 0 ) {
                        branchTable.columns(4).search(telephoneFilter).draw()
                    }
                    if (npwpFilter.length > 0) {
                        branchTable.columns(5).search(npwpFilter).draw()
                    }
                    if (companyFilter.length > 0) {
                        branchTable.columns(6).search(companyFilter).draw()
                    }
                })
                $('.btn-reset').on('click', function (e) {
                    e.preventDefault();
                    $("#filter-form")[0].reset();
                    branchTable.columns().search('').clear().draw();
                })
            }
        )
    </script>
@endsection

