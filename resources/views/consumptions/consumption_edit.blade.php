@extends('master')
@section('title', 'Edit Consumption')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Edit Consumption</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            <form method="POST" action="{{ URL::to('consumption/consumption-edit/edit') }}">
                {{-- csrf --}}
                @csrf
                <div class="row">
                    <input type="hidden" name="advance-receive-id" value="{{ $advanceReceive->id }}">
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">Cabang Penjualan</label>
                        <input type="hidden" name="branch" value="{{ $advanceReceive->branch_id }}">
                        <select class="form-select" name="branch" disabled>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @if($branch->id == $advanceReceive->branch_id) selected @endif >{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">Tanggal Penjualan</label>
                        <input type="date" class="form-control" name="buy-date" value="{{ $advanceReceive->buy_date }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">Expired</label>
                        <input type="date" class="form-control" name="expired-date" value="{{ $advanceReceive->expired_date }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">ID Customer</label>
                        <input name="customer-id" class="form-control" value="{{ $advanceReceive->customers[0]->customer_id }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">Nama Customer</label>
                        <input name="customer-name" class="form-control" value="{{ $advanceReceive->customers[0]->name }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">Tipe</label>
                        <input name="type" class="form-control" value="{{ ucfirst(strtolower($advanceReceive->type)) }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">QTY Produk</label>
                        <input name="qty" class="form-control" value="{{ $advanceReceive->qty }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">Produk</label>
                        <input name="product" class="form-control" value="{{ $advanceReceive->products[0]->name }}" readonly>
                    </div>
                    <div class="mb-3 col-lg-12 col-12">
                        <label class="form-label">Kategori Produk</label>
                        <input name="category" class="form-control" value="{{ $advanceReceive->products[0]->categories[0]->name }}" readonly>
                    </div>
                    {{-- consumption --}}
                    @if(count($advanceReceive->consumptions[0]->history) > 0)
                        @foreach($advanceReceive->consumptions[0]->history as $key => $consumption)
                            <input type="hidden" value="{{ $consumption->id }}" name="consumption-id[]">
                            <div class="mb-3 col-lg-12 col-12">
                                <label class="form-label">Tanggal Consumption Advance Receive Ke-{{ $key+1 }}</label>
                                <input type="date" name="consumption-date[]" class="form-control" value="{{ $consumption->consumption_date }}">
                            </div>
                            <div class="mb-3 col-lg-12 col-12">
                                <label class="form-label">Tempat Consumption Advance Receive Ke-{{ $key+1 }}</label>
                                <select class="form-select" name="consumption-branch[]">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" @if($branch->id == $consumption->branch_id) selected @endif >{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-lg-12 col-12">
                                <button class="btn-delete btn btn-danger w-100" data-consumption="{{ $advanceReceive->id.'||'.$consumption->used_count.'||'.$consumption->id }}">Hapus Consumption Ke-{{ $key+1 }}</button>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-secondary mb-3 col-lg-12 col-12">
                            <p class="text-center m-0">Tidak Ada Consumption</p>
                        </div>
                    @endif
                    <button type="submit" class="btn btn-secondary col-12 col-lg-12 mb-3">Edit Consumption</button>
                    <a href="{{ URL::to('consumption') }}" class="btn btn-danger col-12 col-lg-12"> Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('custom-js')
    <script>
        $(".btn-delete").on("click", function(e) {
            e.preventDefault();
            let consumptionData = $(this).attr('data-consumption').split("||");
            let advanceReceiveID = consumptionData[0];
            let consumptionUsedCount = consumptionData[1];
            let consumptionID = consumptionData[2];

            $.ajax({
                url: "{{ URL::to('consumption/consumption-delete') }}",
                method: "POST",
                headers: {'X-CSRF-TOKEN': $('[name=_token]').val()},
                data: {
                    'advance-receive-id' : advanceReceiveID,
                    'consumption-id' : consumptionID,
                    'consumption-used-count' : consumptionUsedCount
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
                success: function (response) {
                    if (response.status === "failed"){
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus Consumption ke-'+consumptionUsedCount,
                            text: response.message,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        })
                        setTimeout(function () {
                            window.location.reload();
                        }, 1250)
                    }
                    if (response.status === "success"){
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
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

        })
    </script>
@endsection
