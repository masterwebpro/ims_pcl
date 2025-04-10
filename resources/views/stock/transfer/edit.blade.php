@extends('layouts.master')
@section('title') Stock Movement @endsection
@section('css')

<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Stock @endslot
@slot('title') Stock Transfer @endslot
@endcomponent

<div class="row">
    <div class="text-start mb-4 col-4">
        <h3 class="text-success"><?=$transfer_hdr->ref_no?></h3>
    </div>

    <div class="text-center mb-4 col-2">
        <span class="badge border border-secondary text-secondary fs-18 text-primary text-uppercase"><?=$transfer_hdr->status?></span>
    </div>
    <div class="text-end mb-4  col-6">
        <? if(in_array($transfer_hdr->status, array('open', 'new'))) : ?>
            <? if (mod_access('transfer',  'add', Auth::id())) : ?>
                <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
            <? endif ;?>

            <? if (mod_access('transfer', 'post', Auth::id())) : ?>
                <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
            <? endif ;?>

            <? if($transfer_hdr->status == 'open') : ?>
                <? if (mod_access('transfer',  'delete', Auth::id())) : ?>
                    <button data-status="delete" class="submit-delete  btn btn-danger btn-label rounded-pill"><i class="ri-delete-bin-line label-icon align-middle rounded-pill fs-16 me-2"></i> Delete</button>
                <? endif ;?>
            <? endif ;?>
        <? endif;?>

        <a href="{{ URL::to('stock/transfer') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
    </div>
    <!--end col-->
</div>
<!--end row-->


<div class="row justify-content-center">
    <div class="col-xxl-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"><?=$transfer_hdr->ref_no?> </h5>

                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">

                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<form name="submit-transfer" id="submit-transfer">
    <div class="row">
        <div class="col-xxl-12">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-header"><h6 class="text-muted mb-0">Other Info</h6></div>
                            <div class="card-body p-4 ">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <input type="hidden" class="form-control" name="ref_no" id="ref_no" value="{{$transfer_hdr->ref_no}}">
                                            <label for="source_company" class="form-label">Source Company <span class="text-danger">*</span></label>
                                            <select class="form-select select2 source_company" id="source_company" name="source_company">
                                                <option value="">Select Source Company</option>
                                                <? foreach($client_list as $company) : ?>
                                                    <option value="<?=$company->id?>" <?=($company->id == $transfer_hdr->source_company_id) ? 'selected': ''; ?> ><?=$company->client_name?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg source_company_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="transaction_date" class="form-label">Source Site Name <span class="text-danger">*</span></label>
                                            <input type="hidden" class="form-control" name="store_id" id="store_id" value="{{$transfer_hdr->source_store_id}}">
                                            <select class="form-select select2 source_site" name="source_site" id="source_site">
                                                <option value="">Select Source Site</option>
                                            </select>
                                            <span class="text-danger error-msg source_site_error"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" placeholder="Transaction Date"  value="{{$transfer_hdr->trans_date}}"  name="transaction_date" id="transaction_date">
                                            <span class="text-danger error-msg transaction_date_error"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="firstNameinput" class="form-label">DR Num</label>
                                            <input type="text" class="form-control" placeholder="DR num"  value="{{$transfer_hdr->dr_no}}"  name="dr_no" id="dr_no">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="requested_by" class="form-label">Requested By <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Requested By"  value="{{$transfer_hdr->requested_by}}" name="requested_by" id="requested_by">
                                            <span class="text-danger error-msg requested_by_error"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <input type="text" class="form-control" name="remarks"  value="{{$transfer_hdr->remarks}}" placeholder="Remarks" id="remarks">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!--end card-body-->
                    </div>

                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->


        <div class="col-xxl-12">
            <div class="card" id="demo">
                <!--end col-->
                <div class="col-lg-12">
                    <div class="">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">Item List</h5>
                                <div class="flex-shrink-0">
                                    <a href="#" class="add-item btn btn-info btn-label rounded-pill"><i class="bx bx-plus label-icon align-middle rounded-pill fs-14 me-2"></i> Add Item</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="errMsg" class="d-none">
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br>
                                    <ul class="errors">
                                    </ul>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-nowrap table-bordered transfer" id="product-list">
                                    <thead>
                                        <tr class="table-active">
                                            <th scope="col" class="text-center align-middle" rowspan="2">#</th>
                                            <th scope="col" class="text-center" rowspan="2" valign="middle">Product Details</th>
                                            <th rowspan="2" class="text-center" valign="middle">Item Type</th>
                                            <th colspan="3" class="text-center">Source Location</th>
                                            <th colspan="3" class="text-center">Destination Location</th>
                                            <th rowspan="2" class="text-center" valign="middle">Action</th>
                                        </tr>
                                        <tr class="table-active text-center" >
                                            <th>Warehouse</th>
                                            <th>Location</th>
                                            <th>Inv</th>
                                            <th>Warehouse</th>
                                            <th>Location</th>
                                            <th>Inv</th>
                                        </tr>
                                    </thead>
                                    <tbody id="newlink">
                                        <?
                                        $i=1;

                                        // dd($transfer_dtl);
                                        foreach($transfer_dtl as $dtl) : ?>
                                        <tr id="R{{$i}}">
                                            <td class="text-start">
                                                <input type="hidden" name="product_id[]" readonly="" id="product_id_{{$dtl->id}}" value="{{$dtl->product_id}}">
                                                <input type="hidden" name="rcv_dtl_id[]" readonly="" id="rcv_dtl_id_{{$dtl->id}}" value="{{$dtl->rcv_dtl_id}}">
                                                <input type="hidden" name="master_id[]" readonly="" id="master_id_{{$dtl->id}}" value="{{$dtl->master_id}}">


                                                <input type="hidden" name="product_name[]" readonly id="product_name_{{$dtl->id}}" value="{{$dtl->item->product_name}}" />
                                                <input type="hidden" name="product_code[]" readonly id="product_code_{{$dtl->id}}" value="{{$dtl->item->product_code}}'" />

                                                {{$i}}
                                            </td>
                                            <td class="text-start  fs-13">
                                                {{$dtl->item->product_name}}<br><small><?=($dtl->item->sap_code) ? $dtl->item->sap_code : $dtl->item->product_code; ?></small>
                                            </td>
                                            <td class="text-center ps-1 fs-13">{{$dtl->source_item_type}}
                                                <input type="hidden" readonly="" class="form-control" name="item_type[]" data-id="good" id="item_type_{{$dtl->id}}" value="{{$dtl->source_item_type}}">
                                            </td>
                                            <td class="text-center ps-1 fs-13">
                                                {{$dtl->source_warehouse->warehouse_name}}
                                                <input type="hidden" readonly="" class="form-control" name="source_warehouse[]" id="source_warehouse_{{$dtl->id}}" value="{{$dtl->source_warehouse_id}}">
                                            </td>
                                            <td class="text-center ps-1 fs-13">
                                                <input type="hidden" readonly="" class="form-control" name="source_location[]" data-id="{{$dtl->id}}" id="source_location_{{$dtl->id}}" value="{{$dtl->source_storage_location_id}}">
                                                <?=($dtl->source_storage_location_id) ? $dtl->source_location->location : '-'; ?>
                                            </td>
                                            <td class="text-start ps-1 fs-13">
                                                <div class="input-group" style="width: 140px;">
                                                <input type="text" readonly="" class="form-control source_inv_qty input-group-text numeric transter_item" name="source_inv_qty[]" data-id="{{$dtl->id}}" id="source_inv_qty_{{$dtl->id}}" value="{{$dtl->source_inv_qty}}">
                                                <input type="hidden" readonly="" class="form-control" name="source_inv_uom[]" data-id="{{$dtl->id}}" id="source_inv_uom_{{$dtl->id}}" value="{{$dtl->source_inv_uom}}">
                                                <span class="input-group-text">{{$dtl->source_uom->code}}</span>
                                                </div>
                                                <span class="text-danger error-msg old_inv_qty0_error"></span>
                                            </td>
                                            <td class="text-center ps-1 fs-13">
                                                <input type="hidden"  class="form-control destWarehouseId" data-id="{{$dtl->id}}" id="{{$dtl->id}}" name="destWarehouseId[]" value="{{$dtl->dest_warehouse_id}}">
                                                <select style="width: 150px;" name="dest_warehouse[]" data-id="{{$dtl->id}}" id="dest_warehouse_{{$dtl->id}}" class="form-select dest_warehouse select2">
                                                    <option value="">Select Warehouse</option>
                                                    <? foreach($warehouses as $warehouse) : ?>
                                                        <option value ='{{$warehouse->id}}' <?=($warehouse->id == $dtl->dest_warehouse_id) ? 'selected': ''; ?> >{{$warehouse->warehouse_name}}</option>
                                                    <? endforeach;?>
                                                </select>
                                                <span class="text-danger error-msg dest_warehouse_0_error"></span>
                                            </td>
                                            <td class="text-start ps-1">
                                                <input type="hidden"  class="form-control" id="location_{{$dtl->id}}" name="location[]" value="{{$dtl->dest_storage_location_id}}">
                                                <select style="width: 100px;" name="dest_location[]" id="dest_location_{{$dtl->id}}" class="form-select dest_location select2">
                                                    <option value="">Select Location</option>
                                                </select>
                                                <span class="text-danger error-msg dest_location0_error"></span>
                                            </td>
                                            <td class="text-start ps-1">
                                                <div class="input-group" style="width: 140px;">
                                                    <input type="text" class="form-control new_inv_qty numeric transfer_item" name="dest_inv_qty[]" data-id="{{$dtl->id}}" id="dest_inv_qty_{{$dtl->id}}" value="{{$dtl->dest_inv_qty}}">
                                                    <input type="hidden" readonly="" class="form-control" name="dest_inv_uom[]" data-id="{{$dtl->id}}" id="dest_inv_uom_{{$dtl->id}}" value="{{$dtl->dest_inv_uom}}">
                                                    <span class="input-group-text">{{$dtl->dest_uom->code}}</span>
                                                </div>
                                                <span class="text-danger error-msg dest_inv_qty0_error"></span>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <a href="javascript:void(0)" class="text-info split-product" data-id="0">
                                                        <i class=" ri-menu-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>Split</a>&nbsp;
                                                    <a href="javascript:void(0)" class="text-danger remove-product" data-id="0">
                                                        <i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <? $i++; endforeach; ?>
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
            <!--end card-->
        </div>
        <!--end col-->

    </div>
    <!--end row-->
</form>


<!-- show charges Modal -->
<div class="modal" id="show-items" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Item List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3 mb-2">
                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold">Source Warehouse</h6>
                        <p class="fw-medium">
                            <select class="form-select select3 source_warehouse" required="required" id="source_warehouse" name="source_warehouse">
                                <option value="">Select Source Warehouse</option>
                            </select>
                            <span class="text-danger error-msg source_warehouse_error"></span>
                        </p>
                    </div>

                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold"> Source Location </h6>
                        <p class="fw-medium" id="billing-name">
                            <select class="form-select select3" id="source_location" name="source_location">
                                <option value="">No Location</option>
                            </select>
                        </p>
                    </div>

                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold"> Product Name OR Code</h6>
                        <p class="fw-medium">
                            <input type="text" class="form-control" placeholder="Product Name or Code" id="product_name">
                        </p>
                    </div>


                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold">&nbsp;</h6>
                        <p class="fw-medium">
                            <button data-status="open" class="search-item btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search </button>
                        </p>
                    </div>
                    <div class="col-12 hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-warning rounded-pill" id="select-all"><i class="ri-check-double-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Select All</button>
                        <button type="button" class="btn btn-success rounded-pill" id="add-product"><i class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add Selected</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-nowrap table-bordered align-middle" width="100%" style="font-size: 12px;" id="show-items-list">
                        <thead class="table-light">
                            <tr>
                                <th>&nbsp;</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Location</th>
                                <th>Item Type</th>
                                <th>Inv Qty</th>
                                <th>Inv Uom</th>
                                <th>Whse qty</th>
                                <th>Whse Uom</th>
                                <th class="d-none">Rcv DTL ID</th>
                                <th >Master ID</th>
                                <!-- <th>REF1</th>
                                <th>REF1_type</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- <div class="modal" id="show-items" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Item List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3">
                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold">Source Warehouse</h6>
                        <p class="fw-medium">
                            <select class="form-select select3 source_warehouse" required="required" id="source_warehouse" name="source_warehouse">
                                <option value="">Select Source Warehouse</option>
                            </select>
                            <span class="text-danger error-msg source_warehouse_error"></span>
                        </p>
                    </div>

                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold"> Source Location </h6>
                        <p class="fw-medium" id="billing-name">
                            <select class="form-select select3" id="source_location" name="source_location">
                                <option value="">No Location</option>
                            </select>
                        </p>
                    </div>

                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold"> Product Name </h6>
                        <p class="fw-medium">
                            <input type="text" class="form-control" placeholder="Product Name" id="product_name">
                        </p>
                    </div>


                    <div class="col-3">
                        <h6 class="text-muted text-uppercase fw-semibold">&nbsp;</h6>
                        <p class="fw-medium">
                            <button data-status="open" class="search-item btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search </button>
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-nowrap table-bordered align-middle" width="100%" style="font-size: 12px;" id="show-items-list">
                        <thead class="table-light">
                            <tr>
                                <th>&nbsp;</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Location</th>
                                <th>Item Type</th>
                                <th>Inv Qty</th>
                                <th>Inv Uom</th>
                                <th>Whse qty</th>
                                <th>Whse Uom</th>
                                <th>Rcv DTL ID</th>
                                <!-- <th>REF1</th>
                                <th>REF1_type</th> -->
                            {{-- </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-success" id="add-product"><i class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add</button>
                </div>
            </div>
        </div>
    </div>
</div> --> --}}


	@endsection
@section('script')


<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/stock/transfer.js') }}"></script>

@endsection
