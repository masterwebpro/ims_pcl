<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PickList</title>
    <link href="{{ public_path('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
        .table-data {
            width: 100%;
        }
        .table-data th {
        border: .5px solid black;
        padding: 8px;
        text-align: left;
        background-color: darkgrey;
        }

        /* Style for table cells */
        .table-data td {
        border: .5px solid black;
        padding: 8px;
        }
        img.centered {
        display: block;
        margin-left: auto;
        margin-right: auto;
        }
        footer {
            color: black;
            text-align: center;
            padding: 10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        .horizontal-line {
            border-bottom: 1px solid black;
            margin: 20px 0; /* Add some margin for spacing */
        }
        .signature-line{
            border-bottom: 1px solid black;
            width: 135px;
        }
        .page-number:before  {
            content: counter(page);
        }
    </style>
<body>
    <div class="text-center">
        <img src="{{ public_path('assets/images/pcl_logo.png') }}" class="centered" height="60">
    </div>
    <h5 class="text-center text-uppercase mt-1">Picklist</h5>
    <br>
    <table class="table table-info">
        <thead class="text-bold" style="font-style: bold;">
            <tr>
                <td scope="col" style="width: 120px;">Client Name</td>
                <td scope="col">:</td>
                <td scope="col" class="text-capitalize">{{ $wd->client_name }}</td>
                <td scope="col"></td>
                <td scope="col"  class="text-end">Order Date</td>
                <td scope="col">:</td>
                <td scope="col" class="text-end">{{ date('m/d/Y',strtotime($wd->order_date)) }}</td>
            </tr>
            <tr>
                <td scope="col">Mode of Withdrawal</td>
                <td scope="col">:</td>
                <td scope="col" class="text-capitalize">{{ $wd->wd_type }}</td>
                <td scope="col"></td>
                <td scope="col" class="text-end">Order Number</td>
                <td scope="col">:</td>
                <td scope="col" class="text-end">{{ $wd->order_no }}</td>
            </tr>
            <tr>
                <td scope="col"></td>
                <td scope="col"></td>
                <td scope="col"></td>
                <td scope="col"></td>
                <td scope="col" class="text-end">PO Number</td>
                <td scope="col">:</td>
                <td scope="col" class="text-end">{{ $wd->po_num }}</td>
            </tr>
        </thead>
    </table>
    <table class="table-data">
        <thead class="bg-dark">
            <tr class="text-capitalize">
                <th scope="col">#</th>
                <th scope="col">Code</th>
                <th scope="col">Item Description</th>
                <th scope="col" class="text-center">Quantity</th>
                <th scope="col">Unit</th>
                <th scope="col">Warehouse</th>
                <th scope="col">Location</th>
            </tr>
        </thead>
        <tbody>
            <?
            $rowCount = count($wd->items);
            $x=1;
            ?>
            @if(isset($wd->items))
                @foreach($wd->items as $item)
                <tr id="product_{{$item->product_id}}">
                    <td class="text-start">
                    {{$x++}} </td>
                    <td class="text-start fs-14">
                        {{$item->product->product_code}}
                    </td>
                    <td class="text-start fs-14">
                        {{$item->product->product_name}}
                    </td>
                    <td class="ps-1 text-end">
                        {{ number_format($item->inv_qty,2) }}
                    </td>
                    <td class=" ps-1">
                        {{ $item->master->uom->code }}
                    </td>
                    <td class=" ps-1">
                        {{ $item->master->warehouse->warehouse_name }}
                    </td>
                    <td class=" ps-1">
                        {{ $item->master->location->location }}
                    </td>
                </tr>
                @endforeach
                <tr id="product_{{$item->product_id}}">
                    <td class="text-end" colspan="3">
                        TOTAL
                    </td>
                    <td class="text-end fs-14">
                        @php
                            $totalInvQty = 0;
                            foreach ($wd->items as $item) {
                                $totalInvQty += $item->inv_qty;
                            }
                        @endphp
                        {{ number_format($totalInvQty,2) }}
                    </td>
                    <td class="ps-1" colspan="3">
                    </td>
                </tr>
            @else
            <tr class="">
                <td colspan="8" class="text-danger text-center">No Record Found!</td>
            </tr>
            @endif
        </tbody>
    </table>
    <br/>
    <br/>
    <div class="row">
        <div class="col-4">
            <label for="">Remarks:</label>
            <br/>
            <div class="horizontal-line mb-3"></div>
            <div class="horizontal-line"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="mb-4">Picked by:</label>
            <div class="signature-line mb-1"></div>
            <label for="">Printed Name and Signature</label>
        </div>
    </div>
    <footer>
        <div style="text-align: center;">
            <em>Page <span class="page-number"></span> of <span class="page-number"></span></em>
        </div>
    </footer>
</body>
</html>
