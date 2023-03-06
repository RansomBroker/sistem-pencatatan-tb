@extends('master')
@section('title', 'Edit Advance Receive')
@section('content')
    <div class="container-fluid p-0">
        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Edit Advance Receive Baru</h2>
        </div>

        {{-- Form --}}
        <div class="card card-body">
            <form method="POST" action="{{ URL::to('advance-receive/advance-receive-edit/edit') }}">
                {{-- csrf --}}
                @csrf
                <div class="row">
                    <input type="hidden" value="{{$advanceReceive->id}}" name="id">
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Cabang penjualan <sup class="text-danger">(Required)</sup></label>
                        <select class="form-select @error('branch') is-invalid @enderror" name="branch" >
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @if($advanceReceive->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Tanggal Penjualan <sup class="text-danger">(Required)</sup></label>
                        <input type="date" data-type="buy-date" class="form-control @error('buy-date') is-invalid @enderror" name="buy-date" value="{{ $advanceReceive->buy_date }}"/>
                        @error('buy-date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Expired Date <sup class="text-danger">(Required)</sup></label>
                        <input type="date" data-target="expired-date" class="form-control @error('expired-date') is-invalid @enderror" name="expired-date" value="{{ $advanceReceive->expired_date }}"/>
                        @error('expired-date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">ID Customer <sup class="text-danger">(Required)</sup></label>
                        <input type="text" data-type="customer-id" class="form-control @error('customer-id') is-invalid @enderror" name="customer-id" value="{{ $advanceReceive->customers[0]->customer_id }}" placeholder="Ketikan ID Customer"/>
                        @error('customer-id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">Nama Customer</label>
                        <input type="text" data-type="customer-name" class="form-control @error('customer-name') is-invalid @enderror" name="customer-name" value="{{ $advanceReceive->customers[0]->name }}" readonly/>
                        <div class="spinner-border text-primary d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        @error('customer-name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Tipe <sup class="text-danger">(Required)</sup></label>
                        <select class="form-select @error('type') is-invalid @enderror" name="type" >
                            <option value="NEW" @if($advanceReceive->type == "NEW") selected @endif>New</option>
                            <option value="REPEATER" @if($advanceReceive->type == "REPEATER") selected @endif>Repeater</option>
                        </select>
                        @error('type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">IDR Harga Beli <sup class="text-danger">(Required)</sup></label>
                        <input type="text" data-type="currency" class="form-control @error('buy-price') is-invalid @enderror" name="buy-price" value="{{ $advanceReceive->buy_price }}"/>
                        @error('buy-price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="d-block form-label">Include PPN </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('tax-include') is-invalid @enderror" type="radio" name="tax-include" value="1" checked/>
                            <label class="form-check-label">ya</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('tax-include') is-invalid @enderror" type="radio" name="tax-include" value="0"/>
                            <label class="form-check-label">tidak</label>
                        </div>
                        @error('tax-include')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">IDR Net Sales <sup class="text-danger">(Otomatis)</sup></label>
                        <input type="text" data-type="net-sales" class="form-control @error('net-sales') is-invalid @enderror" name="net-sales" value="{{ $advanceReceive->net_sale }}" readonly/>
                        @error('net-sales')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">IDR PPN <sup class="text-danger">(Otomatis)</sup></label>
                        <input type="text" data-type="tax" class="form-control @error('tax') is-invalid @enderror" name="tax" value="{{ $advanceReceive->tax }}" readonly/>
                        @error('tax')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">Pembayaran <sup class="text-danger">(Required)</sup></label>
                        <input type="text" class="form-control @error('payment') is-invalid @enderror" name="payment" value="{{ $advanceReceive->payment }}" />
                        @error('payment')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">QTY Produk <sup class="text-danger">(Required)</sup></label>
                        <input type="number" data-type="qty" class="form-control @error('qty') is-invalid @enderror" name="qty" value="{{ $advanceReceive->qty }}" />
                        @error('qty')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">IDR Harga Satuan <sup class="text-danger">(Otomatis)</sup></label>
                        <input type="text" data-type="unit-price" class="form-control @error('unit-price') is-invalid @enderror" name="unit-price" value="{{ $advanceReceive->unit_price }}" readonly/>
                        @error('unit-price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label class="form-label">Produk <sup class="text-danger">(Required)</sup></label>
                        <select class="form-select @error('product') is-invalid @enderror" data-type="product-select" name="product" >
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @if($advanceReceive->product_id == $product->id) selected @endif>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">Kategori Paket <sup class="text-danger">(Otomatis)</sup></label>
                        <input type="text" data-target="category" class="form-control @error('category') is-invalid @enderror" name="category" value="{{ $advanceReceive->products[0]->categories[0]->name }}" readonly/>
                        @error('category')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3 col-12 col-lg-12 d-flex flex-column">
                        <label class="form-label">Notes<sup class="text-danger">(Optional)</sup></label>
                        <input type="text" data-type="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" value="{{ $advanceReceive->notes }}"/>
                        @error('notes')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-secondary col-12 col-lg-12 mb-3">Simpan Perubahan</button>
                    <a href="{{ URL::to('advance-receive') }}" class="btn btn-danger col-12 col-lg-12 mb-3">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('custom-js')
    <script>
        // initiate data
        let initiate = 0;
        let currBuyPrice = $("input[name=buy-price]").val().split('.')[0];
        let currNetSales = $("input[data-type=net-sales]").val().split('.')[0];
        let currTax = $("input[data-type=tax]").val().split('.')[0];
        let currUnitPrice = $("input[data-type=unit-price]").val().split('.')[0];
        $("input[name=buy-price]").val(formatCurrencyPrice(currBuyPrice));
        $("input[data-type=net-sales]").val(formatCurrencyPrice(currNetSales));
        $("input[data-type=tax]").val(formatCurrencyPrice(currTax));
        $("input[data-type=unit-price]").val(formatCurrencyPrice(currUnitPrice));

        $("input[data-type=buy-date]").change(function() {
            let expiredDate = new Date($(this).val())
            expiredDate.setFullYear(expiredDate.getFullYear() + 2);
            let expiredDateFormat = expiredDate.toISOString().split('T')[0]
            $("input[data-target=expired-date]").val(expiredDateFormat);
        });

        $("input[data-type=customer-id]").on('keyup', function () {
            $("input[data-type=customer-name]").addClass('d-none')
            $('.spinner-border').removeClass('d-none')
            fetch('{{URL::to('customer-get')}}'+ '/' + $(this).val(), {
                method: "GET"
            })
                .then(response => response.json())
                .then(result => {
                    console.log(result)
                    if (result.data === null) {
                        $("input[data-type=customer-name]").removeClass('d-none')
                        $('.spinner-border').addClass('d-none')
                        $("input[data-type=customer-name]").attr('placeholder', 'data tidak ditemukan (pastikan customer telah terdapat pada master data)')
                        $("input[data-type=customer-name]").attr('value', '')
                    }
                    $("input[data-type=customer-name]").removeClass('d-none')
                    $('.spinner-border').addClass('d-none')
                    $("input[data-type=customer-name]").attr('value', result.data.name)
                })
        })

        $("input[data-type=customer-id]").focusout(function () {
            $("input[data-type=customer-name]").removeClass('d-none')
            $('.spinner-border').addClass('d-none')
        })

        $("select[data-type=product-select]").change(function () {
            let data = $(this).val();

            fetch("{{ URL::to('category-get') }}" + '/' + data, {
                method: "GET"
            })
                .then(response => response.json())
                .then(result => {
                    if (result.data === null) {
                        $("input[data-target=category]").attr("placeholder", "Kategori Tidak Ditemukan")
                        $("input[data-target=category]").attr("value", '')
                    }
                    $("input[data-target=category]").attr("value", result.data.categories[0].name)
                })
                .catch(e => {
                    console.log(e)
                })
        })

        $("input[name=buy-price]").on('keyup', function () {
            // get real number
            const regex = /[,]/g;
            let buyPrice = $(this).val();
            let qty = $("input[data-type=qty]").val() || 0;
            let price = buyPrice.replace(regex, '');
            let isTaxInclude = $("input[name=tax-include]").val()
            let netSale = 0;
            let tax = 0;
            let unitPrice = 0;
            /* if radio button was selected first*/
            if (isTaxInclude == 1) {
                netSale = parseInt(price/1.11);
                tax = price - netSale;
                unitPrice = parseInt(netSale/qty) || 0;
                /* set to input value placeholder */
                $("input[data-type=net-sales]").val(formatCurrencyPrice(netSale))
                $("input[data-type=tax]").val(formatCurrencyPrice(tax))
                $("input[data-type=unit-price]").val(formatCurrencyPrice(unitPrice))
            } else {
                netSale = parseInt(price);
                unitPrice = parseInt(netSale/qty) || 0;
                /* set to input value placeholder */
                $("input[data-type=net-sales]").val(formatCurrencyPrice(netSale))
                $("input[data-type=tax]").val(formatCurrencyPrice(tax))
                $("input[data-type=unit-price]").val(formatCurrencyPrice(unitPrice))
            }
        })

        $("input[name=tax-include]").change(function () {
            const regex = /[,]/g;
            let isTaxInclude = $(this).val()
            let qty = $("input[data-type=qty]").val() || 0;
            let price = $("input[name=buy-price]").val();
            let buyPrice =  price.replace(regex, '');
            let netSale = 0;
            let tax = 0;
            let unitPrice = 0

            if (isTaxInclude == 1) {
                netSale = parseInt(buyPrice/1.11);
                tax = buyPrice - netSale;
                unitPrice = parseInt(netSale/qty) || 0;
                /* set to input value placeholder */
                $("input[data-type=net-sales]").val(formatCurrencyPrice(netSale))
                $("input[data-type=tax]").val(formatCurrencyPrice(tax))
                $("input[data-type=unit-price]").val(formatCurrencyPrice(unitPrice))
            } else {
                netSale = parseInt(buyPrice);
                unitPrice = parseInt(netSale/qty) || 0;
                /* set to input value placeholder */
                $("input[data-type=net-sales]").val(formatCurrencyPrice(netSale))
                $("input[data-type=tax]").val(formatCurrencyPrice(tax))
                $("input[data-type=unit-price]").val(formatCurrencyPrice(unitPrice))
            }
        })

        $("input[data-type=qty]").on('keyup', function () {
            const regex = /[,]/g;
            let qty = $(this).val();
            let netSalesRaw = $("input[data-type=net-sales]").val();
            let netSale = netSalesRaw.replace(regex, '').split('.')[0];
            let unitPrice = parseInt(netSale/qty) || 0;
            $("input[data-type=unit-price]").val(formatCurrencyPrice(unitPrice))
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




    </script>
@endsection
