@extends('layouts.master')
@section('title') Receive Order @endsection
@section('css')

<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />


@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Inbound @endslot
@slot('title') Stock Movement @endslot
@endcomponent

<?
    use App\Http\Controllers\StockMovementController;
    $stock = new StockMovementController;
?>

<div class="row justify-content-center">
    <div class="col-xxl-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"><?=$mv_hdr->ref_no?></h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">

                        <? if(in_array($mv_hdr->status, array('open', 'new'))) : ?>
                            <? if (mod_access('putaway',  'add', Auth::id())) : ?>
                                <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                            <? endif ;?>

                            <? if (mod_access('putaway', 'post', Auth::id())) : ?>
                                <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                            <? endif ;?>

                            <? if($mv_hdr->status == 'open') : ?>
                                <? if (mod_access('putaway',  'delete', Auth::id())) : ?>
                                    <button data-status="delete" class="submit-delete  btn btn-danger btn-label rounded-pill"><i class="ri-delete-bin-line label-icon align-middle rounded-pill fs-16 me-2"></i> Delete</button>
                                <? endif ;?>
                            <? endif ;?>
                           
                        <? endif;?>

                        <? if(in_array($mv_hdr->status, array('posted', 'closed'))) : ?>
                            <? if (mod_access('putaway',  'unpost', Auth::id())) : ?>
                                <button type="button" data-status="unpost" class="btn btn-info btn-label rounded-pill submit-po"><i class=" ri-lock-unlock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Unpost</button>
                            <? endif ;?>
                        <? endif;?>

                        <a href="{{ URL::to('stock/movement') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<form name="submit-receive" id="submit-receive">
    <div class="row justify-content-center">
        <div class="col-xxl-12">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="col-4">
                                <input type="hidden" name="ref_no" id="ref_no" value="<?=$mv_hdr->ref_no?>" />
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Company Name <span class="text-danger">*</span></h6>
                                    <p class="fw-medium mb-2" id="billing-name">
                                        <input type="hidden" name="company_id" id="company_id" value="<?=$mv_hdr->company_id?>" />
                                        <select class="form-select select" disabled required="required" id="company" name="company">
                                            <option value="">Select Company</option>                                                            
                                            <? foreach($client_list as $client) : ?>
                                                <option value="<?=$client->id?>" <?=($client->id == $mv_hdr->company_id) ? 'selected': ''; ?> ><?=$client->client_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Site <span class="text-danger">*</span></h6>
                                    <p class="fw-medium mb-2" id="shipping-name">
                                        <input type="hidden" name="store_id" id="store_id" value="<?=$mv_hdr->store_id?>" />
                                        <select class="form-select select" disabled required="required" id="store" name="store">
                                            <option value="">Select Store/Warehouse</option>                                                            
                                        </select>
                                        <span class="text-danger error-msg store_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse <span class="text-danger">*</span></h6>
                                    <p class="fw-medium mb-2" id="shipping-name">
                                        <input type="hidden" name="warehouse_id" id='warehouse_id' value="<?=$mv_hdr->warehouse_id?>" />
                                        <select class="form-select select" disabled required="required" id="warehouse" name="warehouse">
                                            <option value="">Select warehouse</option>                                                            
                                        </select>
                                        <span class="text-danger error-msg warehouse_error"></span>
                                    </p>
                                </div>
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
                <div class="row">
                    <div class="col-lg-12 d-none">
                        <div class="row ms-3 mt-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">PO Number</label> 
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="po_num" name="po_num" value="" placeholder="PO Number">
                                        <span class="text-danger error-msg po_num_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice <span class="text-danger">*</span></label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control"  id="sales_invoice" name="sales_invoice" value="" placeholder="Sales Invoice">
                                        <span class="text-danger error-msg sales_invoice_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-12 mt-3 d-none">
                        <div class="row ms-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">Warehouse <span class="text-danger">*</span></label> 
                                    <div class="col-lg-8">
                                        <select class="form-select select2" required="required" id="warehouse" name="warehouse">
                                            <option value="">Select warehouse</option>                                                            
                                        </select>
                                        <span class="text-danger error-msg warehouse_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Remarks</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" name="remarks" id="remarks" value="" placeholder="Remarks">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--end col-->
                    <div class=" col-lg-12 mt-2">
                        <div class="">
                            <div class="card-header card-title mb-0 flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h5>Item List</h5>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <!-- ITEMS -->
                                <div class="col-md-12 mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroup-sizing-sm"><i class="ri-barcode-line label-icon align-middle rounded-pill fs-16 me-2"></i>Scan Code</span>
                                        <input type="text" class="form-control" aria-label="Recipient's username with two button addons">
                                        <button class="btn btn-warning" id="find-items" type="button"><i class="ri-book-read-line label-icon align-middle rounded-pill fs-16 me-2"></i> Find Item</button>
                                    </div>
                                </div> 
                                
                                <div class="table-responsive">
                                    <table class="table table-nowrap table-bordered movement" id="product-list">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" class="text-center" rowspan="2"> &nbsp;</th>
                                                <th scope="col" class="text-center" rowspan="2" valign="middle">Product Deatails</th>
                                                <th rowspan="2" class="text-center" valign="middle">Item Type</th>
                                                <th colspan="2" class="text-center">Old Location</th>
                                                <th colspan="2" class="text-center">Target Location</th>
                                                <th rowspan="2" class="text-center" valign="middle">Action</th>
                                            </tr>
                                            <tr class="table-active text-center" >
                                                <th>Location</th>
                                                <th >Inv</th>
                                                <th>Location</th>
                                                <th>Inv</th>
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                            <? if($mv_dtl) :  $x=0?>
                                                <? foreach($mv_dtl as $dtl) : ?>
                                                    <tr class="" id="product_<?=$x?>" >
                                                        <td>
                                                            <?=$x+1?>
                                                            <input type="hidden" name="product_id[]" readonly id="product_id_<?=$dtl->product_id?>" value="<?=$dtl->product_id?>" />
                                                            <input type="hidden" name="ref1_no[]" readonly id="product_id_<?=$dtl->product_id?>" value="<?=$dtl->ref1_no?>" />
                                                            <input type="hidden" name="ref1_type[]" readonly id="product_id_<?=$dtl->product_id?>" value="<?=$dtl->ref1_type?>" />
                                                        </td>
                                                        <td class="text-start  fs-12">{{$dtl->item->product_name}}<br/><small>{{$dtl->item->product_code}}</small></td>
                                                        <td class="text-center ps-1 fs-12">
                                                            {{$dtl->old_item_type}}
                                                            <input type="hidden" readonly class="form-control" name="item_type[]" data-id="<?=($x)?>" id="item_type_<?=($x)?>" value="{{$dtl->old_item_type}}">
                                                        </td>
                                                        <td class="text-center ps-1 fs-12">
                                                            <input type="hidden" readonly class="form-control" name="old_location[]" data-id="<?=($x)?>" id="old_location_<?=($x)?>" value="{{$dtl->old_storage_location_id}}">
                                                            {{isset($dtl->old_location->location) ? $dtl->old_location->location : 'RA' }}
                                                        </td>
                                                        <td>
                                                            <div class="input-group"  style="width: 140px;">
                                                                <input type="text" readonly class="form-control input-group-text numeric movement_item" name="old_inv_qty[]" data-id="<?=($x)?>" id="old_inv_qty_<?=($x)?>" value="<?=$dtl->old_inv_qty?>">
                                                                <input type="hidden" readonly class="form-control" name="old_inv_uom[]" data-id="<?=($x)?>" id="old_inv_uom_<?=($x)?>" value="<?=$dtl->old_inv_uom?>">
                                                                <span class="input-group-text">{{$dtl->old_uom->code}}</span>
                                                            </div>
                                                            <span class="text-danger error-msg old_inv_qty<?=($x)?>_error"></span>
                                                        </td>
                                                        <td>
                                                            <select name="new_location[]"  style="width: 120px;"  id="new_location_<?=($x)?>" class="form-select" style="width: 120px;">
                                                                <?=$stock->getNewLocation($mv_hdr->warehouse_id, $dtl->new_storage_location_id);?>
                                                            </select>
                                                            <span class="text-danger error-msg new_location<?=($x)?>_error"></span>
                                                        </td>
                                                        <td>
                                                            <div class="input-group"  style="width: 140px;">
                                                                <input type="text" class="form-control new_inv_qty numeric movement_item" name="new_inv_qty[]" data-id="<?=($x)?>" id="new_inv_qty_<?=($x)?>" value="<?=$dtl->new_inv_qty?>">
                                                                <input type="hidden" readonly class="form-control" name="new_inv_uom[]" data-id="<?=($x)?>" id="new_inv_uom_<?=($x)?>" value="<?=$dtl->new_inv_uom?>">
                                                                <span class="input-group-text">{{$dtl->new_uom->code}}</span>
                                                            </div>
                                                            <span class="text-danger error-msg new_inv_qty<?=($x)?>_error"></span>
                                                        </td>
                                                        <td>
                                                            <div class="text-center">
                                                                <a href="javascript:void(0)" class="text-danger remove-product" data-id="<?=$x?>"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                    <?$x++?>

                                                <? endforeach;?>
                                            
                                            <? else : ?>
                                                <tr class="text-center" >
                                                    <td colspan="8">
                                                        <div class="text-center">
                                                            <h5 class="mt-2">Sorry! No Result Found</h5>
                                                        </div>
                                                    </td>
                                                </tr>
                                            
                                            <? endif;?>
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


    <!-- show charges Modal -->
    <div class="modal" id="show-items" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Product List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
            
                <div class="modal-body">
            
                    <div class="row g-3">
                        <div class="col-3">
                            <h6 class="text-muted text-uppercase fw-semibold">Receiving #</h6>
                            <p class="fw-medium" id="billing-name">
                                <input type="text" class="form-control" id="rcv_no" name="rcv_no" value="" placeholder="Receive Number">
                            </p>
                        </div>

                        <div class="col-3">
                            <h6 class="text-muted text-uppercase fw-semibold">Rack </h6>
                            <p class="fw-medium" id="billing-name">
                                <select class="form-select" id="rack" name="rack">
                                    <option value="">Select Rack</option>  
                                    <? foreach($location as $location_id=>$racks) : ?>
                                        <? foreach($racks as $rack=>$layers) : ?>
                                            <option value="<?=$rack?>"><?=$rack?></option>  
                                        <? endforeach;?>
                                    <? endforeach;?>
                                
                                </select>
                            </p>
                        </div>

                        <div class="col-3">
                            <h6 class="text-muted text-uppercase fw-semibold">Layer</h6>
                            <p class="fw-medium" id="billing-name">
                                <select class="form-select"  id="layer" name="layer">
                                    <option value="">Select layer</option>   
                                </select>
                            </p>
                        </div>

                        <div class="col-3">
                            <h6 class="text-muted text-uppercase fw-semibold">&nbsp;</h6>
                            <p class="fw-medium" id="billing-name">
                                <button data-status="open" class="search-item btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search </button>
                            </p>
                        </div>
                    </div>   

                    <table class="table align-middle" width="100%" style="font-size: 12px;" id="show-items-list">
                        <thead class="table-light">
                            <tr>
                            <th>&nbsp;</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Rack</th>
                                <th>Layer</th>
                                <th>Item Type</th>
                                <th>Inv Qty</th>
                                <th>Inv Uom</th>
                                <th>Whse qty</th>
                                <th>Whse Uom</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-success" id="add-product"><i class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add</button>
                    </div>
                </div>
            </div>
        </div>
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
<script src="{{ URL::asset('/assets/js/stock/movement.js') }}"></script>

@endsection