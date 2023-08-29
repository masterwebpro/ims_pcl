@extends('layouts.master')
@section('title') Purchase Order @endsection
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
@slot('title') Purchase Order @endslot
@endcomponent


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">PO Creation</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <? if($po->status != 'posted') : ?>
                        <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-po"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <button type="button" data-status="posted" class="btn btn-info btn-label rounded-pill submit-po"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                        <? endif;?>
                        <a  href="{{ URL::to('po') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form  method="POST" name="form-po" action="javascript:void(0);" id="form-po" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="card-body">
                <div class="form-container">                   
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4">
                                <div class="col-md-4 form-group">
                                    <label for="po_num" class="form-label">Po Number <span class="text-danger">*</span></label>
                                    <input type="hidden" class="form-control" name="po_id" id="po_id" value="{{ $po->id }}">
                                    <input type="text" class="form-control" name="po_num" id="po_num" value="{{ $po->po_num }}" placeholder="Enter Po Number">
                                    <div class="invalid-feedback error-msg po_num_error">PO Number is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="inputZip" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                    <select class="form-select select2" required="required" id="supplier" name="supplier">
                                        <option value="">Select Supplier</option>                                                            
                                        <? foreach($supplier_list as $supplier) : ?>
                                            <option value="<?=$supplier->id?>" <?=($supplier->id == $po->supplier_id) ? 'selected' : ''?> ><?=$supplier->supplier_name?></option>
                                        <? endforeach;?>
                                    </select>
                                    <div class="invalid-feedback text-danger error-msg supplier_error">Supplier is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="inputState" class="form-label">PO Date <span class="text-danger">*</span></label>
                                    <input type="date" id="po_date" required="required"  name="po_date" class="form-control" value="<?=isset($po->po_date) ? $po->po_date : ''?>">
                                    <div class="invalid-feedback text-danger error-msg po_date_error">PO Date is Required</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="client_id" class="form-label">Client Name</label>
                                    <input type="hidden" value="{{ $po->client_id}}" class="client_id" name="client_id" id="client_id" />
                                    <select class="form-select select2" required="required" id="client" name="client">
                                        <option value="">Select Client</option>                                                            
                                        <? foreach($client_list as $client) : ?>
                                            <option value="<?=$client->id?>" <?=($client->id == $po->client_id) ? 'selected' : ''?> ><?=$client->client_name?></option>
                                        <? endforeach;?>
                                    </select>
                                    <div class="invalid-feedback text-danger error-msg client_error">Client Name is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <input type="hidden" value="{{ $po->store_id }}" class="store_id" name="store_id" id="store_id" />
                                    <select class="form-select select2" required="required" id="store" name="store">
                                        <option value="">Select Store/Warehouse</option>                                                            
                                    </select>
                                    <div class="invalid-feedback text-danger error-msg store_error">Store/Warehouse Name is Required</div>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="inputState" class="form-label">Status</label>
                                    <div><span class="badge badge-soft-primary fs-16 text-uppercase {{ $po->status }}" id="po-status">{{ $po->status}}</span></div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="col-md-12 mt-5">
                        <h5>Order Item Details</h5>
                    </div> 
                    <div class="col-md-12 mb-2">
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="ri-barcode-line label-icon align-middle rounded-pill fs-16 me-2"></i>Scan Code</span>
                            <input type="text" class="form-control" name="item_code" id="item_code">
                            <button class="btn btn-warning" id="find-items" type="button"><i class="ri-book-read-line label-icon align-middle rounded-pill fs-16 me-2"></i> Find Item</button>
                        </div>
                    </div> 

                    <!-- ITEMS -->
                    <div class="table-responsive">
                        <table class="table table-borderless " id="product-list">
                            <thead>
                                <tr class="table-active">
                                    <th style="width: 20px;">#</th>
                                    <th class="text-start" style="width: 350px;">Particulars</th>
                                    <th style="width: 100px;">UOM</th>
                                    <th style="width: 80px;">Quantity</th>
                                    <th class="text-start" style="width: 110px;">Unit Price</th>
                                    <th style="width: 110px;">Discount</th>
                                    <th class="text-start" style="width: 110px;">Amount</th>
                                    <th class="text-center" style="width: 20px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="newlink">
                                <? 
                                    $total_discount = 0;
                                    $subtotal =0;
                                    $x=1;
                                    foreach($po->items as $item) :
                                    
                                        $total_discount += $item->discount ;
                                        $subtotal += $item->total_amount;
                                    ?>
                                    <tr id="product_{{$x}}" class="product fs-12">
                                        <td class="text-start">
                                        <?=$x?>
                                        </td>
                                        <td>
                                          
                                            {{ $item->product->product_name }}<br/><small class="fw-medium">{{ $item->product->product_code }}</small>
                                            <input type="hidden" class="form-control" name="product_code[]" readonly id="product_code_{{ $item->id }}" value="{{ $item->product->product_code }}"/>
                                            <input type="hidden" class="form-control" name="product_id[]" readonly id="product_id_{{ $item->id }}" value="{{ $item->product->product_id }}" />
                                            <input type="hidden" class="form-control" name="product_name[]" readonly id="product_name_{{ $item->id }}" value="{{ $item->product->product_name }}" placeholder="Product Desc" required />
                                           
                                        </td>
                                        <td class=" fs-12">
                                            <select class="form-select uom uom-select "name="uom[]" id="uom_{{ $item->id }}" required>
                                                <option value="">UOM</option>                                                            
                                                <? foreach($uom as $u) : ?>
                                                    <option value="<?=$u->uom_id?>" <?=($u->uom_id == $item->uom_id) ? 'selected' : ''?> ><?=$u->code?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg uom{{($x-1)}}_error"></span>
                                        </td>
                                        <td class=" fs-12">
                                            <input type="number" data-id="{{ $item->id }}" class="form-control text-end qty" name="qty[]" id="qty_{{ $item->id }}" value="{{ $item->requested_qty }}" placeholder="Qty" required />
                                            <span class="text-danger error-msg qty{{($x-1)}}_error"></span>
                                        </td>
                                        <td>
                                            <input type="number" data-id="{{ $item->id }}" class="form-control unit_price text-end" name="unit_price[]" id="unit_price_{{ $item->id }}" value="{{ $item->unit_amount }}" placeholder="Unit price" required />
                                            <span class="text-danger error-msg unit_price{{($x-1)}}_error"></span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control discount text-end" name="discount[]" id="discount_{{ $item->id }}" value="{{ number_format($item->discount,2) }}" placeholder="Discount" required />
                                        </td>
                                        <td class="text-start">
                                            <div>
                                                <input type="text" class="form-control total_amount text-end" name="amount[]" id="total_amount_{{ $item->id }}" value="{{ number_format($item->total_amount,2) }}" placeholder="0.00" readonly />
                                                <span class="text-danger error-msg amount{{($x-1)}}_error"></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="javascript:void(0)" class="text-danger remove-product" data-id="{{ $x }}"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>
                                        </td>
                                    </tr>
                                    <?$x++;?>
                                <? endforeach;?>
                            </tbody>
                            </table>
                            <table class="invoice-table table table-borderless table-nowrap mb-0 mt-4">
                            <tbody>
                                <tr class="border-top border-top-dashed mt-2">
                                    <td class="p-0">
                                        <table class="table table-borderless table-sm text-end">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Sub Total</th>
                                                    <td style="width:200px;">
                                                        <input type="text" class="form-control bg-light border-0 text-end" id="cart-subtotal" value="{{ number_format($subtotal,2) }}" placeholder="$0.00" readonly />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Discount</th>
                                                    <td>
                                                        <input type="text" class="form-control bg-light border-0 text-end" id="cart-discount"  value="{{ number_format(($total_discount * -1),2) }}" placeholder="$0.00" readonly />
                                                    </td>
                                                </tr>
                                                <tr class="border-top border-top-dashed">
                                                    <th scope="row">Total Amount</th>
                                                    <td>
                                                        <input type="text" class="form-control bg-light border-0 text-end" id="cart-total"  value="{{ number_format(($subtotal - $total_discount),2) }}" placeholder="$0.00" readonly />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!--end table-->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--end table-->
                    </div>
                
                </div>
            </div>
            </form>
        </div>
    </div> <!-- end col -->
</div> <!-- end col -->

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
<script src="{{ URL::asset('/assets/js/po/po.js') }}"></script>

@endsection