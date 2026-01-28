@extends('layouts.master')
@section('title') Report @endsection
@section('css')

<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Report @endslot
@slot('title') Stock Transfer Detailed @endslot
@endcomponent
<form action="#" name="submit-transfer" id="submit-transfer">
    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Reference #</h6>
                                    <p class="fw-medium mb-2">
                                        <input type="text" class="form-control" placeholder="Reference No" name="transfer_no" id="transfer_no" value="{{$request->transfer_no}}">
                                        <span class="text-danger error-msg transfer_no_error"></span>
                                     </p>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Transfer Date <small>(YYY-mm-dd)</small></h6>
                                    <input type="text" class="form-control" name="transfer_date" id="transfer_date" value = "{{$request->transfer_date}}" data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" placeholder="Select date range">
                                </div>
                                <div class="col-lg-6 col-md-6 d-flex align-items-center">
                                    <h5 class="card-title flex-grow-1"></h5>
                                    <div class="">
                                        <button class="submit-transfer-search btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search</button>
                                        <a href="#" class="submit-transfer-xls btn btn-secondary btn-label rounded-pill"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i> Export to Excel</a>
                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-body-->
                    </div>

                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->

    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card" id="demo">
                <div class="card-body p-4 ">
                    <div class="table-responsive" id="load-search">
                        <table id="item_list" style="font-size: 12px;"  width="100%"  class="table table-striped table-bordered table-hover align-middle">
                            <thead  class="table-light">
                                <tr class="text-nowrap">
                                    <th class="fw-medium align-middle" rowspan="2">Transfer Date</th>
                                    <th class="fw-medium align-middle" rowspan="2">Reference No</th>
                                    <th class="fw-medium align-middle" rowspan="2">Company </th>
                                    <th class="fw-medium align-middle" rowspan="2">Site Name</th>
                                    <th class="fw-medium align-middle" rowspan="2">DR No</th>
                                    <th class="fw-medium align-middle" rowspan="2">Start Encoding</th>
                                    <th class="fw-medium align-middle" rowspan="2">Finish Encoding</th>
                                    <th class="fw-medium align-middle" rowspan="2">Product Details</th>
                                    <th class="fw-medium text-center" colspan="3">Old Location</th>
                                    <th class="fw-medium text-center" colspan="3">Target Location</th>
                                    <th class="fw-medium align-middle" rowspan="2">Remarks</th>
                                    <th class="fw-medium align-middle" rowspan="2">Request By</th>
                                    <th class="fw-medium align-middle" rowspan="2">Transfer By</th>
                                </tr>
                                <tr class="text-nowrap">
                                    <th class="fw-medium text-center" width="110px;">Item Type</th>
                                    <th class="fw-medium text-center" width="110px;">Location</th>
                                    <th class="fw-medium text-center" width="110px;">Quantity / UOM</th>
                                    <th class="fw-medium text-center" width="110px;">Item Type</th>
                                    <th class="fw-medium text-center" width="110px;">Location</th>
                                    <th class="fw-medium text-center" width="110px;">Quantity / UOM</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="row " id="load-data"></div>
                </div>
                <!--end row-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
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


<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/report/report.js') }}"></script>

@endsection
