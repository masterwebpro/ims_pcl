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
@slot('title') Receiving Detailed @endslot
@endcomponent
<form action="#" name="submit-receive" id="submit-receive">
    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Receiving #</h6>
                                    <p class="fw-medium mb-2">
                                        <input type="text" class="form-control" placeholder="Receiving No" name="rcv_no" id="rcv_no" value="{{$request->rcv_no}}">
                                        <span class="text-danger error-msg rcv_no_error"></span>
                                     </p>
                                </div>
                                <div class="col-lg-3 col-md-3">
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
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse</h6>
                                    <p class="mb-2" id="warehouse1">
                                        <input type="hidden" name="warehouse_id" id="warehouse_id" value="{{$request->warehouse}}">
                                        <select class="form-select select2" id="warehouse" name="warehouse">
                                            <option value="">Select warehouse</option>
                                        </select>
                                        <span class="text-danger error-msg warehouse_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Date Received <small>(YYY-mm-dd)</small></h6>
                                    <input type="text" class="form-control" name="date_received" id="date_received" value = "{{$request->date_received}}" data-provider="flatpickr" data-date-format="Y-m-d" data-range-date="true" placeholder="Select date range">
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Item Type </h6>
                                    <p class=" mb-2">
                                        <select class="form-select select2" id="item_type" name="item_type">
                                            <option value="good" <?=($request->item_type == 'good') ? 'selected' : ''?> >Good</option>
                                            <option value="damage" <?=($request->item_type == 'damage') ? 'selected' : ''?> >Damage</option>
                                            <option value="repair" <?=($request->item_type == 'repair') ? 'selected' : ''?> >Repair</option>
                                        </select>
                                        <span class="text-danger error-msg item_type_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->
                                 <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Product Code</h6>
                                    <p class="fw-medium mb-2">
                                        <input type="text" class="form-control" placeholder="Item Code" name="product_code" id="product_code" value="{{$request->product_code}}">
                                        <span class="text-danger error-msg product_code_error"></span>
                                     </p>
                                </div>
                                 <!--end col-->
                                 <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Product Name</h6>
                                    <p class="fw-medium mb-2">
                                        <input type="text" class="form-control" placeholder="Product Name" name="product_name" id="product_name" value="{{$request->product_name}}">
                                        <span class="text-danger error-msg product_name_error"></span>
                                     </p>
                                </div>
                                 <!--end col-->
                            </div>
                            <div class="row mt-2">
                                <div class="d-flex align-items-center">
                                    <h5 class="card-title flex-grow-1"></h5>
                                    <div class="">
                                        <button class="submit-receive-search btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search</button>
                                        <a href="#" class="submit-receive-xls btn btn-secondary btn-label rounded-pill"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i> Export to Excel</a>
                                        <a href="#" class="submit-receive-print btn btn-primary btn-label rounded-pill"><i class="ri-printer-line label-icon align-middle rounded-pill fs-16 me-2"></i> Print Preview</a>
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
                    <div class="row " id="load-search">
                        <table id="item_list" style="font-size: 12px;"  width="100%"  class="table table-striped table-bordered table-hover align-middle">
                            <thead  class="table-light">
                                <tr>
                                    <th class="fw-medium text-center">Date Received</th>
                                    <th class="fw-medium text-center">Reference No</th>
                                    <th class="fw-medium text-center">PO No</th>
                                    <th class="fw-medium text-center">Product Code</th>
                                    <th class="fw-medium text-center">Product Description</th>
                                    <th class="fw-medium text-center">Item Type</th>
                                    <th class="fw-medium text-center" width="110px;">Whse / UOM</th>
                                    <th class="fw-medium text-center" width="110px;">Inv / UOM</th>
                                    <th class="fw-medium text-center">Lot No</th>
                                    <th class="fw-medium text-center">Expiry Date</th>
                                    <th class="fw-medium text-center">Mfg. Date</th>
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
