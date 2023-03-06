@extends('master')
@section('title', 'Tambah Consumption')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Tambah Consumption</h2>
        </div>

        {{-- card --}}
        <div class="card card-body row d-flex flex-column flex-wrap">
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
                <h3 class="d-inline align-middle"> Tambah Consumption </h3>
                <form id="consumption-form">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-4">
                            <label class="form-label">Tanggal Consumption <sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control" name="consumption-date" required>
                        </div>
                        <div class="col-lg-4 col-12">
                            <label class="form-label">Cabang <sup class="text-danger">*</sup></label>
                            <select class="form-select" id="branch-id" name="branch-id" required>
                                <option value="">--- Pilih Cabang ---</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-striped table-hover w-100 text-nowrap" id="consumption-table">
                        </table>
                    </div>
                    <h4 class="fw-semibold mt-3 mb-3">Data yang Dipilih</h4>
                    <div class="table-responsive ">
                        <table class="table table-striped table-hover w-100 text-nowrap" id="selected-consumption-table">
                            <thead>
                                <tr>
                                    <th>ID Costumer</th>
                                    <th>Nama Customer</th>
                                </tr>
                            </thead>
                            <tbody id="selected-consumption-body">

                            </tbody>
                        </table>
                    </div>
                    <div class="row g-2 mt-3">
                        <button type="submit"  class="btn-submit-form btn btn-secondary ">Tambah</button>
                        <button class="btn-reset-submit-form btn btn-danger "><i class='bx bx-reset'></i> Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('custom-js')
    <script>
        $(document).ready(function () {
                /* initiate table */
                $.ajax({
                    url: "{{ URL::to("consumption/consumption-get-available") }}",
                    success: function (result) {
                        var rows_selected = [];
                        let consumptionTable = $("#consumption-table").DataTable({
                            bProcessing: true,
                            bServerSide: true,
                            ajax: {
                                url: "{{ URL::to('consumption/consumption-get-available') }}",
                                headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
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
                            columns: result.columns,
                            columnDefs: [{
                                targets: 0,
                                searchable :false,
                                orderable :false,
                                width :'1%',
                                className: 'dt-body-center',
                                render: function (data, type, full, meta){
                                    return '<input type="checkbox">';
                                }
                            }],
                            order: [1, 'asc'],
                            rowCallback : function(row, data, dataIndex){
                                // Get row ID
                                var rowId = data.id;

                                // If row ID is in the list of selected row IDs
                                if($.inArray(rowId, rows_selected) !== -1){
                                    $(row).find('input[type="checkbox"]').prop('checked', true);
                                    $(row).addClass('selected');
                                }
                            }
                        })

                        $("#consumption-table_filter").hide()
                        $("#consumption-table thead tr .dt-body-center").html(`<input name="select_all" value="1" type="hidden">Action`)


                        // Handle click on checkbox
                        $('#consumption-table tbody').on('click', 'input[type="checkbox"]', function(e){
                            var $row = $(this).closest('tr');

                            // Get row data
                            var data = consumptionTable.row($row).data();

                            // Get row ID
                            var rowId = data.id;

                            // Determine whether row ID is in the list of selected row IDs
                            var index = $.inArray(rowId, rows_selected);

                            // If checkbox is checked and row ID is not in list of selected row IDs
                            if(this.checked && index === -1){
                                rows_selected.push(rowId);

                                // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                            } else if (!this.checked && index !== -1){
                                rows_selected.splice(index, 1);
                            }

                            if(this.checked){
                                $row.addClass('selected');
                            } else {
                                $row.removeClass('selected');
                            }

                            // tampilkan data
                            selectedConsumption(rows_selected);
                            // Prevent click event from propagating to parent
                            e.stopPropagation();
                        });

                        // reset selected data
                        $('.btn-reset-submit-form').on('click', function (e) {
                            e.preventDefault();
                            $("#consumption-form")[0].reset();
                            var $row = $('input[type="checkbox"]').closest('tr');

                            // Get row data
                            var data = consumptionTable.row($row).data();

                            // Get row ID
                            var rowId = data.id;

                            // Determine whether row ID is in the list of selected row IDs
                            var index = $.inArray(rowId, rows_selected);

                            // If checkbox is checked and row ID is not in list of selected row IDs
                            rows_selected = [];

                            console.log(rows_selected);
                            $row.removeClass('selected');
                            $('input[type="checkbox"]').prop('checked', false);

                            selectedConsumption(rows_selected);
                            // Prevent click event from propagating to parent
                            e.stopPropagation();

                        })

                        // tambah data
                        $("#consumption-form").on("submit", function (e) {
                            e.preventDefault();
                            let consumptionDate = $("input[name=consumption-date]").val();
                            let branchID = $("#branch-id option:selected").val()

                            if (rows_selected.length == 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Tidak Boleh Kosong',
                                    text: 'Silahkan Pilih Data Consumption',
                                })
                                return;
                            }

                            /* make request to server */
                            $.ajax({
                                url: "{{ URL::to('consumption/consumption-add/add') }}",
                                headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                                method: 'POST',
                                data: {
                                    'consumption-date' : consumptionDate,
                                    'branch-id' : branchID,
                                    'advance-receive-id-list' :rows_selected
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
                                success: function (data) {
                                    if (data.status === "success" ) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil menambahkan Consumption',
                                            text: 'Consumption berhasil ditambahkan',
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
                        })

                        // filter data
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

                            if (idFilter.length > 0 ) {
                                consumptionTable.columns(4).search(idFilter).draw();
                            }

                            if (nameFilter.length > 0) {
                                consumptionTable.columns(5).search(nameFilter).draw();
                            }

                            if (branchFilter.length > 0) {
                                consumptionTable.columns(1).search(branchFilter).draw();
                            }

                        })

                        // reset form pencarian
                        $('.btn-reset').on('click', function (e) {
                            e.preventDefault();
                            $("#filter-form")[0].reset();
                            consumptionTable.columns().search('').clear().draw();
                        })

                    }
                });

                function selectedConsumption(selectedRows) {
                    $.ajax({
                        url: "{{ URL::to('/consumption/consumption-get-selected-customer') }}",
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                        data: {
                            'selectedRows' : selectedRows
                        },
                        success:function (response) {
                            $('#selected-consumption-body').empty();
                            let html = ``;
                            response.data.forEach(function(data) {
                                html += `
                                    <tr>
                                        <td>${data.customers[0].customer_id}</td>
                                        <td>${data.customers[0].name}</td>
                                    </tr>
                                `
                            });

                            $("#selected-consumption-body").append(html)

                        }
                    })
                }
            }
        )
    </script>
@endsection
