@extends('layouts.master')
@section('title') Inventory Report @endsection
@section('css')

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Report @endslot
@slot('title') Inventory @endslot
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
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name</h6>
                                    <p class=" mb-2">
                                        <select class="form-select select2" id="company" name="company">
                                            <option value="">Select Company</option>
                                            <? foreach($client_list as $client) : ?>
                                                <? if(strtoupper($client->client_type) == 'O') : ?>
                                                <option value="<?=$client->id?>" ><?=$client->client_name?></option>
                                                <? endif;?>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg company_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-md-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Site Address</h6>
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
                            <div class="row mt-1">

                                 <div class="col-lg-4 col-md-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Products</h6>
                                    <p class=" mb-2">
                                        <input type="hidden" class="form-control" name="product_id" id="product_id">
                                        <input type="text" class="form-control" dir="ltr" spellcheck=false autocomplete="off" autocapitalize="off" name="product_holder" id="product_holder" value="" placeholder="Type PO Name or Code">
                                        <span class="text-danger error-msg product_id_error"></span>
                                     </p>
                                </div>
                                 <!--end col-->

                                 <div class="col-lg-2 col-md-2">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Item Type</h6>
                                    <p class=" mb-2">
                                        <select class="form-select select2" id="item_type" name="item_type">
                                            <option value="good">Good</option>
                                            <option value="damage">Damage</option>
                                            <option value="repair">Repair</option>
                                        </select>
                                        <span class="text-danger error-msg item_type_error"></span>
                                    </p>
                                </div>
                                 <!--end col-->

                                <div class="col-lg-6 col-md-6">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">&nbsp;</h6>
                                    <a href="#" class="submit-inventory btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search</a>
                                    <a href="#" class="submit-xls-inventory btn btn-secondary btn-label rounded-pill"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i> Export to Excel</a>
                                    <a href="#" class="submit-print-inventory d-none btn btn-primary btn-label rounded-pill"><i class="ri-printer-line label-icon align-middle rounded-pill fs-16 me-2"></i> Print</a>
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
                    <div class="row table-responsive">
                        <table id="inventory_list" style="font-size: 11px;"  width="100%"  class="table table-striped align-middle table-nowrap mb-0 table-bordered table-hover align-middle">
                            <thead  class="table-light">
                                <tr>
                                    <!-- <th class="fw-medium text-center">Client</th> -->
                                    <th class="fw-medium text-center">#</th>
                                    <th class="fw-medium text-center">Site</th>
                                    <th class="fw-medium text-center">Warehouse</th>
                                    <th class="fw-medium text-center">Sap Code</th>
                                    <th class="fw-medium text-center">Product Code</th>
                                    <th class="fw-medium text-center">Product Name</th>
                                    <th class="fw-medium text-center">Batch/Lot No</th>
                                    <th class="fw-medium text-center">MFG Date</th>
                                    <th class="fw-medium text-center">Exp Date</th>
                                    <th class="fw-medium text-center">Item Type</th>
                                    <th class="fw-medium text-center">Location</th>
                                    <!-- <th class="fw-medium text-center">Whse QTY</th>
                                    <th class="fw-medium text-center">Whse UOM</th> -->
                                    <th class="fw-medium text-center">Inv QTY</th>
                                    <th class="fw-medium text-center">Inv UOM</th>
                                    <th class="fw-medium text-center">Reserve QTY</th>
                                    <th class="fw-medium text-center">Balance QTY</th>
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
<script src="{{ URL::asset('/assets/js/report/inventory.js') }}"></script>

@endsection
