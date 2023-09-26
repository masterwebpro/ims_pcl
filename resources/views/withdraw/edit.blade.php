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
                        <div class="col-md-4 text-start">
                            <h5 class="card-title mb-0 flex-grow-1">{{ $wd->wd_no }}</h5>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="badge  fs-16 <?=$wd->status?> text-uppercase"><?=$wd->status?></span>
                        </div>
                        <div class="col-md-6 text-end">
                            <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
                            <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i
                                    class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                            <button type="button" class="generate-picklist  btn btn-danger btn-label rounded-pill"><i
                                    class="ri-file-pdf-line label-icon align-middle rounded-pill fs-16 me-2"></i>Picklist</button>
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
                                                <option value="">Select Client</option>
                                                <? foreach($client_list as $client) : ?>
                                                <option value="<?= $client->id ?>" <?=($client->id == $wd->customer_id) ? 'selected' : '' ?>><?= $client->client_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg client_error"></span>
                                        </p>

                                    </div>
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Deliver To <span
                                                class="text-danger">*</span></h6>
                                        <input type="hidden" name="wd_no" id="wd_no" value="{{ $wd->wd_no }}" />
                                        <input type="hidden" name="wd_id" id="wd_id" value="{{ _encode($wd->id) }}" />
                                        <p class="fw-medium mb-2" id="shipping-name">
                                            <select class="form-select select2" required="required" id="deliver_to"
                                                name="deliver_to">
                                                <option value="">Select Deliver To</option>
                                                <? foreach($deliver_list as $deliver) : ?>
                                                <option value="<?= $deliver->id ?>" <?=($deliver->id == $wd->deliver_to_id) ? 'selected' : '' ?>><?= $deliver->client_name ?>
                                                </option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg supplier_error"></span>
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
                                                <option value="<?= $company->id ?>" <?=($company->id == $wd->company_id) ? 'selected' : '' ?>><?= $company->client_name ?></option>
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
                                                <option value="">Select Store/Warehouse</option>
                                                <? foreach($store_list as $store) : ?>
                                                <option value="<?= $store->id ?>" <?=($store->id == $wd->store_id) ? 'selected' : '' ?>><?= $store->store_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg store_error"></span>
                                        </p>
                                        <!-- <p class="text-muted mb-1" id="shipping-address-line-1">supp_add  here</p>
                                            <p class="text-muted mb-1">supp_add2 here</p>
                                            <p class="text-muted mb-0">supp_city province, country here</p> -->
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
                                                <option value="<?= $type->code ?>" <?=($type->code == $wd->order_type ? 'selected' : '')?>><?= $type->name ?></option>
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
                                                name="order_no" value="{{ $wd->order_no }}" placeholder="Order no">
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
                                                value="{{ $wd->dr_no }}" placeholder="DR Number">
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
                                            name="withdraw_date" placeholder="Withdraw Date" value="{{ $wd->withdraw_date }}">
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
                                                value="{{ $wd->po_num }}" placeholder="PO Number">
                                            <span class="text-danger error-msg po_num_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice </label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="sales_invoice"
                                                name="sales_invoice" value="{{ $wd->sales_invoice }}" placeholder="Sales Invoice">
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
                                            <option value="<?= $type['code'] ?>" <?=($type['code'] == $wd->wd_type ? 'selected' : '')?>><?= $type['name'] ?></option>
                                            <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg do_type_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Order Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="order_date"
                                            name="order_date" value="{{ $wd->order_date }}" placeholder="Order Date">
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
                                                name="pickup_date" value="{{ $wd->pickup_date }}" placeholder="Pickup Date">
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
                                                name="trgt_dlv_date" value="{{ $wd->target_dlv_date }}" placeholder="Date Received">
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
                                                value="{{ $wd->name }}" placeholder="Created By">
                                            <span class="text-danger error-msg created_by_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Actual Delivery Date</label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="actual_dlv_date"
                                                name="actual_dlv_date" value="{{ $wd->actual_dlv_date }}" placeholder="Inspect Date">
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
                                                value="{{ $wd->remarks }}" placeholder="Remarks">
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
                                                    <th scope="col">Date Received</th>
                                                    <th scope="col">Available Stocks</th>
                                                    <th scope="col">Withdraw Quantity</th>
                                                    <th scope="col">Unit</th>
                                                    <th scope="col">Lot No.</th>
                                                    <th scope="col">Expiry Date</th>
                                                    <th scope="col">Warehouse</th>
                                                    <th scope="col">Location</th>
                                                    <th scope="col" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="newlink">
                                                <?
                                                $rowCount = count($wd->items);
                                                $x=1;
                                                 ?>
                                                @if(isset($wd->items))
                                                    @foreach($wd->items as $item)
                                                    <tr id="rows_{{$x}}">
                                                        <td class="text-start">
                                                            <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />
                                                            <input type="hidden" name="masterfile_id[]" readonly id="masterfile_id" value="{{$item->masterfile_id}}" />
                                                            <input type="hidden" name="inv_uom[]" readonly id="inv_uom_{{$item->inv_uom}}" value="{{$item->inv_uom}}" />
                                                            <input type="hidden" name="is_serialize[]" readonly value="{{$item->product->is_serialize}}" />
                                                        {{$x++}} </td>
                                                        <td class="text-start fs-14">
                                                            {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                        </td>
                                                        <td class=" ps-1">
                                                            @php
                                                                $type = ($item->master->item_type == 'good') ? 'bg-success' : 'bg-danger';
                                                            @endphp
                                                            <span class="badge {{$type}} text-capitalize">{{$item->master->item_type}} </span>
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ isset($item->master->receiving) ? date('M d, Y', strtotime($item->master->receiving->date_received)) : '' }}
                                                        </td>
                                                        <td class="ps-1 text-center">
                                                            {{ number_format($item->master->inv_qty,2) }}
                                                        </td>
                                                        <td class="ps-1">
                                                            <input type="text"  class="form-control inv_qty numeric" name="inv_qty[]" data-qty="{{ $item->master->inv_qty }}" data-id="{{$x}}" id="inv_qty_{{$x}}" value="{{$item->inv_qty}}" placeholder="Inv Qty" />
                                                            <span class="text-danger error-msg inv_qty{{$x}}_error"></span>
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ $item->master->uom->code }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->master->receiving) ? $item->master->receiving->lot_no : "" }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->master->receiving) ? $item->master->receiving->expiry_date : "" }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ $item->master->warehouse->warehouse_name }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ $item->master->location->location }}
                                                        </td>
                                                        <td>
                                                            <div class="text-center">
                                                                <button type="button" class="btn btn-icon btn-danger remove-product mx-2 waves-effect waves-light" data-id="{{$x}}">
                                                                    <i class="ri-delete-bin-5-fill"></i>
                                                                </button>
                                                                @if ($item->product->is_serialize == 1)
                                                                <button type="button" class="add-serial btn btn-icon btn-success waves-effect waves-light" data-itemize="{{ $item->itemize }}" id="row_{{$x}}" data-rowid="{{$x}}" data-productname="{{ $item->product->product_name }}" data-productcode="{{ $item->product->product_code }}" data-productid="{{$item->product_id}}" data-masterfileid="{{$item->masterfile_id}}">
                                                                    <i class="ri-barcode-line"></i>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                <tr class="">
                                                    <td colspan="8" class="text-danger text-center">No Record Found!</td>
                                                </tr>
                                                @endif

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
                        <div class="col-3">
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
                                <th>Date Recieved</th>
                                <th>Item Type</th>
                                <th>Available Stocks</th>
                                <th>Unit</th>
                                <th>Lot No.</th>
                                <th>Expiry Date</th>
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
                            <input type="text" class="form-control" id="product_qty" disabled>
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
