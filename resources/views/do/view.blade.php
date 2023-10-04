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
                        <h5 class="card-title mb-0 flex-grow-1">{{ $do->do_no }}</h5>
                        <div class="col-md-2 text-center">
                            <span class="badge  fs-16 <?=$do->status?> text-uppercase"><?=$do->status?></span>
                        </div>
                        <div class="col-md-6 text-end">
                            @if ($do->status == 'open')
                            <a href="{{ URL::to('do/'._encode($do->id).'/edit') }}" class="btn btn-success btn-label rounded-pill"><i
                                        class="ri-pencil-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                    Edit</a>
                            @else
                            <button data-status="posted" class="submit-withdrawal btn btn-warning btn-label rounded-pill"><i
                                class="ri-download-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                            Withdraw</button>
                            @endif
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
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Customer Name <span
                                                class="text-danger">*</span></h6>
                                        <input type="hidden" name="do_no" id="do_no" />
                                        <p class="fw-medium mb-2" id="shipping-name">
                                            <select class="form-select select2" required="required" id="customer" disabled
                                                name="customer">
                                                <option value="">Select Customer</option>
                                                <? foreach($client_list as $client) : ?>
                                                <option value="<?= $client->id ?>" <?=($client->id == $do->customer_id) ? 'selected' : '' ?>><?= $client->client_name ?>
                                                </option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg customer_error"></span>
                                        </p>
                                    </div>

                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Deliver To <span
                                                class="text-danger">*</span></h6>
                                        <p class="fw-medium mb-2" id="billing-name">
                                            <select class="form-select select2" required="required" id="client" disabled
                                                name="deliver_to">
                                                <option value="">Select Deliver To</option>
                                                <? foreach($deliver_list as $deliver) : ?>
                                                <option value="<?= $deliver->id ?>" <?=($deliver->id == $do->deliver_to_id) ? 'selected' : '' ?>><?= $deliver->client_name ?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg client_error"></span>
                                        </p>
                                    </div>
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Company <span
                                            class="text-danger">*</span></h6>
                                    <p class="fw-medium mb-2" id="billing-name">
                                        <select class="form-select select2" id="company" name="company" disabled>
                                            <option value="">Select Company</option>
                                            <? foreach($company_list as $company) : ?>
                                                <? if($company->client_type == 'O') : ?>
                                                    <option value="<?=$company->id?>"  <?=($do->company_id == $company->id) ? 'selected': ''?> ><?=$company->client_name?></option>
                                                <? endif;?>
                                            <? endforeach;?>
                                        </select>
                                        </p>
                                    </div>
                                    <!--end col-->
                                    <div class="col-3">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Site
                                            <span class="text-danger">*</span>
                                        </h6>
                                        <p class="fw-medium mb-2" id="shipping-name">
                                            <select class="form-select select2" required="required" id="store" disabled
                                                name="store">
                                                <option value="">Select Site</option>
                                                <? foreach($store_list as $store) : ?>
                                                <option value="<?= $store->id ?>" <?=($store->id == $do->store_id) ? 'selected' : '' ?>><?= $store->store_name ?></option>
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
                                            <input type="text" class="form-control" id="order_no" disabled
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
                                            <input type="text" class="form-control" id="po_num" name="po_num" disabled
                                                value="{{ $do->po_num }}" placeholder="PO Number">
                                            <span class="text-danger error-msg po_num_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice </label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="sales_invoice" disabled
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
                                            <select class="form-select select2" required="required" id="do_type" disabled
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
                                            <input type="date" class="form-control" id="order_date" disabled
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
                                            <input type="date" class="form-control" id="pickup_date" disabled
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
                                            <input type="date" class="form-control" id="trgt_dlv_date" disabled
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
                                                value="<?=$do->name?>" placeholder="Created By">
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
                                        <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" name="remarks" id="remarks" disabled
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
                                    <div class="table-responsive">
                                        <table class="table table-nowrap" id="product-list">
                                            <thead>
                                                <tr class="table-active">
                                                    <th scope="col" style="width: 10px;">#</th>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Order Quantity</th>
                                                    <th scope="col">Unit</th>
                                                    <th scope="col">Unserve Quantity</th>
                                                    <th scope="col">Unit</th>
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
                                                        <td class="ps-1">
                                                            <input type="text" disabled class="form-control inv_qty numeric uom_select w-100 " name="inv_qty[]" data-id="{{$item->product_id}}" id="inv_qty_{{$x}}" value="{{$item->inv_qty}}" placeholder="Inv Qty" />
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->unit) ? $item->unit->code : "" }}
                                                        </td>
                                                        <td class="ps-1">
                                                            <input type="text" disabled class="form-control inv_qty numeric uom_select w-100 " name="unserve_qty[]" data-id="{{$item->product_id}}" id="unserve_qty_{{$x}}" value="{{$item->unserve_qty}}" placeholder="Unserve Qty" />
                                                        </td>
                                                        <td class=" ps-1">
                                                            {{ ($item->unit) ? $item->unit->code : "" }}
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
