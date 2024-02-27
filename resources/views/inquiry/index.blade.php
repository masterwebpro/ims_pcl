@extends('layouts.master')
@section('title') Report @endsection
@section('css')

<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">


@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Inquiry @endslot
@slot('title') Item Inquiry @endslot
@endcomponent

<!--end row-->
<form action="{{ route('inquiry.index') }}" method="GET">
    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row">
                                 <div class="col-lg-6 col-md-6 ">
                                    <h6 class="text-muted text-uppercase fw-semibold">Products <span class="text-danger">*</span></h6>
                                    <p class=" mb-2">
                                        <input type="hidden" class="form-control" name="product_id" id="product_id">
                                        <input type="text" class="form-control" dir="ltr" spellcheck=false autocomplete="off" autocapitalize="off" name="product_holder" id="product_holder" value="" placeholder="Type PO Name or Code">
                                        <span class="text-danger error-msg product_id_error"></span>
                                     </p>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <h6 class="text-muted text-uppercase fw-semibold ">&nbsp;</h6>
                                    <button type="submit" class="btn btn-primary w-100"> <i
                                            class="ri-equalizer-fill me-1 align-bottom"></i>
                                        Filters
                                    </button>
                                </div>
                                 <!--end col-->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <th>Date</th>
                                    <th>Trans Type</th>
                                    <th>Reference No</th>
                                    <th class="text-center bg-success">RCV Quantity</th>
                                    <th class="text-center bg-warning">WD Quantity</th>
                                    <th class="text-center bg-danger">Dispatch Quantity</th>
                                    <th class="text-center bg-info">Reserve Quantity</th>
                                </thead>
                                <tbody>
                                    @php
                                        $rcv = 0;
                                        $tot_wd = 0;
                                        $dispatch = 0;
                                        $reserve = 0;
                                    @endphp
                                    @foreach ($data as $row)
                                        <tr>
                                            <td>{{ $row['date_received'] }}</td>
                                            <td>RCV</td>
                                            <td>{{ $row['rcv_no'] }}</td>
                                            <td class="text-end bg-success">{{ number_format($row['inv_qty'],2) }}</td>
                                            <td class="bg-warning"></td>
                                            <td class="bg-danger"></td>
                                            <td class="bg-info"></td>
                                        </tr>
                                        @php
                                        $bal = $row['inv_qty'];
                                        $rcv += $row['inv_qty'];
                                        $wd = 0;
                                        $dqty = 0;
                                        $rsv = 0;
                                        @endphp
                                        @foreach ($row['withdraw'] as $col)
                                            <tr>
                                                <td>{{ date('M d, Y',strtotime($col['withdraw_date'])) }}</td>
                                                <td>{{ ($col['status'] == 'posted') ? 'WD' : 'RESERVE' }}</td>
                                                <td>{{ $col['wd_no'] }}</td>
                                                <td class="text-end bg-success"></td>
                                                <td class="text-end bg-warning">{{ number_format($col['inv_qty'],2) }}</td>
                                                <td class="text-end bg-danger">{{ number_format($col['dispatch_qty'],2) }}</td>
                                                <td class="text-end bg-info">{{ number_format($col['inv_qty'] - $col['dispatch_qty'],2) }}</td>
                                            </tr>
                                            @php
                                                $bal -= $col['inv_qty'];
                                                $wd += $col['inv_qty'];
                                                $dqty += $col['dispatch_qty'];
                                                $rsv += $col['inv_qty'] - $col['dispatch_qty'];
                                            @endphp
                                        @endforeach
                                        <tr class="bg-light">
                                            <td colspan="2" class="text-end"></td>
                                            <td>Total Balance</td>
                                            <td class="text-end">{{ number_format($row['inv_qty'],2) }}</td>
                                            <td class="text-end">{{ number_format($wd,2) }}</td>
                                            <td class="text-end">{{ number_format($dqty,2) }}</td>
                                            <td class="text-end">{{ number_format($rsv,2) }}</td>
                                        </tr>
                                        @php
                                                $tot_wd += $wd;
                                                $dispatch += $dqty;
                                                $reserve += $rsv;
                                        @endphp
                                    @endforeach
                                    <tr class="bg-primary text-white">
                                        <td rowspan="2" colspan="3" class="text-center align-middle fs-3">Remaining Balance</td>
                                        <td class="text-end">{{ number_format($rcv,2) }}</td>
                                        <td class="text-end">{{ number_format($tot_wd,2) }}</td>
                                        <td rowspan="2"class="text-end">{{ number_format($dispatch,2) }}</td>
                                        <td rowspan="2"class="text-end">{{ number_format($reserve,2) }}</td>
                                    </tr>
                                    <tr class="bg-primary text-white">
                                        <td colspan="2" class="text-center text-white">{{ number_format($rcv - $tot_wd,2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/masks/jquery.mask.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/moment/moment.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

<!-- autocomplete js -->
<script src="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/inquiry/inquiry.js') }}"></script>

@endsection
