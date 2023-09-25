<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Withdrawal Summary report</title>
  <link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap-v4.css') }}">
  
  <style>
    table, tr, td {
        border: 0px solid #f00;
    }
    body {
        font-family: Arial, Helvetica, sans-serif;
    }
    
    table.border_customer th {
        border: 1px solid #000000;
        background-color: #d1d1d1;
        padding: 4px;
    }

    table.border_customer td {
        border: 1px solid #000000;
        padding: 4px;
        text-transform: uppercase;
    }

    table.border th {
        border: 1px solid #000000;
        background-color: #d1d1d1;
        padding-left: 5px;
    
    }

    table.border td {
        border: 1px solid #000000;
        padding-left: 5px;
    }

    table.condition td {
        padding: 2px 0px;
        text-align: justify;
    }

    table.car td {
        padding: 0 3px;
    }
    table.car th {
        padding: 0 3px;
    }

    .padding {
        padding: 0 3px;
    } 

    .main {
        margin-top: -20px;
    }

    .title {
        font-size:12px;
    }
    .address {
        font-size:8px;
    }
    .small {
        font-size:11px;
        line-height: 16px;
    }
    .bold {
        font-weight: bold;
    }
    .sale_condition {
        font-size:8px;
    }

    table.sale_condition td {
        padding: 2px 0px;
        text-align: justify;
    }

    .condition_header {
        font-size:9px;
        font-weight: 500;
        padding: 3px 2px;
        margin: 2px 0;
    }
    .right {
        text-align: right;
        padding-right: 5px;
    }
    .left {
        text-align: left;
        padding-right: 10px;
    }
    .center {
        text-align: center;
    }
    .uppercase {
        text-transform: uppercase;
    }

    .double {
        border-bottom: 3px double #000;
        margin: 0 5px;
    }

    /** Define the footer rules **/
    footer {
        position: fixed;
        bottom: 0cm;
        left: 0cm;
        right: 0cm;
      
        /** Extra personal styles **/
        color: black;
        text-align: center;
    }
    </style>
</head>
<body>
    <div class="main">
        <table width="100%">
            <tr>
                <td width="55%" colspan="2" valign="top">
                    <div class="fs-3">
                        <img src="{{ public_path('assets/images/pcl_logo.png') }}" height="60px" height="50px">
                    </div>
                </td>
            </tr>
        </table> 
        
        <table width="100%">
            <tr>
                <td width="55%" colspan="2" valign="top">
                    <div class="center" style="margin: 10px 0px 10px 0px; font-weight: bold;">
                        WITHDRAWAL DETAILED REPORT
                    </div>
                </td>
            </tr>
        </table>

        <table width="100%" class="small border">
            <tr class="bold">
                <th class="fw-medium text-center" width="80px;">Date Withdraw</th>
                <th class="fw-medium text-center" width="75px;">Reference No</th>
                <th class="fw-medium text-center">Order No</th>
                <th class="fw-medium text-center">Order Type</th>
                <th class="fw-medium text-center">DR No</th>
                <th class="fw-medium text-center">Sales Invoice</th>
                <th class="fw-medium text-center">PO No</th>
                <th class="fw-medium text-center" width="50px;">Product Code</th>
                <th class="fw-medium text-center">Product Description</th>
                <th class="fw-medium text-center" width="65px;">Inv / UOM</th>
            </tr>
            <? foreach($result as $rec): ?>
            <tr>
                <td>{{date('M d, Y', strtotime($rec->withdraw_date)) }}</td>
                <td>{{$rec->wd_no}}</td>
                <td>{{$rec->order_no}}</td>
                <td>{{$rec->order_type}}</td>
                <td>{{$rec->dr_no}}</td>
                <td>{{$rec->sales_invoice}}</td>
                <td>{{$rec->po_num}}</td>
                <td width='100px;'>{{$rec->product_code}}</td>
                <td class="left">{{$rec->product_name}}</td>
                <td class="right">{{$rec->inv_qty}} / {{$rec->ui_code}}</td>
            </tr>
            <? endforeach;?>
        </table> 
    </div>
    <footer>
        <div style="font-size: 8px" class="left">Run Date: <?=date("Ymd-His");?></div>
    </footer>
</body>
</html>