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
                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Supplier Name</h6>
                                    <input type="hidden" name="rcv_no" id="rcv_no" />
                                    <input type="hidden" name="hasPo" id="hasPo" value="1" />
                                    <p class="mb-2">
                                        <input type="hidden" name="supplier" id="supplier" value="<?=$po->supplier_id?>" />
                                        <select class="form-select select2" disabled required="required" id="supplier1" name="supplier1">
                                            <option value="">Select Supplier</option>
                                            <? foreach($supplier_list as $supplier) : ?>
                                                <option value="<?=$supplier->id?>" <?=($supplier->id == $po->supplier_id) ? 'selected': ''; ?> ><?=$supplier->supplier_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg supplier_error"></span>
                                    </p>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Customer Name</h6>
                                    <p class="mb-2">
                                        <select class="form-select select2" id="customer" name="customer">
                                            <option value="">Select customer</option>
                                            <? foreach($client_list as $customer) : ?>
                                                <? if(strtoupper($customer->client_type) == 'C') : ?>
                                                    <option value="<?=$customer->id?>"  <?=($customer->id == $po->customer_id) ? 'selected': ''; ?> ><?=$customer->client_name?></option>
                                                <? endif; ?>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>
                                <!--end col-->

                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Company Name</h6>
                                    <p class="mb-2">
                                        <input type="hidden" name="company_id" id="company_id" value="<?=$po->company_id?>" />
                                        <select class="form-select select2" id="company" name="company">
                                            <option value="">Select company</option>
                                            <? foreach($client_list as $company) : ?>
                                                <? if(strtoupper($company->client_type) == 'O') : ?>
                                                    <option value="<?=$company->id?>"  <?=($company->id == $po->company_id) ? 'selected': ''; ?> ><?=$company->client_name?></option>
                                                <? endif ?>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg client_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-md-6">
                                    <h6 class="text-muted mb-3">Site Name</h6>
                                    <p class=" mb-2">
                                        <input type="hidden" name="store_id" id="store_id" value="<?=$po->store_id?>" />
                                        <select class="form-select select2 disabled" readonly required="required" id="store" name="store">
                                            <option value="">Select Site/Warehouse</option>
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
                                        <input type="date" class="form-control" id="date_received" name="date_received" value="<?=date("Y-m-d")?>" placeholder="Date Received">
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
                                        <input type="date" class="form-control"  id="inspect_date" name="inspect_date" value="<?=date("Y-m-d")?>" placeholder="Inspect Date">
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
                                        <input type="date" class="form-control" name="date_arrived" id="date_arrived" value="<?=date("Y-m-d")?>" placeholder="DD-MM-YYYY">
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
                                        <label for="colFormLabel" class="col-lg-4 col-form-label">Start Unloading<span
                                                class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                                <div class="input-group">
                                                    <input type="date" class="form-control" id="start_unloading_date"
                                                        name="start_unloading_date" placeholder="Start Date" value="<?=date('Y-m-d');?>">
                                                    <input type="time" class="form-control" id="start_unloading_time"
                                                        name="start_unloading_time" placeholder="Start Time" value="{{ date('H:i') }}">
                                                </div>
                                                <span class="text-danger error-msg start_unloading_date_error"></span>
                                                <span class="text-danger error-msg start_unloading_time_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                    <label for="colFormLabel" class="col-form-label col-lg-4">Finish Unloading<span
                                            class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="finish_unloading_date"
                                                    name="finish_unloading_date" placeholder="Finish Date" value="<?=date('Y-m-d');?>">
                                                <input type="time" class="form-control" id="finish_unloading_time"
                                                    name="finish_unloading_time" placeholder="Finish Time" value="<?=date('H:i')?>">
                                            </div>
                                            <span class="text-danger error-msg finish_unloading_date_error"></span>
                                            <span class="text-danger error-msg finish_unloading_time_error"></span>
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
                                        <input type="date" class="form-control" name="date_departed" id="date_departed" value="<?=date("Y-m-d")?>" placeholder="DD-MM-YYYY">
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
                                                <th scope="col" class="d-none"  style="width: 10px;">#</th>
                                                <th scope="col" >Product</th>
                                                <th scope="col" >Item Type</th>
                                                <th scope="col" >WHSE Qty</th>
                                                <th scope="col" >WHSE UOM</th>
                                                <th scope="col" >Inv Qty</th>
                                                <th scope="col" >Inv UOM</th>
                                                <th scope="col" >Manufacturing Date</th>
                                                <th scope="col" >Lot/Batch</th>
                                                <th scope="col" >Expiry Date</th>
                                                <th scope="col" >Remarks</th>
                                                <th scope="col" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="newlink">
                                        <?
                                            $rowCount = count($po->items);
                                            $total_whse_qty = 0;
                                            $total_inv_qty = 0;
                                            $x=1;
                                            $i=0;
                                             ?>
                                            @if(isset($po->items))
                                                @foreach($po->items as $item)
                                                    @if($item->available_qty > 0)
                                                    <?
                                                        $total_whse_qty += $item->available_qty;
                                                        $total_inv_qty += $item->available_qty;
                                                    ?>
                                                    <tr id="R{{$i}}">
                                                        <td class="text-start d-none">
                                                            <input type="hidden" name="product_id[]" readonly id="product_id_{{$item->product_id}}" value="{{$item->product_id}}" />

                                                        {{$x++}} </td>
                                                        <td class="text-start fs-13" >
                                                            {{$item->product->product_name}}<br/><small>{{$item->product->product_code}}</small>
                                                            <input type="hidden" name="po_dtl_id[]" readonly id="product_id_{{$item->id}}" value="{{$item->id}}" />
                                                            <input type="hidden" name="product_code[]" value="{{$item->product->product_code}}" />
                                                        </td>
                                                        <td class="text-start c_item_type">
                                                            <select name="item_type[]" id="item_type_{{$x}}" class="uom uom_select form-select">
                                                                <option value="good">Good</option>
                                                                <option value="damage">Damage</option>
                                                                <option value="repair">Repair</option>
                                                            </select>
                                                            <input type="hidden" name="available_qty[]" readonly name="available_qty[]" id="available_qty_{{$item->product_id}}" value="{{number_format($item->available_qty,0)}}" />
                                                            <span id="item_type" class="text-danger error-msg item_type{{$i}}_error"></span>
                                                        </td>
                                                        <td class=" ps-1 c_whse_qty">
                                                            <input type="text" style="width: 70px;" class="form-control text-end numeric whse_qty uom_select" name="whse_qty[]" data-id="{{$item->product_id}}" id="whse_qty_{{$item->product_id}}" value="{{number_format($item->available_qty,0)}}" placeholder="Whse Qty" />
                                                            <span id="whse_qty" class="text-danger error-msg whse_qty{{$i}}_error"></span>
                                                        </td>
                                                        <td class=" ps-1 c_whse_uom">
                                                        <select name="whse_uom[]" id="uom_{{$item->product_id}}" data-id="{{$item->product_id}}"  class="uom whse_uom uom_select form-select">
                                                                <option value="">Select UOM</option>
                                                                @foreach($uom_list as $uom_whse)
                                                                <option value="{{$uom_whse->uom_id}}" <?=($uom_whse->uom_id == $item->uom_id) ? 'selected': ''; ?> >{{$uom_whse->code}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span id="whse_uom" class="text-danger error-msg whse_uom{{$i}}_error"></span>
                                                        </td>
                                                        <td class="ps-1 c_inv_qty">
                                                            <input type="text" style="width: 70px;" class="form-control text-end  inv_qty numeric uom_select" name="inv_qty[]" data-id="{{$item->product_id}}" id="inv_qty_{{$item->product_id}}" value="{{number_format($item->available_qty,0)}}" placeholder="Inv Qty" />
                                                            <span id="inv_qty" class="text-danger error-msg inv_qty{{$i}}_error"></span>
                                                        </td>
                                                        <td class=" ps-1 c_inv_uom">
                                                            <select name="inv_uom[]" id="inv_uom_{{$item->product_id}}" data-id="{{$item->product_id}}"  class="uom uom_select form-select">
                                                                <option value="">Select UOM</option>
                                                                @foreach($uom_list as $uom)
                                                                <option value="{{$uom->uom_id}}" <?=($uom->uom_id == $item->uom_id) ? 'selected': ''; ?> >{{$uom->code}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span id="inv_uom" class="text-danger error-msg inv_uom{{$i}}_error"></span>
                                                        </td>
                                                        <td class="ps-1">
                                                            <input type="date" class="form-control" style="width: 150px;" name="manufacture_date[]" value="{{$item->manufacture_date}}" placeholder="Manufacturing Date" />
                                                        </td>
                                                        <td class="ps-1">
                                                            <input type="text" class="form-control" style="width: 150px;" name="lot_no[]" value="{{$item->lot_no}}" placeholder="Lot/Batch No" />
                                                        </td>
                                                        <td class="ps-1">
                                                            <input type="date" class="form-control " name="expiry_date[]"  value="{{$item->expiry_date}}" placeholder="Expiry Date" />
                                                        </td>
                                                        <td class="ps-1">
                                                            <input type="text" class="form-control" style="width: 150px;" name="item_remarks[]"  value="{{$item->remarks}}" placeholder="Remarks" />
                                                        </td>
                                                        <td>
                                                            <div class="text-center">
                                                                <a href="javascript:void(0)" class="text-info split-row" data-id="{{$item->product_id}}"><i class=" ri-menu-add-line label-icon align-middle rounded-pill fs-16 me-1"></i>Split</a> |

                                                                <a href="javascript:void(0)" class="text-danger remove-product" data-id="{{$item->product_id}}">
                                                                    <i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-1"></i>Remove
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    <?$i++;?>
                                                @endforeach
                                            @else
                                            <tr class="">
                                                <td colspan="8" class="text-danger text-center">No Record Found!</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                                <td colspan='2' class="fw-semibold">Total</td>
                                                <td class="text-end fw-medium"><input type="text" class="form-control border-0 text-end" id="total_whse_qty" value="{{$total_whse_qty}}" placeholder="0.00" readonly /></td>
                                                <td class="text-end">&nbsp;</td>
                                                <td class="text-end fw-medium"><input type="text" class="form-control border-0 text-end" id="total_inv_qty" value="{{$total_inv_qty}}" placeholder="0.00" readonly /></td>
                                                <td colspan='6'>&nbsp;</td>
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
