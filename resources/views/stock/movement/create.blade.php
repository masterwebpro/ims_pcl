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
@slot('li_1') Inbound @endslot
@slot('title') Stock Movement @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-xxl-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"></h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
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
                                <input type="hidden" name="ref_no" id="ref_no" value="" />
                                    <h6 class="text-muted mb-3">Company Name <span class="text-danger">*</span></h6>
                                    <p class="mb-2">
                                        <input type="hidden" name="company_id" id="company_id" value="<?=$company_id?>" />
                                        <input type="hidden" name="company" id="company" value="<?=$company_id?>" />
                                        <select class="form-select select" disabled>
                                            <option value="">Select Company</option>                                                            
                                            <? foreach($client_list as $company) : ?>
                                                <option value="<?=$company->id?>" <?=($company->id == $company_id) ? 'selected': ''; ?> ><?=$company->client_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg company_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-4">
                                    <h6 class="text-muted mb-3">Site Name <span class="text-danger">*</span></h6>
                                    <p class="mb-2">
                                        <input type="hidden" name="store_id" id="store_id" value="<?=$store_id?>" />
                                        <input type="hidden" name="store" value="<?=$store_id?>" />
                                        <select class="form-select select" disabled id="store">
                                            <option value="">Select Store/Warehouse</option>                                                            
                                        </select>
                                        <span class="text-danger error-msg store_error"></span>
                                    </p>
                                </div>
                                <!--end col-->
                                <div class="col-4">
                                    <h6 class="text-muted mb-3">Warehouse <span class="text-danger">*</span></h6>
                                    <p class="mb-2">
                                        <input type="hidden" name="warehouse_id" id='warehouse_id' value="<?=$warehouse_id?>" />
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
                                    <div class="col-md-12 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><i class="ri-barcode-line label-icon align-middle rounded-pill fs-16 me-2"></i>Scan Code</span>
                                            <input type="text" class="form-control" name="item_code" id="item_code">
                                            <button class="btn btn-warning" id="find-items" type="button"><i class="ri-book-read-line label-icon align-middle rounded-pill fs-16 me-2"></i> Find Item</button>
                                        </div>
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
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Item List</h5>
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
                            <h6 class="text-muted text-uppercase fw-semibold">Location </h6>
                            <p class="fw-medium" id="billing-name">
                                <select class="form-select select3" id="location" name="location">
                                    <option value="">Select Rack</option>  
                                    <? foreach($locations as $location) : ?>
                                         <option value="<?=$location->storage_location_id?>"><?=$location->location?></option>  
                                    <? endforeach;?>
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
                                <th>Location</th>
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


	@endsection
@section('script')


<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/stock/movement.js') }}"></script>

@endsection