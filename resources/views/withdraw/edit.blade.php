@extends('layouts.master')
@section('title')
    Delivery Order
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
            Delivery Order
        @endslot
    @endcomponent

    <div class="row justify-content-center">
        <div class="col-xxl-11">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <div class="col-md-4 text-start">
                            <h5 class="card-title mb-0 flex-grow-1">{{ $do->do_no }}</h5>
                        </div>
                        <div class="col-md-2 text-center">
                            <span class="badge  fs-16 <?=$do->status?> text-uppercase"><?=$do->status?></span>
                        </div>
                        <div class="col-md-6 text-end">
                            <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
                            <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i
                                    class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                            <a href="{{ URL::to('do') }}" class="btn btn-primary btn-label rounded-pill"><i
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
    <form name="submit-do" id="submit-do">
        <div class="row justify-content-center">

            <div class="col-xxl-11">
                <div class="card">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="card-body p-4 ">
                                <div class="row g-3">
                                    <div class="col-4">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Supplier Name <span
                                                class="text-danger">*</span></h6>
                                        <input type="hidden" name="do_no" id="do_no" value="{{ $do->do_no }}" />
                                        <p class="fw-medium mb-2" id="shipping-name">
                                            <select class="form-select select2" required="required" id="supplier"
                                                name="supplier">
                                                <option value="">Select Supplier</option>
                                                <? foreach($supplier_list as $supplier) : ?>
                                                <option value="<?= $supplier->id ?>" <?=($supplier->id == $do->supplier_id) ? 'selected' : '' ?>><?= $supplier->supplier_name ?>
                                                </option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg supplier_error"></span>
                                        </p>
                                        <!-- <p class="text-muted mb-1" id="shipping-address-line-1">supp_add  here</p>
                                            <p class="text-muted mb-1">supp_add2 here</p>
                                            <p class="text-muted mb-0">supp_city province, country here</p> -->
                                    </div>

                                    <div class="col-4">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name <span
                                                class="text-danger">*</span></h6>
                                        <p class="fw-medium mb-2" id="billing-name">
                                            <select class="form-select select2" required="required" id="client"
                                                name="client">
                                                <option value="">Select Client</option>
                                                <? foreach($client_list as $client) : ?>
                                                <option value="<?= $client->id ?>" <?=($client->id == $do->client_id) ? 'selected' : '' ?>><?= $client->client_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg client_error"></span>
                                        </p>
                                        <!-- <p class="text-muted mb-1" id="shipping-address-line-1">client_add  here</p>
                                            <p class="text-muted mb-1">client_add2 here</p>
                                            <p class="text-muted mb-0">client_city province, country here</p> -->
                                    </div>
                                    <!--end col-->
                                    <div class="col-4">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse / Store Address
                                            <span class="text-danger">*</span>
                                        </h6>
                                        <p class="fw-medium mb-2" id="shipping-name">
                                            <select class="form-select select2" required="required" id="store"
                                                name="store">
                                                <option value="">Select Store/Warehouse</option>
                                                <? foreach($store_list as $store) : ?>
                                                <option value="<?= $store->id ?>" <?=($store->id == $do->store_id) ? 'selected' : '' ?>><?= $store->store_name ?></option>
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
                                                <option value="<?= $type->code ?>" <?=($type->code == $do->order_type ? 'selected' : '')?>><?= $type->name ?></option>
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
                                                name="order_no" value="{{ $do->order_no }}" placeholder="Order no">
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
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">PO Number</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="po_num" name="po_num"
                                                value="{{ $do->po_num }}" placeholder="PO Number">
                                            <span class="text-danger error-msg po_num_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice </label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="sales_invoice"
                                                name="sales_invoice" value="{{ $do->sales_invoice }}" placeholder="Sales Invoice">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="row ms-3 mx-3">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">DO Type <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-select select2" required="required" id="do_type"
                                            name="do_type">
                                            <option value="">Select DO Type</option>
                                            <? foreach($do_type as $type) : ?>
                                            <option value="<?= $type['code'] ?>" <?=($type['code'] == $do->do_type ? 'selected' : '')?>><?= $type['name'] ?></option>
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
                                            name="order_date" value="{{ $do->order_date }}" placeholder="Order Date">
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
                                                name="pickup_date" value="{{ $do->pickup_date }}" placeholder="Pickup Date">
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
                                                name="trgt_dlv_date" value="{{ $do->target_dlv_date }}" placeholder="Date Received">
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
                                                value="{{ $do->name }}" placeholder="Created By">
                                            <span class="text-danger error-msg created_by_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Actual Delivery Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="actual_dlv_date"
                                                name="actual_dlv_date" value="{{ $do->actual_dlv_date }}" placeholder="Inspect Date">
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
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">Warehouse <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-select select2" required="required" id="warehouse"
                                                name="warehouse">
                                                <option value="">Select warehouse</option>
                                                <? foreach ($warehouse_list as $key => $wh):?>
                                                    <option value="<?=$wh->id?>" <?=($wh->id == $do->warehouse_id) ? 'selected' : ''?>>{{ $wh->warehouse_name }}</option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg warehouse_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="remarks" id="remarks"
                                                value="{{ $do->remarks }}" placeholder="Remarks">
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
                                        <h5>Delivery Order Details</h5>
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
                                                    {{-- <th scope="col">Item Type</th> --}}
                                                    <th scope="col">WHSE Qty</th>
                                                    <th scope="col">WHSE UOM</th>
                                                    <th scope="col">Inv Qty</th>
                                                    <th scope="col">Inv UOM</th>
                                                    {{-- <th scope="col">Lot/Batch #</th> --}}
                                                    {{-- <th scope="col">Expiry Date</th> --}}
                                                    <th scope="col" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="newlink">
                                                <?
                                                $rowCount = count($do->items);
                                                $x=1;
                                                 ?>
                                                @if(isset($do->items))
                                                    @foreach($do->items as $item)
                                                    <tr id="product_{{$item->product_id}}">
                                                        <td class="text-start">
                                                            <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />
                                                        {{$x++}} </td>
                                                        <td class="text-start fs-14">
                                                            {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                        </td>
                                                        <td class=" ps-1">
                                                            <input type="text"    class="form-control numeric whse_qty uom_select" name="whse_qty[]" data-id="{{$item->product_id}}" id="whse_qty_{{$x}}" value="{{$item->whse_qty}}" placeholder="Whse Qty" />
                                                        </td>
                                                        <td class=" ps-1">
                                                           <select name="whse_uom[]"   id="whse_uom_{{$x}}" class="uom uom_select form-select">
                                                                <option value="">Select UOM</option>
                                                                @foreach($uom_list as $uom_whse)
                                                                <option value="{{$uom_whse->uom_id}}" <?=($uom_whse->uom_id == $item->whse_uom) ? 'selected': ''; ?> >{{$uom_whse->code}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="ps-1">
                                                            <input type="text"  class="form-control inv_qty numeric uom_select" name="inv_qty[]" data-id="{{$item->product_id}}" id="inv_qty_{{$x}}" value="{{$item->inv_qty}}" placeholder="Inv Qty" />
                                                        </td>
                                                        <td class=" ps-1">
                                                            <select name="inv_uom[]"    id="inv_uom_{{$x}}" class="uom uom_select form-select">
                                                                <option value="">Select UOM</option>
                                                                @foreach($uom_list as $uom)
                                                                <option value="{{$uom->uom_id}}" <?=($uom->uom_id == $item->inv_uom) ? 'selected': ''; ?> >{{$uom->code}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="text-center">
                                                                <a href="javascript:void(0)" class="text-danger remove-product" data-id="{{$item->product_id}}">
                                                                    <i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                                </a>
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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Product List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>

                <div class="modal-body">
                    <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-items-list">
                        <thead class="table-light">
                            <tr>
                                <th>&nbsp;</th>
                                <th>Product Code</th>
                                <th>Product SKU</th>
                                <th>Product Name</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

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
    <script src="{{ URL::asset('/assets/js/do/do.js') }}"></script>
@endsection
