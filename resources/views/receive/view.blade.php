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
@slot('title') Receive Order @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-xxl-11">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="align-items-center">
                    <div class="row">
                        <div class="col-xxl-4 text-start">
                            <h5 class="card-title mb-0 flex-grow-1"><?=$rcv->rcv_no?> </h5>
                        </div>
                        <div class="col-lg-4 text-center">
                            <span class="badge  fs-16 <?=$rcv->status?> text-uppercase"><?=$rcv->status?></span>
                        </div>
                        <div class="col-lg-4 text-end">
                            <?php  if ($rcv->status == 'open') : ?>
                            <a href="{{ URL::to('receive') }}/<?=_encode($rcv->id)?>/edit" class="btn btn-success btn-label rounded-pill"><i class="ri-pencil-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                            <? endif;?>
                            <a href="{{ URL::to('receive') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                        </div>
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
        <div class="col-xxl-11">
            <div class="card">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card-body p-4 ">
                            <div class="row g-3">
                                <div class="col-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Supplier Name</h6>
                                    <input type="hidden" name="rcv_no" id="rcv_no" value="<?=$rcv->rcv_no?>" />
                                    <p class="fw-medium mb-2" id="shipping-name">
                                        <select class="form-select select2" disabled required="required" id="supplier" name="supplier">
                                            <option value="">Select Supplier</option>                                                            
                                            <? foreach($supplier_list as $supplier) : ?>
                                                <option value="<?=$supplier->id?>" <?=($supplier->id == $rcv->supplier_id) ? 'selected': ''; ?> ><?=$supplier->supplier_name?></option>
                                            <? endforeach;?>
                                        </select> 
                                        <span class="text-danger error-msg supplier_error"></span>
                                    </p>
                                    <!-- <p class="text-muted mb-1" id="shipping-address-line-1">supp_add  here</p>
                                    <p class="text-muted mb-1">supp_add2 here</p>
                                    <p class="text-muted mb-0">supp_city province, country here</p> -->
                                </div>

                                <div class="col-4">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name</h6>
                                    <p class="fw-medium mb-2" id="billing-name">
                                        <input type="hidden" name="client_id" id="client_id" value="<?=$rcv->client_id?>" />
                                        <select class="form-select select2" disabled required="required" id="client" name="client">
                                            <option value="">Select Client</option>                                                            
                                            <? foreach($client_list as $client) : ?>
                                                <option value="<?=$client->id?>" <?=($client->id == $rcv->client_id) ? 'selected': ''; ?> ><?=$client->client_name?></option>
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
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Site Address</h6>
                                    <p class="fw-medium mb-2" id="shipping-name">
                                        <input type="hidden" name="store_id" id="store_id" value="<?=$rcv->store_id?>" />
                                        <select class="form-select select2" disabled required="required" id="store" name="store">
                                            <option value="">Select Store/Warehouse</option>                                                            
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
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">PO Number</label> 
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="po_num" disabled name="po_num" value="<?=isset($rcv->po_num) ? $rcv->po_num : '' ?>" placeholder="PO Number">
                                        <span class="text-danger error-msg po_num_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" disabled id="sales_invoice" name="sales_invoice" value="<?=isset($rcv->sales_invoice) ? $rcv->sales_invoice : '' ?>" placeholder="Sales Invoice">
                                        <span class="text-danger error-msg sales_invoice_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="row ms-3 mt-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">Received By</label> 
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" disabled  id="received_by" name="received_by" value="<?=isset($rcv->received_by) ? $rcv->received_by : '' ?>" placeholder="Received By">
                                        <span class="text-danger error-msg received_by_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Date Received</label>
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control"  disabled id="date_received" name="date_received" value="<?=isset($rcv->date_received) ? date("Y-m-d", strtotime($rcv->date_received)) : '' ?>" placeholder="Date Received">
                                        <span class="text-danger error-msg date_received_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="row ms-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">Inspect By</label> 
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" disabled  id="inspect_by" name="inspect_by" value="<?=isset($rcv->inspect_by) ? $rcv->inspect_by : '' ?>" placeholder="Inspect By">
                                        <span class="text-danger error-msg inspect_by_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Inspect Date</label>
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control" disabled   id="inspect_date" name="inspect_date" value="<?=isset($rcv->inspect_date) ? $rcv->inspect_date : '' ?>" placeholder="Inspect Date">
                                        <span class="text-danger error-msg inspect_date_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="row ms-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="date_arrived" class="col-lg-4 col-form-label">Date Arrived</label> 
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control" disabled  name="date_arrived" id="date_arrived" value="<?=isset($rcv->date_arrived) ? date("Y-m-d", strtotime($rcv->date_arrived)) : '' ?>" placeholder="DD-MM-YYYY">
                                        <span class="text-danger error-msg date_arrived_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="time_arrived" class="col-lg-4  col-form-label">Time Arrived</label>
                                    <div class="col-lg-8">
                                        <input type="time" class="form-control" disabled  name="time_arrived" id="time_arrived" value="<?=isset($rcv->date_arrived) ? date("H:i", strtotime($rcv->date_arrived)) : '' ?>" placeholder="hh:mm" id="cleave-time">
                                        <span class="text-danger error-msg time_arrived_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="row ms-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">Date Departed</label> 
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control"  disabled name="date_departed" id="date_departed" value="<?=isset($rcv->date_departed) ? date("Y-m-d", strtotime($rcv->date_departed)) : '' ?>" placeholder="DD-MM-YYYY">
                                        <span class="text-danger error-msg date_departed_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="time_departed" class="col-lg-4  col-form-label">Time Departed</label>
                                    <div class="col-lg-8">
                                        <input type="time" class="form-control" disabled  name="time_departed" id="time_departed" value="<?=isset($rcv->date_departed) ? date("H:i", strtotime($rcv->date_departed)) : '' ?>" placeholder="hh:mm" id="cleave-time">
                                        <span class="text-danger error-msg time_departed_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="row ms-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">Plate Number</label> 
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" disabled  name="plate_no" id="plate_no" value="<?=isset($rcv->plate_no) ? $rcv->plate_no : '' ?>" placeholder="Plate Number">
                                        <span class="text-danger error-msg plate_no_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Truck Type</label>
                                    <div class="col-lg-8">
                                        <select class="form-select select2"  disabled required="required" id="truck_type" name="truck_type">
                                            <option value="">Select Truck Type</option>                                                            
                                            <? foreach($truck_type_list as $truck) : ?>
                                                <option value="<?=$truck->id?>" <?=($truck->id == $rcv->truck_type) ? 'selected': ''; ?> ><?=$truck->vehicle_desc?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg truck_type_error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-3">
                        <div class="row ms-3 mx-3">
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4 col-form-label">Warehouse</label> 
                                    <div class="col-lg-8">
                                        <input type="hidden" name="warehouse_id" id='warehouse_id' value="<?=isset($rcv->warehouse_id) ? $rcv->warehouse_id : ''?>" />
                                        <select class="form-select select2"  disabled  required="required" id="warehouse" name="warehouse">
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
                                        <input type="text" class="form-control" disabled   value="<?=isset($rcv->remarks) ? $rcv->remarks : '' ?>" placeholder="Remarks">
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
                                    <h5>Receiving Details</h5>
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
                                    <table class="table table-nowrap" id="product-list">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col"  style="width: 10px;">#</th>
                                                <th scope="col" >Product</th>
                                                <th scope="col" >Classfication</th>
                                                <th scope="col" >WHSE Qty</th>
                                                <th scope="col" >WHSE UOM</th>
                                                <th scope="col" >Inv Qty</th>
                                                <th scope="col" >Inv UOM</th>
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                            <? 
                                            $rowCount = count($rcv->items);
                                            $x=1;
                                             ?>
                                            @if(isset($rcv->items))
                                                @foreach($rcv->items as $item)
                                                <tr id="product_{{$item->product_id}}">
                                                    <td class="text-start fs-12">
                                                        <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />
                                                    {{$x++}} </td>
                                                    <td class="text-start fs-12"> 
                                                        {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                    </td>
                                                    <td class="text-start fs-12"> 
                                                        <select name="item_type[]"  disabled  id="item_type_{{$x}}" class="uom uom_select form-select">
                                                            <option class="fs-8" value="good"   <?=($item->item_type == 'good')? 'selected':''?>>Good</option> 
                                                            <option class="fs-8" value="damage" <?=($item->item_type == 'damage')? 'selected':''?>>Damage</option>  
                                                            <option class="fs-8" value="repair" <?=($item->item_type == 'repair')? 'selected':''?>>Repair</option>                                                                                                                    
                                                        </select>
                                                    </td>
                                                    <td class="fs-12">
                                                        <input type="text"  disabled  class="form-control numeric whse_qty uom_select fs-12" name="whse_qty[]" data-id="{{$item->product_id}}" id="whse_qty_{{$x}}" value="{{$item->whse_qty}}" placeholder="Whse Qty" />
                                                    </td>
                                                    <td class="fs-12">
                                                       <select name="whse_uom[]"  disabled id="whse_uom_{{$x}}" class="uom uom_select form-select">
                                                            <option value="">Select UOM</option>                                                            
                                                            @foreach($uom_list as $uom_whse)
                                                            <option value="{{$uom_whse->uom_id}}" <?=($uom_whse->uom_id == $item->whse_uom) ? 'selected': ''; ?> >{{$uom_whse->code}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="fs-12">
                                                        <input type="text" disabled class="form-control inv_qty numeric uom_select" name="inv_qty[]" data-id="{{$item->product_id}}" id="inv_qty_{{$x}}" value="{{$item->inv_qty}}" placeholder="Inv Qty" />
                                                    </td>
                                                    <td class=" ps-1">
                                                        <select name="inv_uom[]"  disabled  id="inv_uom_{{$x}}" class="uom uom_select form-select">
                                                            <option value="">Select UOM</option>                                                            
                                                            @foreach($uom_list as $uom)
                                                            <option value="{{$uom->uom_id}}" <?=($uom->uom_id == $item->inv_uom) ? 'selected': ''; ?> >{{$uom->code}}</option>
                                                            @endforeach
                                                        </select>
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
<div class="modal" id="show-items" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Product List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
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
                    <button type="button" class="btn btn-success" id="add-product"><i class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add</button>
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
<script src="{{ URL::asset('/assets/js/receive/receive.js') }}"></script>

@endsection