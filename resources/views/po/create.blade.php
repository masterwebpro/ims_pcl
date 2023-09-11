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
    <div class="col-xxl-11">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">PO Creation <span class="badge badge-soft-primary fs-16 text-uppercase open" id="po-status">New</span></h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-po"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <button type="button" data-status="posted" class="btn btn-info btn-label rounded-pill submit-po"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
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
                                    <input type="hidden" class="form-control" name="po_id" id="po_id" value="">
                                    <input type="text" class="form-control" name="po_num" id="po_num" value="" placeholder="Enter Po Number">
                                    <span class="text-danger error-msg po_num_error"></span>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="inputZip" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                    <select class="form-select select2" required="required" id="supplier" name="supplier">
                                        <option value="">Select Supplier</option>
                                        <? foreach($supplier_list as $supplier) : ?>
                                            <option value="<?=$supplier->id?>" ><?=$supplier->supplier_name?></option>
                                        <? endforeach;?>
                                    </select>
                                    <span class="text-danger error-msg supplier_error"></span>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="inputState" class="form-label">PO Date <span class="text-danger">*</span></label>
                                    <input type="date" id="po_date" required="required"  name="po_date" class="form-control" value="<?=isset($po->po_date) ? $po->po_date : ''?>">
                                    <span class="text-danger error-msg po_date_error"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label for="client_id" class="form-label">Customer Name</label>
                                    <input type="hidden" value="" class="customer_id" name="customer_id" id="customer_id" />
                                    <select class="form-select select2" required="required" id="customer" name="customer">
                                        <option value="">Select Customer</option>
                                        <? foreach($client_list as $customer) : ?>
                                            <? if($customer->client_type == 'C') : ?>
                                                <option value="<?=$customer->id?>" ><?=$customer->client_name?></option>
                                            <? endif;?>
                                        <? endforeach;?>
                                    </select>
                                    <span class="text-danger error-msg customer_error"></span>
                                </div>

                                <div class="col-md-4 form-group">
                                    <label for="client_id" class="form-label">Company Name</label>
                                    <input type="hidden" value="" class="company_id" name="company_id" id="company_id" />
                                    <select class="form-select select2" required="required" id="company" name="company">
                                        <option value="">Select Company</option>
                                        <? foreach($client_list as $company) : ?>
                                            <? if($company->client_type == 'O') : ?>
                                                <option value="<?=$company->id?>" ><?=$company->client_name?></option>
                                            <? endif;?>
                                        <? endforeach;?>
                                    </select>
                                    <span class="text-danger error-msg company_error"></span>
                                </div>

                                <div class="col-md-4 form-group store_cont">
                                    <label for="store_id" class="form-label">Site Name</label>
                                    <input type="hidden" value="" class="store_id" name="store_id" id="store_id" />
                                    <select class="form-select select2" required="required" id="store" name="store">
                                        <option value="">Select Site</option>
                                        <? foreach($store_list as $store) : ?>
                                            <option value="<?=$store->id?>" ><?=$store->store_name?></option>
                                        <? endforeach;?>
                                    </select>
                                    <span class="text-danger error-msg store_error"></span>
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
                        <table class="table table-borderless table-nowrap" id="product-list">
                            <thead>
                                <tr class="table-active">
                                    <th scope="col" style="width: 20px;">#</th>
                                    <th scope="col" >Product</th>
                                    <th scope="col" >UOM</th>
                                    <th scope="col" class="text-center" >Quantity</th>
                                    <th scope="col" class="text-start d-none">Unit Price</th>
                                    <th scope="col" class="text-start d-none">Discount</th>
                                    <th scope="col" class="text-start d-none">Amount</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="newlink">
                                <?
                                    $total_discount = 0;
                                    $subtotal =0;
                                    $total_qty =0;
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="fw-medium"> Total </td>
                                    <td class="fw-medium"><input type="text" class="form-control border-0 text-end" id="total_qty" value="{{ number_format($total_qty,2) }}" placeholder="0.00" readonly /></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            </table>
                            <table class="invoice-table table table-borderless table-nowrap mb-0 mt-4">
                            <tbody>
                                <tr class="border-top border-top-dashed mt-2">
                                    <td class="p-0">
                                        <table class="table table-borderless table-sm text-end d-none">
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
