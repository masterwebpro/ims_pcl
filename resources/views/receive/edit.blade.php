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
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1"></h5>
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <a href="{{ URL::to('receive') }}" data-status="open" class="btn btn-success btn-label rounded-pill"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</a>
                        <a href="{{ URL::to('receive') }}" data-status="posted" class="btn btn-info btn-label rounded-pill"><i class="ri-lock-line label-icon align-middle rounded-pill fs-16 me-2"></i> Post</a>
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

<div class="row justify-content-center">
    <div class="col-xxl-11">
        <div class="card">
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card-body p-4 ">
                        <div class="row g-3">
                            <div class="col-4">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Supplier Name</h6>
                                <p class="fw-medium mb-2" id="shipping-name">
                                    <select class="form-select select2" required="required" id="supplier" name="supplier">
                                        <option value="">Select Supplier</option>                                                            
                                        <? foreach($supplier_list as $supplier) : ?>
                                            <option value="<?=$supplier->id?>" ><?=$supplier->supplier_name?></option>
                                        <? endforeach;?>
                                    </select> 
                                </p>
                                <!-- <p class="text-muted mb-1" id="shipping-address-line-1">supp_add  here</p>
                                <p class="text-muted mb-1">supp_add2 here</p>
                                <p class="text-muted mb-0">supp_city province, country here</p> -->
                            </div>

                            <div class="col-4">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Client Name</h6>
                                <p class="fw-medium mb-2" id="billing-name">
                                    <select class="form-select select2" required="required" id="client" name="client">
                                        <option value="">Select Client</option>                                                            
                                        <? foreach($client_list as $client) : ?>
                                            <option value="<?=$client->id?>" ><?=$client->client_name?></option>
                                        <? endforeach;?>
                                    </select>
                                </p>
                                <!-- <p class="text-muted mb-1" id="shipping-address-line-1">client_add  here</p>
                                <p class="text-muted mb-1">client_add2 here</p>
                                <p class="text-muted mb-0">client_city province, country here</p> -->
                            </div>
                            <!--end col-->
                            <div class="col-4">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Warehouse / Store Address</h6>
                                <p class="fw-medium mb-2" id="shipping-name">
                                    <select class="form-select select2" required="required" id="store" name="store">
                                        <option value="">Select Store/Warehouse</option>                                                            
                                    </select>
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
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">PO Number</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" value="" placeholder="PO Number">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Sales Invoice</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" value="" placeholder="Sales Invoice">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row ms-3 mt-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Received By</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" value="" placeholder="Received By">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Date Received</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" value="" placeholder="Date Received">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Inspect By</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" value="" placeholder="Inspect By">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Inspect Date</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" value="" placeholder="Inspect Date">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="date_arrived" class="col-lg-4 col-form-label">Date Arrived</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="date_arrived" id="date_arrived" placeholder="DD-MM-YYYY">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="time_arrived" class="col-lg-4  col-form-label">Time Arrived</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="time_arrived" id="time_arrived" placeholder="hh:mm" id="cleave-time">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Date Departed</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="date_departed" id="date_departed" placeholder="DD-MM-YYYY">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="time_departed" class="col-lg-4  col-form-label">Time Departed</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="time_departed" id="time_departed" placeholder="hh:mm" id="cleave-time">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Plate Number</label> 
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" value="" placeholder="Plate Number">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4  col-form-label">Truck Type</label>
                                <div class="col-lg-8">
                                    <select class="form-select select2" required="required" id="truck_type" name="truck_type">
                                        <option value="">Select Truck Type</option>                                                            
                                        <? foreach($truck_type_list as $truck) : ?>
                                            <option value="<?=$truck->id?>" ><?=$truck->vehicle_desc?></option>
                                        <? endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="row ms-3 mx-3">
                        <div class="col-lg-6">
                            <div class="row">
                                <label for="colFormLabel" class="col-lg-4 col-form-label">Warehouse</label> 
                                <div class="col-lg-8">
                                    <select class="form-select select2" required="required" id="warehouse" name="warehouse">
                                        <option value="">Select warehouse</option>                                                            
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
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
                                            <th scope="col" >Inv Qty</th>
                                            <th scope="col" >WHSE Qty</th>
                                            <th scope="col" >CBM</th>
                                            <th scope="col" >Lot/Batch #</th>
                                            <th scope="col" >Expiry Date</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="newlink">
                                        <? 
                                            $total_discount = 0;
                                            $subtotal =0;
                                            
                                        ?>

                                        <tr>
                                            <td>1</td>
                                            <td class="fw-semibold">Ariete Donuts & Cookies<br/><small>A002</small></td>
                                            <td>10 <br/><small>Case of 10</small></td>
                                            <td>100 <br/><small>Piece</small></td>
                                            <td>0.550</td>
                                            <td>L: 1223223<br/>B: BAS07262023</td>
                                            <td>2023-12-30</td>
                                            <td class="text-center">
                                            <a href="javascript:void(0)" class="text-info"><i class="ri-edit-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>
                                            <a href="javascript:void(0)" class="text-danger"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>
                                            </td>
                                        </tr>

                                       
                                    </tbody>
                                    </table>
                                    
                                <!--end table-->
                            </div>





                            <div class="table-responsive d-none">
                                <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">Code</th>
                                            <th scope="col">SKU</th>
                                            <th scope="col" class="text-start">Description</th>
                                            <th scope="col">UOM</th>
                                            <th scope="col">Rqstd Qty</th>
                                            <th scope="col">Actual Qty</th>
                                            <th scope="col" class="text-end">Unit Price</th>
                                            <th scope="col" class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-list">
                                    <? 
                                        $x=1;
                                        $total_amount = 0;
                                        $total_discount = 0;
                                        ?>
                                        <tr>
                                            <th scope="row"><?=$x++;?></th>
                                            <td></td>
                                            <td></td>
                                            <td class="text-start">
                                                <span class="fw-medium"></span>
                                                <p class="text-muted mb-0"></p>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-end"></td>
                                            <td class="text-end"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!--end table-->
                            </div>
                            <div class="border-top border-top-dashed mt-2">
                                <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                    <tbody>
                                        <tr class="border-top border-top-dashed fs-15">
                                            <th scope="row">Total Amount</th>
                                            <th class="text-end">{{ number_format(($total_amount - $total_discount) ,2) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
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