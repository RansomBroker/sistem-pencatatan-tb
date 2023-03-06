@extends('master')
@section('title', 'Product')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Product</h2>
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

            <a href="{{ URL::to('product/product-add') }}" class="col-12 col-lg-3 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add New Product</a>
            {{-- search form --}}
            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Cari Product</h3>
                <h5 class="d-inline align-middle"> Filter Berdasarkan:</h5>
                <form class="mb-3" id="filter-form">
                    <div class="row g-2">
                        <div class="col-lg-4 col-12">
                            <label class="form-label">Product ID</label>
                            <input class="form-control" name="id" placeholder="Ketikan Product ID">
                        </div>
                        <div class="col-lg-4 col-12">
                            <label class="form-label">Nama Category</label>
                            <input class="form-control" name="category" placeholder="Ketikan Category">
                        </div>
                        <div class="col-lg-4 col-12">
                            <label class="form-label">Nama Product</label>
                            <input class="form-control" name="product" placeholder="Ketikan Product">
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
                <h3 class="d-inline align-middle"> Data Product </h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover w-100 text-nowrap" id="product-table">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Product ID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Memo</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Product ID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Memo</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
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
                let productTable = $("#product-table").DataTable({
                    bProcessing: true,
                    bServerSide: true,
                    ajax: {
                        url: "{{ URL::to('product/data-get') }}",
                        headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                        type: 'POST',
                        initComplete: function(data) {
                            console.log(data)
                        }
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
                        {"data": "product_id"},
                        {"data": "categories.0.name"},
                        {"data": "name"},
                        {"data": "memo"},
                        {
                            sortable: false,
                            "render": function(data, type, full, meat) {
                                return `<a href="{{ URL::to('product/product-edit') }}/${full.id}" class="btn btn-success"><i class='bx bxs-edit'></i> Edit</a>  <button class="btn btn-danger"><i class='bx bxs-trash=alt'></i> Delete</button>`
                            }
                        }
                    ]
                })

                $("#product-table_filter").hide()

                productTable.on( 'draw.dt', function () {
                    var PageInfo = $('#product-table').DataTable().page.info();
                    productTable.column(0, { page: 'current' }).nodes().each( function (cell, i) {
                        cell.innerHTML = i + 1 + PageInfo.start;
                    } );
                });

                /* filter data */
                $('.btn-submit').on('click', function (e) {
                    e.preventDefault()
                    productTable.columns().search('').draw();
                    let categoryFilter = $('[name=category]').val();
                    let productFilter = $('[name=product]').val();
                    let idFilter = $('[name=id]').val();

                    if (idFilter.length > 0 ) {
                        productTable.columns(1).search(idFilter).draw();
                    }

                    if (categoryFilter.length > 0 ) {
                        productTable.columns(2).search(categoryFilter).draw();
                    }

                    if (productFilter.length > 0 ) {
                        productTable.columns(3).search(productFilter).draw();
                    }

                })

                $('.btn-reset').on('click', function (e) {
                    e.preventDefault();
                    $("#filter-form")[0].reset();
                    productTable.columns().search('').clear().draw();
                })

            }
        )
    </script>
@endsection
