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
                        <div class="col-lg-3 text-start">
                            <h5 class="card-title mb-0 flex-grow-1"><?=$rcv->rcv_no?> </h5>
                        </div>
                        <div class="col-lg-3 text-center">
                            <span class="badge  fs-16 <?=$rcv->status?> text-uppercase"><?=$rcv->status?></span>
                        </div>
                        <div class="col-lg-6 text-end">

                        <? if(in_array($rcv->status, array('posted'))) : ?>
                            <? if (mod_access('rcv',  'unpost', Auth::id())) : ?>
                                <button type="button" data-status="unpost" class="btn btn-info btn-label rounded-pill submit-unpost"><i class=" ri-lock-unlock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Unpost</button>
                            <? endif ;?>
                        <? endif;?>

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
                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Supplier Name</h6>
                                    <input type="hidden" name="rcv_no" id="rcv_no" value="<?=$rcv->rcv_no?>" />
                                    <p class=" mb-2">
                                        <select class="form-select select2" disabled required="required" id="supplier" name="supplier">
                                            <option value="">Select Supplier</option>
                                            <? foreach($supplier_list as $supplier) : ?>
                                                <option value="<?=$supplier->id?>" <?=($supplier->id == $rcv->supplier_id) ? 'selected': ''; ?> ><?=$supplier->supplier_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg supplier_error"></span>
                                    </p>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Customer</h6>
                                    <p class=" mb-2">
                                        <select class="form-select select2" disabled id="customer" name="customer">
                                            <option value="">Select customer</option>
                                            <? foreach($client_list as $customer) : ?>
                                                <? if(strtoupper($customer->client_type) == 'C') : ?>
                                                    <option value="<?=$customer->id?>" <?=($customer->id == $rcv->customer_id) ? 'selected': ''; ?> ><?=$customer->client_name?></option>
                                                <? endif;?>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Company</h6>
                                    <p class=" mb-2">
                                        <input type="hidden" name="company_id" id="company_id" value="<?=$rcv->company_id?>" />
                                        <select class="form-select select2" disabled  id="company" name="company">
                                            <option value="">Select Client</option>
                                            <? foreach($client_list as $company) : ?>
                                                <? if(strtoupper($company->client_type) == 'O') : ?>
                                                    <option value="<?=$company->id?>" <?=($company->id == $rcv->company_id) ? 'selected': ''; ?> ><?=$company->client_name?></option>
                                                <? endif;?>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Site Name</h6>
                                    <p class="mb-2">
                                        <input type="hidden" name="store_id" id="store_id" value="<?=$rcv->store_id?>" />
                                        <select class="form-select select2" disabled required="required" id="store" name="store">
                                            <option value="">Select Store/Warehouse</option>
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
                                    <label for="start_unloading" class="col-lg-4 col-form-label">Start Unloading</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" disabled  name="start_unloading" id="start_unloading" value="<?=isset($rcv->start_unloading) ? date("m/d/Y H:i A", strtotime($rcv->start_unloading)) : '' ?>" placeholder="DD-MM-YYYY">
                                        <span class="text-danger error-msg start_unloading_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="row">
                                    <label for="finish_unloading" class="col-lg-4  col-form-label">Finish Unloading</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" disabled  name="finish_unloading" id="finish_unloading" value="<?=isset($rcv->finish_unloading) ? date("m/d/Y H:i A", strtotime($rcv->finish_unloading)) : '' ?>" placeholder="hh:mm" id="cleave-time">
                                        <span class="text-danger error-msg finish_unloading_error"></span>
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
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-nowrap" id="product-list">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col"  style="width: 10px;" class="d-none">#</th>
                                                <th scope="col" >Product</th>
                                                <th scope="col" >Classfication</th>
                                                <th scope="col" >WHSE Qty</th>
                                                <th scope="col" >WHSE UOM</th>
                                                <th scope="col" >Inv Qty</th>
                                                <th scope="col" >Inv UOM</th>
                                                <th scope="col" >Manufacturing Date</th>
                                                <th scope="col" >Lot/Batch</th>
                                                <th scope="col" >Expiry Date</th>
                                                <th scope="col" >Remarks</th>
                                                @if (in_array($rcv->status, array('posted')))
                                                    @if (mod_access('rcv',  'modify', Auth::id()))
                                                        <th scope="col" class="text-center" style="width: 50px;">Action</th>
                                                    @endif
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                            <?
                                            $rowCount = count($rcv->items);
                                            $total_whse_qty = 0;
                                            $total_inv_qty = 0;
                                            $x=1;
                                             ?>
                                            @if(isset($rcv->items))
                                                @foreach($rcv->items as $item)
                                                <?
                                                    $total_whse_qty += $item->whse_qty;
                                                    $total_inv_qty += $item->inv_qty;
                                                ?>
                                                <tr id="product_{{$item->product_id}}">
                                                    <td class="text-start fs-12 d-none">
                                                        <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />
                                                    {{$x++}} </td>
                                                    <td class="text-start fs-12">
                                                        {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                        <input type="hidden" name="po_dtl_id[]" readonly id="product_id_{{$item->po_dtl_id}}" value="{{$item->po_dtl_id}}" />
                                                    </td>
                                                    <td class="text-start fs-12">
                                                        <select name="item_type[]"  disabled  id="item_type_{{$x}}" class="uom uom_select form-select">
                                                            <option class="fs-8" value="good"   <?=($item->item_type == 'good')? 'selected':''?>>Good</option>
                                                            <option class="fs-8" value="damage" <?=($item->item_type == 'damage')? 'selected':''?>>Damage</option>
                                                            <option class="fs-8" value="repair" <?=($item->item_type == 'repair')? 'selected':''?>>Repair</option>
                                                        </select>
                                                    </td>
                                                    <td class="fs-12 ">
                                                        <input type="text"  disabled  class="form-control numeric text-end whse_qty uom_select fs-12" name="whse_qty[]" data-id="{{$item->product_id}}" id="whse_qty_{{$x}}" value="{{$item->whse_qty}}" placeholder="Whse Qty" />
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
                                                        <input type="text" disabled class="form-control inv_qty  text-end numeric uom_select" name="inv_qty[]" data-id="{{$item->product_id}}" id="inv_qty_{{$x}}" value="{{$item->inv_qty}}" placeholder="Inv Qty" />
                                                    </td>
                                                    <td class=" ps-1">
                                                        <select name="inv_uom[]"  disabled  id="inv_uom_{{$x}}" class="uom uom_select form-select">
                                                            <option value="">Select UOM</option>
                                                            @foreach($uom_list as $uom)
                                                            <option value="{{$uom->uom_id}}" <?=($uom->uom_id == $item->inv_uom) ? 'selected': ''; ?> >{{$uom->code}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="ps-1">
                                                        <input type="date" class="form-control manufacture_date" {{ mod_access('rcv', 'modify', Auth::id()) ? '' : 'disabled' }} style="width: 150px;" id="manufacture_date_{{$x}}" name="manufacture_date[]" value="{{$item->manufacture_date}}" placeholder="Manufacturing Date" />
                                                    </td>
                                                    <td class="ps-1">
                                                        <input type="text" class="form-control lot_no" {{ mod_access('rcv', 'modify', Auth::id()) ? '' : 'disabled' }} style="width: 150px;" id="lot_no_{{$x}}" value="{{$item->lot_no}}" placeholder="Lot/Batch No" />
                                                    </td>
                                                    <td class="ps-1">
                                                        <input type="date" class="form-control expiry_date" {{ mod_access('rcv', 'modify', Auth::id()) ? '' : 'disabled' }} name="expiry_date[]" id="expiry_date_{{$x}}" value="{{$item->expiry_date}}" placeholder="Expiry Date" />
                                                    </td>
                                                    <td class="ps-1">
                                                        <input type="text" class="form-control item_remarks" {{ mod_access('rcv', 'modify', Auth::id()) ? '' : 'disabled' }} style="width: 150px;" name="item_remarks[]" id="item_remarks_{{$x}}"  value="{{$item->remarks}}" placeholder="Remarks" />
                                                    </td>
                                                    @if (in_array($rcv->status, array('posted')))
                                                    @if (mod_access('rcv',  'modify', Auth::id()))
                                                        <td class="ps-1 text-center">
                                                            <button type="button" class="btn btn-info btn-sm update-item" id="item-{{ $item->id }}" data-id="{{$item->id}}" data-row-id={{ $x }} title="Update Item"><i class="ri-edit-line"></i> Modify</button>
                                                        </td>
                                                    @endif
                                                @endif
                                                </tr>
                                                @endforeach
                                            @else
                                            <tr class="">
                                                <td colspan="8" class="text-danger text-center">No Record Found!</td>
                                            </tr>
                                            @endif

                                        </tbody>
                                        <tfoot>
                                            <td colspan='2' class="fw-semibold">Total</td>
                                            <td class="text-end fw-medium"><?=$total_whse_qty?></td>
                                            <td class="text-end">&nbsp;</td>
                                            <td class="text-end fw-medium"><?=$total_inv_qty?></td>
                                            <td colspan='4'>&nbsp;</td>
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
