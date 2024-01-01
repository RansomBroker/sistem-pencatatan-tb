$(document).ready(function () {
        const column = [
            {
                'data' : 'action',
                'title' : 'Action'
            },
            {
                'data' : 'branch',
                'title' : 'Cabang penjualan'
            },
            {
                'data' : 'buy_date',
                'title' : 'Tanggal Penjualan'
            },
            {
                'data' : 'expired_date',
                'title' : 'Expired Date'
            },
            {
                'data' : 'customer_id',
                'title' : 'ID Customer'
            },
            {
                'data' : 'customer_name',
                'title' : 'Nama Customer'
            },
            {
                'data' : 'type',
                'title' : 'Tipe'
            },
            {
                'data' : 'buy_price',
                'title' : 'IDR Harga Beli'
            },
            {
                'data' : 'net_sales',
                'title' : 'IDR Net Sales'
            },
            {
                'data' : 'tax',
                'title' : 'IDR PPN'
            },
            {
                'data' : 'payment',
                'title' : 'Pembayaran'
            },
            {
                'data' : 'qty',
                'title' : 'Qty Produk'
            },
            {
                'data' : 'unit_price',
                'title' : 'IDR Harga Satuan'
            },
            {
                'data' : 'product',
                'title' : 'Produk'
            },
            {
                'data' : 'memo',
                'title' : 'Memo/Model'
            },
            {
                'data' : 'category',
                'title' : 'Kategory Produk'
            },
            {
                'data' : 'notes',
                'title' : 'Notes'
            },
            {
                'data' : 'consumption-date-1',
                'title' : 'Tanggal Pemakaian Ke-1'
            },
            {
                'data' : 'consumption-branch-1',
                'title' : 'Tempat Pemakaian Ke-1'
            },
            {
                'data' : 'consumption-date-2',
                'title' : 'Tanggal Pemakaian Ke-2'
            },
            {
                'data' : 'consumption-branch-2',
                'title' : 'Tempat Pemakaian Ke-2'
            },
            {
                'data' : 'consumption-date-3',
                'title' : 'Tanggal Pemakaian Ke-3'
            },
            {
                'data' : 'consumption-branch-3',
                'title' : 'Tempat Pemakaian Ke-3'
            },
            {
                'data' : 'consumption-date-4',
                'title' : 'Tanggal Pemakaian Ke-4'
            },
            {
                'data' : 'consumption-branch-4',
                'title' : 'Tempat Pemakaian Ke-4'
            },
            {
                'data' : 'consumption-date-5',
                'title' : 'Tanggal Pemakaian Ke-5'
            },
            {
                'data' : 'consumption-branch-5',
                'title' : 'Tempat Pemakaian Ke-5'
            },
            {
                'data' : 'consumption-date-6',
                'title' : 'Tanggal Pemakaian Ke-6'
            },
            {
                'data' : 'consumption-branch-6',
                'title' : 'Tempat Pemakaian Ke-6'
            },
            {
                'data' : 'consumption-date-7',
                'title' : 'Tanggal Pemakaian Ke-7'
            },
            {
                'data' : 'consumption-branch-7',
                'title' : 'Tempat Pemakaian Ke-7'
            },
            {
                'data' : 'consumption-date-8',
                'title' : 'Tanggal Pemakaian Ke-8'
            },
            {
                'data' : 'consumption-branch-8',
                'title' : 'Tempat Pemakaian Ke-8'
            },
            {
                'data' : 'consumption-date-9',
                'title' : 'Tanggal Pemakaian Ke-9'
            },
            {
                'data' : 'consumption-branch-9',
                'title' : 'Tempat Pemakaian Ke-9'
            },
            {
                'data' : 'consumption-date-10',
                'title' : 'Tanggal Pemakaian Ke-10'
            },
            {
                'data' : 'consumption-branch-10',
                'title' : 'Tempat Pemakaian Ke-10'
            },
            {
                'data' : 'consumption-date-11',
                'title' : 'Tanggal Pemakaian Ke-11'
            },
            {
                'data' : 'consumption-branch-11',
                'title' : 'Tempat Pemakaian Ke-11'
            },
            {
                'data' : 'consumption-date-12',
                'title' : 'Tanggal Pemakaian Ke-12'
            },
            {
                'data' : 'consumption-branch-12',
                'title' : 'Tempat Pemakaian Ke-12'
            },
            {
                'data' : 'qty_total',
                'title' : 'QTY Total Consumption Advance Receive'
            },
            {
                'data' : 'idr_total',
                'title' : 'IDR Total Consumption Advance Receive'
            },
            {
                'data' : 'qty_expired',
                'title' : 'QTY Expired Advance Receive'
            },
            {
                'data' : 'qty_refund',
                'title' : 'QTY Refund Advance Receive'
            },
            {
                'data' : 'qty_remains',
                'title' : 'QTY Total Sisa Advance Receive'
            },
            {
                'data' : 'idr_remains',
                'title' : 'IDR Total Sisa Advance Receive'
            },
        ]

        let consumptionTable = $("#consumption-table").DataTable({
            bProcessing: true,
            bServerSide: true,
            ajax: {
                url: 'consumption/data-get',
                headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                type: 'POST',
                dataSrc: function (data) {
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
                        return `<a href='consumption/consumption-edit/${full.id}' class="btn btn-success"><i class='bx bxs-edit'></i> Edit</a>`;
                    }
                }
            ],
            rowCallback: function (row, data) {
                $('td:eq(17)', row).addClass('table-primary')
                $('td:eq(18)', row).addClass('table-primary')
                $('td:eq(21)', row).addClass('table-primary')
                $('td:eq(22)', row).addClass('table-primary')
                $('td:eq(25)', row).addClass('table-primary')
                $('td:eq(26)', row).addClass('table-primary')
                $('td:eq(29)', row).addClass('table-primary')
                $('td:eq(30)', row).addClass('table-primary')
                $('td:eq(33)', row).addClass('table-primary')
                $('td:eq(34)', row).addClass('table-primary')
                $('td:eq(37)', row).addClass('table-primary')
                $('td:eq(38)', row).addClass('table-primary')

            },
            columns: column,
            initComplete: function () {
                let table = this.api();
                let role = parseInt($('[name=role]').val())

                if (role === 1) {
                    table.column(0).visible(false)
                }

            },
        })

        $("#consumption-table_filter").hide()

        $(consumptionTable.column(17).header()).addClass("table-primary")
        $(consumptionTable.column(18).header()).addClass("table-primary")
        $(consumptionTable.column(21).header()).addClass("table-primary")
        $(consumptionTable.column(22).header()).addClass("table-primary")
        $(consumptionTable.column(25).header()).addClass("table-primary")
        $(consumptionTable.column(26).header()).addClass("table-primary")
        $(consumptionTable.column(29).header()).addClass("table-primary")
        $(consumptionTable.column(30).header()).addClass("table-primary")
        $(consumptionTable.column(33).header()).addClass("table-primary")
        $(consumptionTable.column(34).header()).addClass("table-primary")
        $(consumptionTable.column(37).header()).addClass("table-primary")
        $(consumptionTable.column(38).header()).addClass("table-primary")

        /* filter data */
        $('.btn-submit').on('click', function (e) {
            e.preventDefault()
            $('.summary-table').removeClass("d-none")
            $('.summary-text').removeClass("d-none")
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

        /* reset filter */
        $('.btn-reset').on('click', function (e) {
            e.preventDefault();
            $('.summary-table').addClass("d-none")
            $('.summary-text').addClass("d-none")
            $("#filter-form")[0].reset();
            consumptionTable.columns().search('').clear().draw();
        })

        /* export excel */
        $('.btn-export').on('click', function(e) {
            e.preventDefault();
            let idFilter = $("[name=id]").val();
            let nameFilter = $("[name=name]").val();
            let branchFilter = $("[name=branch]").val();
            /* period buy_date*/
            let startConsumptionDate = $("[name=consumption-date-start]").val();
            let endConsumptionDate = $("[name=consumption-date-end]").val();

            if (startConsumptionDate.length === 0 || endConsumptionDate.length === 0) {
                Swal.fire(
                    'Error proses export',
                    'Rentang tanggal maksimal 3 Tahun transaksi',
                    'error'
                )
                return 0;
            }

            if (startConsumptionDate.length > 0 || endConsumptionDate.length > 0) {
                let dateStart = new Date(startConsumptionDate);
                let dateEnd = new Date(endConsumptionDate);
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
                    url: 'consumption/consumption-export/excel',
                    headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        'id-filter': idFilter,
                        'name-filter': nameFilter,
                        'branch-filter': branchFilter,
                        'start-consumption-date' : startConsumptionDate,
                        'end-consumption-date': endConsumptionDate
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
                                    url: 'consumption/consumption-export/check' +'/' + data.batchID + '/' + data.name,
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
                        }
                    }
                })
            }



        })

    }
)
