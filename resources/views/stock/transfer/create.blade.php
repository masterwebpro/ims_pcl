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

<div class="row justify-content-center">
    <div class="col-xxl-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"></h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button data-status="open" class="submit-open btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <button data-status="posted" class="submit-posted  btn btn-info btn-label rounded-pill"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</button>
                        <a href="{{ URL::to('stock/transfer') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
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
                                            <input type="hidden" class="form-control" name="ref_no" id="ref_no">
                                            <label for="firstNameinput" class="form-label">Source Company <span class="text-danger">*</span></label>
                                            <select class="form-select select2 source_company" id="source_company" name="source_company">
                                                <option value="">Select Source Company</option>                                                            
                                                <? foreach($client_list as $company) : ?>
                                                    <option value="<?=$company->id?>" ><?=$company->client_name?></option>
                                                <? endforeach;?>
                                            </select>
                                            <span class="text-danger error-msg source_company_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="transaction_date" class="form-label">Source Site Name <span class="text-danger">*</span></label>
                                            <select class="form-select select2 source_site" name="source_site" id="source_site">
                                                <option value="">Select Source Site</option>                                                            
                                            </select>
                                            <span class="text-danger error-msg source_site_error"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" placeholder="Transaction Date" name="transaction_date" id="transaction_date">
                                            <span class="text-danger error-msg transaction_date_error"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="firstNameinput" class="form-label">DR Num</label>
                                            <input type="text" class="form-control" placeholder="DR num" name="dr_no" id="dr_no">
                                        </div>
                                    </div>
                                  
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="requested_by" class="form-label">Requested By <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Requested By" name="requested_by" id="requested_by">
                                            <span class="text-danger error-msg requested_by_error"></span>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <input type="text" class="form-control" name="remarks" placeholder="Remarks" id="remarks">
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
                                            <th scope="col" class="text-center" rowspan="2">#</th>
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


	@endsection
@section('script')


<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/stock/transfer.js') }}"></script>

@endsection