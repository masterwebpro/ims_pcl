<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delivery Slip</title>
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
    <h5 class="text-center text-uppercase mt-1">Delivery Slip</h5>
    <br>
    <table class="table table-info">
        <thead class="text-bold" style="font-style: bold;">
            <tr>
                <td scope="col" style="width: 120px;">Batch No</td>
                <td scope="col">:</td>
                <td scope="col" class="text-capitalize" style="width: 250px;">{{ $dispatch->dispatch_no }}</td>
                <td scope="col"></td>
                <td scope="col"  class="text-end">Date</td>
                <td scope="col">:</td>
                <td scope="col" class="text-start">{{ date('m/d/Y') }}</td>
            </tr>
            <tr>
                <td scope="col">Truck Seal</td>
                <td scope="col">:</td>
                <td scope="col" class="text-capitalize">
                    {{ $dispatch->seal_no }}
                </td>
                <td scope="col"></td>
                <td scope="col" class="text-end border border-bottom-solid">Plate No</td>
                <td scope="col">:</td>
                <td scope="col" class="text-start">{{ $dispatch->plate_no }}</td>
            </tr>
            <tr>
                <td scope="col">Trucker Name</td>
                <td scope="col">:</td>
                <td scope="col">{{ $dispatch->trucker_name }}</td>
                <td scope="col"></td>
                <td scope="col" class="text-end">Truck Type</td>
                <td scope="col">:</td>
                <td scope="col" class="text-start">{{ $dispatch->truck_type }}</td>
            </tr>
        </thead>
    </table>
    <table class="table-data">
        <thead class="bg-dark">
            <tr class="text-capitalize">
                <th scope="col">#</th>
                <th scope="col">Deliver To</th>
                <th scope="col">WD No.</th>
                <th scope="col">Order No.</th>
                <th scope="col">Order Date</th>
                <th scope="col" class="text-center">DR No.</th>
                <th scope="col" class="text-center">PO No.</th>
                <th scope="col" class="text-center">S.I No.</th>
                <th scope="col" class="text-center">Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?
            $rowCount = count($dispatch->items);
            $x=1;
            ?>
            @if(isset($dispatch->items))
                @foreach($dispatch->items as $item)
                <tr id="product_{{$item->wd_no}}">
                    <td class="align-top">
                    {{$x++}} </td>
                    <td class="align-top fs-14">
                        {{$item->deliver_to}}
                    </td>
                    <td class="align-top fs-14">
                        {{$item->wd_no}}
                    </td>
                    <td class="text-start fs-14">
                        {{$item->order_no}}
                    </td>
                    <td class="text-start fs-14">
                        {{ date('M d, Y', strtotime($item->order_date)) }}
                    </td>
                    <td class="text-start fs-14">
                        {{$item->dr_no}}
                    </td>
                    <td class="text-start fs-14">
                        {{$item->po_num}}
                    </td>
                    <td class="text-start fs-14">
                        {{$item->sales_invoice}}
                    </td>
                    <td class="ps-1 text-end">
                        {{ number_format($item->dispatch_qty,2) }}
                    </td>
                </tr>
                @endforeach
                <tr id="product_{{$item->product_id}}">
                    <td class="text-end" colspan="8">
                        TOTAL
                    </td>
                    <td class="text-end fs-14">
                        @php
                            $totalInvQty = 0;
                            foreach ($dispatch->items as $item) {
                                $totalInvQty += $item->dispatch_qty;
                            }
                        @endphp
                        {{ number_format($totalInvQty,2) }}
                    </td>
                </tr>
            @else
            <tr class="">
                <td colspan="9" class="text-danger text-center">No Record Found!</td>
            </tr>
            @endif
        </tbody>
    </table>
    <footer>
        <div class="row">
            <div class="col-4">
                <h5>Remarks</h5>
                <div class="horizontal-line mb-3"></div>
                <div class="horizontal-line"></div>
            </div>
        </div>
        <br/>
        <div class="row">
            <table>
                <tr>
                    <td>Prepared by: </td>
                    <td></td>
                    <td>Approved by:</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="mb-2">
                    <td class="align-top text-right">
                        &nbsp; &nbsp; &nbsp; &nbsp;{{ $created_by }}
                        <hr style="border: 1px solid #000;"/>
                    </td>
                    <td style="width: 40%;"></td>
                    <td style="width: 40%;" class="align-top text-left">
                        <br/>
                        <hr style="border: 1px solid #000;"/>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="width: 50%;"> Received the above in good condition and complete.</td>
                    <td></td>
                    <td>Received by:</td>
                </tr>
                <tr>
                    <td><br/></td>
                    <td><br/></td>
                    <td><br/></td>
                </tr>
                <tr>
                    <td class="text-left">
                        <hr style="border: 1px solid #000;"/>
                        Signature over Printed Name / Date
                    </td>
                    <td></td>
                    <td>
                        <hr style="border: 1px solid #000;"/>
                        Signature over Printed Name / Date
                    </td>
                </tr>
            </table>
        </div>
        <br/>
        <div style="text-align: center;">
            <em>Page <span class="page-number"></span> of <span class="page-number"></span></em>
        </div>
    </footer>
</body>
</html>
