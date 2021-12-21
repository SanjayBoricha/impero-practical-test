@extends('layouts.default')

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@dashboardcode/bsmultiselect@1.1.18/dist/css/BsMultiSelect.min.css">
@endpush

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap mb-4">
            @foreach($paymentTypes as $type)
                <div class="card me-2 mb-2">
                    <div class="card-body">
                        <b class="text-capitalize">{{ $type }}</b> <br>
                        
                        Total: <span data-key="{{ $type }}Total">$0.00</span> <br>
                        Percentage: <span data-key="{{ $type }}Percentage">0.00%</span> 
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-start mb-4">
            <div class="flex-shrink">
                <label for="dateRange" class="form-label">Order date range</label>
                <input id="dateRange" type="text" name="daterange" value="{{ $rangeStart }} - {{ $rangeEnd }}" class="form-control" />
            </div>
            <div class="flex-shrink ms-4">
                <label for="productSelect" class="form-label">Products select</label>

                <select id="productSelect" class="selectpicker" multiple aria-label="size 3 select example">
                    @foreach($productNames as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <table id="example" class="table table-striped table-bordered" style="width:100%;margin-top:1rem !important">
            <thead>
                <tr>
                    <th>Order id</th>
                    <th>Pin type</th>
                    <th>Payment type</th>
                    <th>Customer name</th>
                    <th>Full address</th>
                    <th>Order date</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Product name</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/@dashboardcode/bsmultiselect@1.1.18/dist/js/BsMultiSelect.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dateRange').daterangepicker();

            $('#productSelect').bsMultiSelect();

            var formatter = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            });

            var table = $('#example').DataTable({
                ajax: {
                    url: "{{ route('orders:datatable') }}",
                    type: "GET",
                    data: function(d) {
                        d.range = $('#dateRange').val()
                        d.search = $('label > input[type="search"]').val()
                        d.products = $('#productSelect').val()
                    },
                    dataFilter: function (data) {
                        data = jQuery.parseJSON(data);

                        for (key in data.totals) {
                            $(`[data-key="${key}"]`).html(formatter.format(data.totals[key]))
                        }

                        for (key in data.percentages) {
                            $(`[data-key="${key}"]`).html(String(parseFloat(data.percentages[key]).toFixed(2)) + "%")
                        }

                        return JSON.stringify(data);
                    }.bind(this)
                },
                processing: true,
                serverSide: true,
                columns: [
                    { data: "order_id" },
                    { data: "pin_type" },
                    { data: "payment_type" },
                    { data: "customer_name" },
                    { data: "full_address" },
                    { data: "order_date" },
                    { data: "price" },
                    { data: "quantity" },
                    { data: "product_name" },
                ],
            });

            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                table.ajax.reload();
            })

            $('#productSelect').change(function() {
                table.ajax.reload();
            })
        } );
    </script>
@endpush