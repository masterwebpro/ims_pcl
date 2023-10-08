@extends('layouts.master')
@section('title')
    Withdrawal
@endsection
@section('css')
    <!--datatable css-->
    <link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Outbound
        @endslot
        @slot('title')
        Withdrawal
        @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Create Withdrawal</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
                            <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill d-none"><i
                                    class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                            <a href="{{ URL::to('withdraw') }}" class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    <form name="submit-withdrawal" id="submit-withdrawal">
        <div class="row justify-content-center">

            <div class="col-xxl-11">
                <div class="card">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card-body p-4 ">
                                <div class="row g-3">
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Customer Name <span
                                                class="text-danger">*</span></h6>
                                        <p class="fw-medium mb-2" id="billing-name">
                                            <select class="form-select select2" required="required" id="client"
                                                name="client">
                                                <option value="">Select Customer</option>
                                                <? foreach($client_list as $client) : ?>
                                                <option value="<?= $client->id ?>"><?= $client->client_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg client_error"></span>
                                        </p>
                                    </div>
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Deliver To <span
                                                class="text-danger">*</span></h6>
                                        <input type="hidden" name="wd_no" id="wd_no" />
                                        <p class="fw-medium mb-2" id="shipping-name">
                                            <select class="form-select select2" required="required" id="deliver_to"
                                                name="deliver_to">
                                                <option value="">Select Deliver To</option>
                                                <? foreach($deliver_list as $deliver) : ?>
                                                <option value="<?= $deliver->id ?>"><?= $deliver->client_name ?>
                                                </option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg deliver_to_error"></span>
                                        </p>
                                    </div>
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Company Name <span
                                                class="text-danger">*</span></h6>
                                        <p class="fw-medium mb-2" id="billing-name">
                                            <select class="form-select select2" required="required" id="company"
                                                name="company">
                                                <option value="">Select Company</option>
                                                <? foreach($company_list as $company) : ?>
                                                <option value="<?= $company->id ?>"><?= $company->client_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg company_error"></span>
                                        </p>
                                    </div>
                                    <!--end col-->
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Site Name
                                            <span class="text-danger">*</span>
                                        </h6>
                                        <p class="fw-medium mb-2" id="shipping-name">
                                            <select class="form-select select2" required="required" id="store"
                                                name="store">
                                                <option value="">Select Site Name</option>
                                            </select>
                                            <span class="text-danger error-msg store_error"></span>
                                        </p>
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
                    <div class="row ">
                        <div class="col-lg-12">
                            <div class="row ms-3 mt-3 mx-3">

                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Order Type <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-select select2" required="required" id="order_type"
                                                name="order_type">
                                                <option value="">Select Order Type</option>
                                                <? foreach($order_type as $type) : ?>
                                                <option value="<?= $type->code ?>" <?=($type->code == 'SO' ? 'selected' : '')?>><?= $type->name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg order_type_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Order No. <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="order_no"
                                                name="order_no" value="" placeholder="Order no">
                                            <span class="text-danger error-msg order_no_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">DR Number</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="dr_no" name="dr_no"
                                                value="" placeholder="DR Number">
                                            <span class="text-danger error-msg dr_no_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Withdrawal Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="withdraw_date"
                                            name="withdraw_date" placeholder="Withdraw Date" value="<?=date('Y-m-d');?>">
                                            <span class="text-danger error-msg withdraw_date_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">PO Number</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="po_num" name="po_num"
                                                value="" placeholder="PO Number">
                                            <span class="text-danger error-msg po_num_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice </label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="sales_invoice"
                                                name="sales_invoice" value="" placeholder="Sales Invoice">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="row ms-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">Withdrawal Type <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-select select2" required="required" id="wd_type"
                                            name="wd_type">
                                            <option value="">Select Withdrawal Type</option>
                                            <? foreach($wd_type as $type) : ?>
                                            <option value="<?= $type['code'] ?>" <?=($type['code'] == 'delivery' ? 'selected' : '')?>><?= $type['name'] ?></option>
                                            <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg wd_type_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Order Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="order_date"
                                            name="order_date" value="<?=date('Y-m-d');?>" placeholder="Order Date">
                                            <span class="text-danger error-msg order_date_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row ms-3 mt-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Pickup Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="pickup_date"
                                                name="pickup_date" value="<?=date('Y-m-d');?>" placeholder="Pickup Date">
                                            <span class="text-danger error-msg pickup_date_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Target Delivery Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="trgt_dlv_date"
                                                name="trgt_dlv_date" value="<?=date('Y-m-d');?>" placeholder="Date Received">
                                            <span class="text-danger error-msg trgt_dlv_date_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="row ms-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">Created By <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="created_by" name="created_by" disabled
                                                value="<?=$created_by?>" placeholder="Created By">
                                            <span class="text-danger error-msg created_by_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Actual Delivery Date</label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="actual_dlv_date"
                                                name="actual_dlv_date" value="<?=date('Y-m-d');?>" placeholder="Inspect Date">
                                            <span class="text-danger error-msg actual_dlv_date_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="row ms-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="remarks" id="remarks"
                                                value="" placeholder="Remarks">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--end col-->
                        <div class=" col-lg-12 mt-5">
                            <div class="">
                                <div class="card-header card-title mb-0 flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <h5>Withdrawal Details</h5>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <!-- ITEMS -->
                                    <div class="col-md-12 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i
                                                    class="ri-barcode-line label-icon align-middle rounded-pill fs-16 me-2"></i>Scan
                                                Code</span>
                                            <input type="text" class="form-control"
                                                aria-label="Recipient's username with two button addons">
                                            <button class="btn btn-warning" id="find-items" type="button"><i
                                                    class="ri-book-read-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                Find Item</button>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-nowrap" id="product-list">
                                            <thead>
                                                <tr class="table-active">
                                                    <th scope="col" style="width: 10px;">#</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Item Type</th>
                                                    {{-- <th scope="col">Date Received</th> --}}
                                                    <th scope="col">Available Stocks</th>
                                                    <th scope="col">Withdraw Quantity</th>
                                                    <th scope="col">Unit</th>
                                                    {{-- <th scope="col">Lot No</th> --}}
                                                    {{-- <th scope="col">Expiry Date</th> --}}
                                                    <th scope="col">Warehouse</th>
                                                    <th scope="col">Location</th>
                                                    <th scope="col" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="newlink">

                                            </tbody>
                                        </table>

                                        <!--end table-->
                                    </div>
                                </div>
                                <!--end card-body-->
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
    </form>


    <!-- show charges Modal -->
    <div class="modal" id="show-items" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Master List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <h6 class="text-muted text-uppercase fw-semibold">Product Code|Name|SKU</h6>
                            <p class="fw-medium" id="billing-name">
                                <input type="text" class="form-control" id="product" name="product" value="" placeholder="Product code,name,sku">
                            </p>
                        </div>
                        <div class="col-3">
                            <h6 class="text-muted text-uppercase fw-semibold">Warehouse</h6>
                            <p class="fw-medium" id="billing-name">
                                <select class="form-select" id="warehouse" name="warehouse">
                                    <option value="">Select Warehouse</option>
                                </select>
                            </p>
                        </div>
                        <div class="col-3">
                            <h6 class="text-muted text-uppercase fw-semibold">Item Type </h6>
                            <p class="fw-medium" id="billing-name">
                                <select class="form-select" required="required" id="item_type" name="item_type">
                                    <option value="">Select Type</option>
                                        <option value="good" selected>Good</option>
                                        <option value="damage">Damage</option>
                                        <option value="repair">Repair</option>
                                </select>
                            </p>
                        </div>

                        <div class="col-2">
                            <h6 class="text-muted text-uppercase fw-semibold">&nbsp;</h6>
                            <p class="fw-medium" id="billing-name">
                                <button data-status="open" class="search-item btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search </button>
                            </p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-items-list">
                            <thead class="table-light">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    {{-- <th>Date Recieved</th> --}}
                                    <th>Item Type</th>
                                    <th>Available Stocks</th>
                                    <th>Unit</th>
                                    {{-- <th>Lot No.</th> --}}
                                    {{-- <th>Expiry Date</th> --}}
                                    <th>Warehouse</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-success" id="add-product"><i
                                class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- show add serial Modal -->
    <div class="modal" id="show-serial" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Add Serial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Product</label>
                            <input type="text" class="form-control" id="product_code" disabled>
                            <input type="hidden" class="form-control" id="productid">
                            <input type="hidden" class="form-control" id="masterfileid">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Quantity</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="product_qty" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row mb-2">
                                <div class="col-md-12 form-group">
                                    <label for="serial_no">Serial No</label>
                                    <input type="text" id="serial_no"placeholder="Scan Serial No" class="form-control"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <div class="form-check form-switch form-switch-success form-switch-md">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toggle" checked>
                                        <label for="toggle">Auto Focus Warranty</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12 form-group">
                                    <label for="warranty_no">Warrant No</label>
                                    <input type="text" id="warranty_no" placeholder="Scan Warrant No" class="form-control"/>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="d-grid gap-2">
                                    <input class="form-control" type="file" id="fileInput">
                                    <button type="submit" class="btn btn-warning" id="upload-serial"><i class="ri-upload-line"></i> Upload Serial</button>
                                    <button type="button" class="btn btn-info" id="download-template"><i class="ri-download-line"></i> Download Template</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="d-grid gap-2">
                                    <button type="button" id="save-serial" class="btn btn-success"><i class="ri-save-line"></i> Save Serial</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-serial-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="3"  class="text-center text-danger">Serial Count : <span id="counter">0</span></th>
                                        </tr>
                                        <tr>
                                            <th>Serial No</th>
                                            <th>Warranty No</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="serial-list">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/masks/jquery.mask.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/withdraw/withdraw.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
@endsection
