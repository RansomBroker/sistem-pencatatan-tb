@extends('master')
@section('title', 'Consumption')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

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
                /* initiate table */
                $.ajax({
                    url: "{{ URL::to("/consumption/get-column") }}",
                    success: function (result) {
                        let consumptionTable = $("#consumption-table").DataTable({
                            bProcessing: true,
                            bServerSide: true,
                            ajax: {
                                url: "{{ URL::to('consumption/data-get') }}",
                                headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                                type: 'GET',
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
                            columns: result.columns,
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

                        $('.btn-reset').on('click', function (e) {
                            e.preventDefault();
                            $("#filter-form")[0].reset();
                            consumptionTable.columns().search('').clear().draw();
                        })
                    }

                });


            }
        )
    </script>
@endsection
