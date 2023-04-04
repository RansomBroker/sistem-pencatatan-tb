@extends('master')
@section('title', 'Category')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Category</h2>
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

            <a href="{{ URL::to('category/category-add') }}" class="col-12 col-lg-3 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add New Category</a>
            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Category</h3>
                <h5 class="d-inline align-middle"> Filter Berdasarkan:</h5>
                <form class="mb-3" id="filter-form">
                    <div class="row g-2">
                        <div class="col-lg-6 col-12">
                            <label class="form-label">Nama Category</label>
                            <input class="form-control" name="name" placeholder="Ketikan Nama customer">
                        </div>
                        <div class="col-lg-6 col-12 row mt-3">
                            <button type="submit" class="btn btn-info btn-submit col-lg-2 col-12 me-1 align-self-end"><i class='bx bx-search' ></i> Cari</button>
                            <button type="reset" class="btn btn-danger btn-reset col-lg-3 col-12 align-self-end"><i class='bx bx-reset'></i> Reset Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- table --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Data Category </h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover w-100 text-nowrap" id="category-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
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
                let categoryTable = $("#category-table").DataTable({
                    bProcessing: true,
                    bServerSide: true,
                    ajax: {
                        url: "{{ URL::to('category/data-get') }}",
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
                        {
                            sortable: false,
                            "render": function(data, type, full, meat) {
                                return `
                                <a href="{{ URL::to('category/category-edit') }}/${full.id}" class="btn btn-success"><i class='bx bxs-edit'></i> Edit</a>
                                <button class="btn-delete btn btn-danger" data-id="${full.id}" data-name="${full.name}"><i class='bx bxs-trash=alt'></i> Delete</button>`
                            }
                        }
                    ]
                })

                $("#category-table_filter").hide()

                categoryTable.on( 'draw.dt', function () {
                    var PageInfo = $('#category-table').DataTable().page.info();
                    categoryTable.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                        cell.innerHTML = i + 1 + PageInfo.start;
                    } );
                });

                /* filter data */
                $('.btn-submit').on('click', function (e) {
                    e.preventDefault()
                    categoryTable.columns().search('').draw();
                    let nameFilter = $('[name=name]').val();

                    if (nameFilter.length > 0 ) {
                        categoryTable.columns(1).search(nameFilter).draw();
                    }
                })

                $('.btn-reset').on('click', function (e) {
                    e.preventDefault();
                    $("#filter-form")[0].reset();
                    categoryTable.columns().search('').clear().draw();
                })

                /* Delete Category */
                $(document).on('click', '.btn-delete', function () {
                    let categoryID = $(this).attr("data-id");
                    let categoryName = $(this).attr("data-name");

                    swal.fire({
                        icon: 'question',
                        title: 'Konfirmasi Penghapusan Category',
                        text: 'Apakah anda yakin akan menghapus category ' + categoryName,
                        showCancelButton: true,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ URL::to('category/category-delete') }}" + '/' + categoryID,
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
                                            title: 'Berhasil Menghapus Category',
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
                                            title: 'Gagal Menghapus Category',
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
                })
            }
        )
    </script>
@endsection
