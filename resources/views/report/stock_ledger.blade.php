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
@slot('li_1') Report @endslot
@slot('title') Stock Ledger @endslot
@endcomponent

<!--end row-->
<form name="submit-receive" id="submit-ledger">
    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-3 mb-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name <span class="text-danger">*</span></h6>
                                    <p class=" mb-2">
                                        <select class="form-select select2" id="client" name="client">
                                            <option value="">Select Client</option>
                                            <? foreach($client_list as $client) : ?>
                                                <option value="<?=$client->id?>" ><?=$client->client_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Site Address <span class="text-danger">*</span></h6>
                                    <p class=" mb-2">
                                        <select class="form-select select2" id="store" name="store">
                                            <option value="">Select Store/Warehouse</option>
                                        </select>
                                        <span class="text-danger error-msg store_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse</h6>
                                    <p class="mb-2">
                                        <select class="form-select select2" id="warehouse" name="warehouse">
                                            <option value="">Select warehouse</option>
                                        </select>
                                        <span class="text-danger error-msg warehouse_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Location</h6>
                                    <p class="mb-2" >
                                        <select class="form-select select2" id="location" name="location">
                                            <option value="">Select Location</option>
                                        </select>
                                        <span class="text-danger error-msg location_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->
                            </div>
                            <div class="row">

                                <div class="col-lg-4 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold">Date Covered <span class="text-danger">*</span></h6>
                                        <input type="text" class="form-control" name="date" id="date_picker"
                                        data-provider="flatpickr" data-date-format="Y-m-d" value="<?=isset($request->date) ? $request->date : date("Y-m-d", strtotime("-3 months"))." to ".date('Y-m-d')?>" data-range-date="true"
                                        placeholder="Select date range" data-maxDate="<?=date('Y-m-d')?>"
                                       >
                                        <span class="text-danger error-msg date_range_error"></span>

                                </div>

                                 <div class="col-lg-5 col-md-4 ">
                                    <h6 class="text-muted text-uppercase fw-semibold">Products <span class="text-danger">*</span></h6>
                                    <p class=" mb-2">
                                        <input type="hidden" class="form-control" name="product_id" id="product_id">
                                        <input type="text" class="form-control" dir="ltr" spellcheck=false autocomplete="off" autocapitalize="off" name="product_holder" id="product_holder" value="" placeholder="Type PO Name or Code">
                                        <span class="text-danger error-msg product_id_error"></span>
                                     </p>
                                </div>
                                 <!--end col-->
                                 <div class="col-lg-2 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold ">Item Type <span class="text-danger">*</span></h6>
                                    <p class=" mb-2">
                                        <select class="form-select select2" id="item_type" name="item_type">
                                            <option value="">Select Item Type</option>
                                            @foreach ($item_type as $item)
                                                <option value="{{ $item->code }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-msg item_type_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->

                                <div class="col-lg-6 col-md-6">
                                    <h6 class="text-muted text-uppercase fw-semibold ">&nbsp;</h6>
                                    <a href="#" class="submit-stock-ledger btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search</a>
                                    <!-- <a href="#" class="submit-xls btn btn-secondary btn-label rounded-pill"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i> Export to Excel</a>
                                    <a href="#" class="submit-print btn btn-primary btn-label rounded-pill"><i class="ri-printer-line label-icon align-middle rounded-pill fs-16 me-2"></i> Print</a> -->
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
        <div class="col-xxl-12">
            <div class="card" id="demo">
                <div class="card-body p-4 ">
                    <div class="row ">
                        <table id="masterfile_list" style="font-size: 11px;"  width="100%"  class="table table-striped table-bordered table-hover align-middle">
                            <thead  class="table-light">
                                <tr>
                                    <th class="fw-medium text-center">Date</th>
                                    <th class="fw-medium text-center">Transaction</th>
                                    <th class="fw-medium text-center">Reference No</th>
                                    <th class="fw-medium text-center">Location</th>
                                    <th class="fw-medium text-center">Type</th>
                                    <th class="fw-medium text-center">Received QTY</th>
                                    <th class="fw-medium text-center">Transfer QTY</th>
                                    <th class="fw-medium text-center">Withdrawal QTY</th>
                                    <th class="fw-medium text-center">Reserved QTY</th>
                                    <th class="fw-medium text-center">Balance</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
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

<!-- autocomplete js -->
<script src="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/report/stock-ledger.js') }}"></script>

@endsection
