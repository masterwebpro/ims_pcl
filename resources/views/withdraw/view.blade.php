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
                        <h5 class="card-title mb-0 flex-grow-1">{{ $wd->wd_no }}</h5>
                        <div class="col-md-2 text-center">
                            <span class="badge  fs-16 <?=$wd->status?> text-uppercase"><?=$wd->status?></span>
                        </div>
                        <div class="col-md-6 text-end">
                            <? if(in_array($wd->status, array('posted'))) : ?>
                                <? if (mod_access('withdrawal',  'unpost', Auth::id())) : ?>
                                    <button type="button" data-status="unpost" class="btn btn-info btn-label rounded-pill submit-unpost"><i class=" ri-lock-unlock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Unpost</button>
                                <? endif ;?>
                            <? endif;?>
                            @if ($wd->status == 'open')
                            <a href="{{ URL::to('withdraw/'._encode($wd->id).'/edit') }}" class="btn btn-success btn-label rounded-pill"><i
                                        class="ri-pencil-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                    Edit</a>
                            @else
                            <button type="button" class="generate-withdrawal-slip  btn btn-danger btn-label rounded-pill"><i
                                class="ri-file-pdf-line label-icon align-middle rounded-pill fs-16 me-2"></i>Withdrawal Slip</button>
                            @endif
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
    <form name="submit-do" id="submit-do">
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
                                            <select class="form-select select2" required="required" id="client" disabled
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
                                            <select class="form-select select2" required="required" id="deliver_to" disabled
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
                                            <select class="form-select select2" required="required" id="company" disabled
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
                                            <select class="form-select select2" required="required" id="store" disabled
                                                name="store">
                                                <option value="">Select Store/Warehouse</option>
                                                <? foreach($store_list as $store) : ?>
                                                <option value="<?= $store->id ?>" <?=($store->id == $wd->store_id) ? 'selected' : '' ?>><?= $store->store_name ?></option>
                                                <? endforeach;?>
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
                                            <select class="form-select select2" required="required" id="order_type" disabled
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
                                            <input type="text" class="form-control" id="order_no" disabled
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
                                            <input type="text" class="form-control" id="ar_no" name="dr_no" disabled
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
                                            <input type="date" class="form-control" id="withdraw_date" disabled
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
                                            <input type="text" class="form-control" id="po_num" name="po_num" disabled
                                                value="{{ $wd->po_num }}" placeholder="PO Number">
                                            <span class="text-danger error-msg po_num_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice </label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="sales_invoice" disabled
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
                                            <select class="form-select select2" required="required" id="wd_type" disabled
                                            name="wd_type">
                                            <option value="">Select DO Type</option>
                                            <? foreach($wd_type as $type) : ?>
                                            <option value="<?= $type['code'] ?>" <?=($type['code'] == $wd->wd_type ? 'selected' : '')?>><?= $type['name'] ?></option>
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
                                            <input type="date" class="form-control" id="order_date" disabled
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
                                            <input type="date" class="form-control" id="pickup_date" disabled
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
                                            <input type="date" class="form-control" id="trgt_dlv_date" disabled
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
                                                value="<?=$wd->name?>" placeholder="Created By">
                                            <span class="text-danger error-msg created_by_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Actual Delivery Date <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="date" class="form-control" id="actual_dlv_date" disabled
                                                name="actual_dlv_date" value="{{ $wd->actual_dlv_date }}" placeholder="Inspect Date">
                                            <span class="text-danger error-msg actual_dlv_date_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-3">
                            <div class="row ms-3 mx-3">
                                {{-- <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">Warehouse <span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <select class="form-select select2" required="required" id="warehouse" disabled
                                                name="warehouse">
                                                <option value="">Select warehouse</option>
                                                <? foreach ($warehouse_list as $key => $wh):?>
                                                <option value="<?=$wh->id?>" <?=($wh->id == $wd->warehouse_id) ? 'selected' : ''?>>{{ $wh->warehouse_name }}</option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg warehouse_error"></span>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="remarks" id="remarks" disabled
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
                                    <div class="table-responsive">
                                        <table class="table table-nowrap" id="product-list">
                                            <thead>
                                                <tr class="table-active">
                                                    <th scope="col" style="width: 10px;">#</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Item Type</th>
                                                    <th scope="col">Date Received</th>
                                                    @if ($wd->status == 'open')
                                                    {{-- <th scope="col">Available Stocks</th> --}}
                                                    @endif
                                                    <th scope="col">Withdraw Quantity</th>
                                                    <th scope="col">Unit</th>
                                                    <th scope="col">Lot No</th>
                                                    <th scope="col">Expiry Date</th>
                                                    <th scope="col">Mfg. Date</th>
                                                    <th scope="col">Warehouse</th>
                                                    <th scope="col">Location</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="newlink">
                                                <?
                                                $rowCount = count($wd->items);
                                                $x=1;
                                                $total = 0;
                                                 ?>
                                                @if(isset($wd->items))
                                                    @foreach($wd->items as $item)
                                                    @php
                                                        $total += $item->inv_qty;
                                                    @endphp
                                                    <tr id="product_{{$item->product_id}}">
                                                        <td class="text-start">
                                                            <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />
                                                        {{$x++}} </td>
                                                        <td class="text-start fs-14">
                                                            {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                        </td>
                                                        <td class=" ps-1">
                                                            <span class="badge bg-success text-capitalize">{{ isset($item->master) ? $item->master->item_type : ''}} </span>
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ isset($item->master) ? date('M d, Y', strtotime($item->master->received_date)) : '' }}
                                                        </td>
                                                        {{-- @if ($wd->status == 'open')
                                                        <td class="ps-1 text-center">
                                                            {{ number_format($item->master->inv_qty,2) }}
                                                        </td>
                                                        @endif --}}
                                                        <td class="ps-1 text-center">
                                                            {{ number_format($item->inv_qty,2) }}
                                                            <input type="hidden"  class="form-control inv_qty numeric" name="inv_qty[]"  data-id="{{$x}}" id="inv_qty_{{$x}}" value="{{$item->inv_qty}}" placeholder="Inv Qty" />
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->uom) ? $item->uom->code : "" }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->master) ? $item->master->lot_no : "" }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->master) ? $item->master->expiry_date : "" }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->master) ? $item->master->manufacture_date : "" }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->master) ? $item->master->warehouse_name : "" }}
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->master) ?  $item->master->location : "" }}
                                                        </td>
                                                        <td class="ps-1">
                                                            @if ($item->product->is_serialize == 1)
                                                                <button type="button" class="add-serial btn btn-icon btn-success waves-effect waves-light" data-type="view" data-itemize="{{ $item->itemize }}" id="row_{{$x}}" data-rowid="{{$x}}" data-productname="{{ $item->product->product_name }}" data-productcode="{{ $item->product->product_code }}" data-productid="{{$item->product_id}}" data-masterfileid="{{$item->masterfile_id}}">
                                                                    <i class="ri-barcode-line"></i>
                                                                </button>
                                                                @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                <tr class="">
                                                    <td colspan="8" class="text-danger text-center">No Record Found!</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-end">Total</td>
                                                    <td class="text-center" id="total"><?=number_format($total,2)?></td>
                                                    <td colspan="6"></td>
                                                </tr>
                                            </tfoot>
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
    <div class="modal" id="show-serial" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">View Serial</h5>
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
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-serial-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th colspan="3"  class="text-center text-danger">Serial Count : <span id="counter">0</span></th>
                                        </tr>
                                        <tr>
                                            <th>Serial No</th>
                                            <th>Warranty No</th>
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
@endsection
