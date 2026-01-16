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
@slot('title') Dispatch Detailed @endslot
@endcomponent
<form action="#" name="submit-withdrawal" id="submit-withdrawal">
    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Dispatch #</h6>
                                    <p class="fw-medium mb-2">
                                        <input type="text" class="form-control" placeholder="Dispatch No" name="dispatch_no" id="dispatch_no" value="{{$request->dispatch_no}}">
                                        <span class="text-danger error-msg dispatch_no_error"></span>
                                     </p>
                                </div>
                                {{-- <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name</h6>
                                    <p class=" mb-2" id="billing-name">
                                        <select class="form-select select2" id="client" name="client">
                                            <option value="">Select Client</option>
                                            <? foreach($client_list as $client) : ?>
                                                <option value="<?=$client->id?>" <?=($request->client == $client->id) ? 'selected' : ''?> ><?=$client->client_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Site Address </h6>
                                    <p class=" mb-2" id="shipping-name">
                                        <input type="hidden" name="store_id" id="store_id" value="{{$request->store}}">
                                        <select class="form-select select2" id="store" name="store">
                                            <option value="">Select Store/Warehouse</option>
                                        </select>
                                        <span class="text-danger error-msg store_error"></span>
                                    </p>
                                </div> --}}
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Date Dispatch <small>(YYY-mm-dd)</small></h6>
                                    <input type="text" class="form-control" name="dispatch_date" id="dispatch_date" value = "{{$request->dispatch_date}}" data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" placeholder="Select date range">
                                </div>
                            {{-- </div>
                            <div class="row g-3 mt-1">
                                 <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Product Code</h6>
                                    <p class="fw-medium mb-2">
                                        <input type="text" class="form-control" placeholder="Item Code" name="product_code" id="product_code" value="{{$request->product_code}}">
                                        <span class="text-danger error-msg product_code_error"></span>
                                     </p>
                                </div>
                                 <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Product Name</h6>
                                    <p class="fw-medium mb-2">
                                        <input type="text" class="form-control" placeholder="Product Name" name="product_name" id="product_name" value="{{$request->product_name}}">
                                        <span class="text-danger error-msg product_name_error"></span>
                                     </p>
                                </div> --}}
                                <div class="col-lg-6 col-md-6 d-flex align-items-center">
                                    <h5 class="card-title flex-grow-1"></h5>
                                    <div class="">
                                        <button class="submit-dispatch-search btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search</button>
                                        <a href="#" class="submit-dispatch-xls btn btn-secondary btn-label rounded-pill"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i> Export to Excel</a>
                                        {{-- <a href="#" class="submit-dispatch-print btn btn-primary btn-label rounded-pill"><i class="ri-printer-line label-icon align-middle rounded-pill fs-16 me-2"></i> Print Preview</a> --}}
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
                                    <th class="fw-medium align-middle" rowspan="2">Date Dispatch</th>
                                    <th class="fw-medium align-middle" rowspan="2">Reference No</th>
                                    <th class="fw-medium align-middle" rowspan="2">Withdrawal No</th>
                                    <th class="fw-medium align-middle" rowspan="2">Plate No</th>
                                    <th class="fw-medium align-middle" rowspan="2">Truck Type</th>
                                    <th class="fw-medium align-middle" rowspan="2">Trucker Name</th>
                                    <th class="fw-medium align-middle" rowspan="2">Seal No</th>
                                    <th class="fw-medium align-middle" rowspan="2">Dispatch By</th>
                                    <th class="fw-medium align-middle" rowspan="2">Driver/Helper Name</th>
                                    <th class="fw-medium align-middle" rowspan="2">Start Picking</th>
                                    <th class="fw-medium align-middle" rowspan="2">Finish Picking</th>
                                    <th class="fw-medium align-middle" rowspan="2">Actual Truck Arrival</th>
                                    <th class="fw-medium align-middle" rowspan="2">Start Loading</th>
                                    <th class="fw-medium align-middle" rowspan="2">Finish Loading</th>
                                    <th class="fw-medium align-middle" rowspan="2">Depart Date/Time</th>
                                    <th class="fw-medium align-middle" rowspan="2">Product Code</th>
                                    <th class="fw-medium align-middle" rowspan="2">Product Description</th>
                                    <th class="fw-medium align-middle">Withdraw</th>
                                    <th class="fw-medium align-middle">Dispatch</th>
                                    <th class="fw-medium align-middle" rowspan="2">Order No</th>
                                    <th class="fw-medium align-middle" rowspan="2">Order Date</th>
                                    <th class="fw-medium align-middle" rowspan="2">DR Number</th>
                                    <th class="fw-medium align-middle" rowspan="2">Prepared By</th>
                                </tr>
                                <tr class="text-nowrap">
                                    <th class="fw-medium text-center" width="110px;">Quantity / UOM</th>
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
