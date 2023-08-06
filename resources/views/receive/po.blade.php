@extends('layouts.master')
@section('title') Receive PO @endsection
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
@slot('title') Receive PO @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-xxl-11">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"></h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                        <a href="{{ URL::to('receive') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
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
                                    <input type="hidden" name="rcv_no" id="rcv_no" />
                                    <p class="fw-medium mb-2" id="shipping-name">
                                        <input type="hidden" name="supplier" id="supplier" value="<?=$po->supplier_id?>" />
                                        <select class="form-select select2" disabled required="required" id="supplier1" name="supplier1">
                                            <option value="">Select Supplier</option>                                                            
                                            <? foreach($supplier_list as $supplier) : ?>
                                                <option value="<?=$supplier->id?>" <?=($supplier->id == $po->supplier_id) ? 'selected': ''; ?> ><?=$supplier->supplier_name?></option>
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
                                        <input type="hidden" name="client_id" id="client_id" value="<?=$po->client_id?>" />
                                        <input type="hidden" name="client" id="client" value="<?=$po->client_id?>" />
                                        <select class="form-select select2" disabled required="required" id="client1" name="client1">
                                            <option value="">Select Client</option>                                                            
                                            <? foreach($client_list as $client) : ?>
                                                <option value="<?=$client->id?>"  <?=($client->id == $po->client_id) ? 'selected': ''; ?> ><?=$client->client_name?></option>
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
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse / Store Address</h6>
                                    <p class="fw-medium mb-2" id="shipping-name">
                                        <input type="hidden" name="store_id" id="store_id" value="<?=$po->store_id?>" />
                                        <select class="form-select select2 disabled" readonly required="required" id="store" name="store">
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
                                        <input type="text" class="form-control disabled" id="po_num" name="po_num" readonly value="{{$po->po_num}}" placeholder="PO Number">
                                        <span class="text-danger error-msg po_num_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control"  id="sales_invoice" name="sales_invoice" value="" placeholder="Sales Invoice">
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
                                        <input type="text" class="form-control" id="received_by" name="received_by" value="" placeholder="Received By">
                                        <span class="text-danger error-msg received_by_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Date Received</label>
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control" id="date_received" name="date_received" value="" placeholder="Date Received">
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
                                        <input type="text" class="form-control" id="inspect_by" name="inspect_by" value="" placeholder="Inspect By">
                                        <span class="text-danger error-msg inspect_by_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Inspect Date</label>
                                    <div class="col-lg-8">
                                        <input type="date" class="form-control"  id="inspect_date" name="inspect_date" value="" placeholder="Inspect Date">
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
                                        <input type="date" class="form-control" name="date_arrived" id="date_arrived" placeholder="DD-MM-YYYY">
                                        <span class="text-danger error-msg date_arrived_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="time_arrived" class="col-lg-4  col-form-label">Time Arrived</label>
                                    <div class="col-lg-8">
                                        <input type="time" class="form-control" name="time_arrived" id="time_arrived" placeholder="hh:mm" id="cleave-time">
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
                                        <input type="date" class="form-control" name="date_departed" id="date_departed" placeholder="DD-MM-YYYY">
                                        <span class="text-danger error-msg date_departed_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="time_departed" class="col-lg-4  col-form-label">Time Departed</label>
                                    <div class="col-lg-8">
                                        <input type="time" class="form-control" name="time_departed" id="time_departed" placeholder="hh:mm" id="cleave-time">
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
                                        <input type="text" class="form-control" name="plate_no" id="plate_no" value="" placeholder="Plate Number">
                                        <span class="text-danger error-msg plate_no_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="colFormLabel" class="col-lg-4  col-form-label">Truck Type</label>
                                    <div class="col-lg-8">
                                        <select class="form-select select2" required="required" id="truck_type" name="truck_type">
                                            <option value="">Select Truck Type</option>                                                            
                                            <? foreach($truck_type_list as $truck) : ?>
                                                <option value="<?=$truck->id?>" ><?=$truck->vehicle_desc?></option>
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
                                        <input type="text" class="form-control" value="" placeholder="Remarks">
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
                                                <th scope="col" >Item Type</th>
                                                <th scope="col" >WHSE Qty</th>
                                                <th scope="col" >WHSE UOM</th>
                                                <th scope="col" >Inv Qty</th>
                                                <th scope="col" >Inv UOM</th>
                                                <th scope="col" >Lot/Batch #</th>
                                                <th scope="col" >Expiry Date</th>
                                                <th scope="col" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                        <? 
                                            $rowCount = count($po->items);
                                            $x=1;
                                             ?>
                                            @if(isset($po->items))
                                                @foreach($po->items as $item)
                                                <tr id="product_{{$item->product_id}}">
                                                    <td class="text-start">
                                                        <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />
                                                    {{$x++}} </td>
                                                    <td class="text-start fs-14"> 
                                                        {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                    </td>
                                                    <td class="text-start"> 
                                                        <select name="item_type[]" id="item_type_{{$x}}" class="uom uom_select form-select">
                                                            <option value="good">Good</option> 
                                                            <option value="damage">Damage</option>  
                                                            <option value="repair">Repair</option>                                                            
                                                        </select>
                                                    </td>
                                                    <td class=" ps-1">
                                                        <input type="text" class="form-control numeric whse_qty uom_select" name="whse_qty[]" data-id="{{$item->product_id}}" id="whse_qty_{{$x}}" value="{{$item->requested_qty}}" placeholder="Whse Qty" />
                                                    </td>
                                                    <td class=" ps-1">
                                                       <select name="whse_uom[]" id="whse_uom_{{$x}}" class="uom uom_select form-select">
                                                            <option value="">Select UOM</option>                                                            
                                                            @foreach($uom_list as $uom_whse)
                                                            <option value="{{$uom_whse->uom_id}}" <?=($uom_whse->uom_id == $item->uom_id) ? 'selected': ''; ?> >{{$uom_whse->code}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="ps-1">
                                                        <input type="text" class="form-control inv_qty numeric uom_select" name="inv_qty[]" data-id="{{$item->product_id}}" id="inv_qty_{{$x}}" value="{{$item->requested_qty}}" placeholder="Inv Qty" />
                                                    </td>
                                                    <td class=" ps-1">
                                                        <select name="inv_uom[]" id="inv_uom_{{$x}}" class="uom uom_select form-select">
                                                            <option value="">Select UOM</option>                                                            
                                                            @foreach($uom_list as $uom)
                                                            <option value="{{$uom->uom_id}}" <?=($uom->uom_id == $item->uom_id) ? 'selected': ''; ?> >{{$uom->code}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-start ps-1">
                                                        <small>-</small>
                                                    </td>
                                                    <td class="text-start ps-1">
                                                        <small>-</small>
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