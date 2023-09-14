@extends('layouts.master')
@section('title') Receive Order @endsection
@section('css')

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">
<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />


@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Inbound @endslot
@slot('title') Receive Order  @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Receiving List</h5>
                    <div class="flex-shrink-0">
                        <button data-status="open" class="create-receiving btn btn-success btn-label rounded-pill"><i class="ri-file-line label-icon align-middle rounded-pill fs-16 me-2"></i> Create Receiving</button>
                        <button data-status="posted" class="receive-po  btn btn-secondary btn-label rounded-pill"><i class="ri-download-line label-icon align-middle rounded-pill fs-16 me-2"></i> Receive from PO</button>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{ route('receive.index')  }}" method="GET">
                    <div class="row g-3">
                        <div class="col-lg-3 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="q" class="form-control search"
                                    placeholder="RCV or PO Number" value="{{isset($request->q) ? $request->q : ''}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <!--end col-->

                        <div class="col-lg-3 col-sm-6">
                            <div class="input-light">
                                <select class="form-select" name="filter_date" id="filter_date">
                                    <option value="">Filter Date By</option>
                                    <option <?=($request->filter_date == 'po_date') ? 'selected': ''?> value="po_date">PO Date</option>
                                    <option <?=($request->filter_date == 'created_at') ? 'selected': ''?> value="created_at">Created Date</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <input type="text" class="form-control" name="date" id="date_picker"
                                data-provider="flatpickr" data-date-format="Y-d-m" value="{{isset($request->date) ? $request->date : ''}}" data-range-date="true"
                                placeholder="Select date range">
                        </div>
                        <!--end col-->

                        <div class="col-lg-3 col-sm-3">
                            <div class="input-light">
                                <select class="form-control" data-choices data-choices-search-false
                                    name="status" id="status">
                                    <option value="">Status</option>
                                    <option <?=($request->status == 'all') ? 'selected': ''?> value="all" selected>All Status</option>
                                    <option <?=($request->status == 'open') ? 'selected': ''?> value="open">Open</option>
                                    <option <?=($request->status == 'closed') ? 'selected': ''?> value="closed">Closed</option>
                                    <option <?=($request->status == 'posted') ? 'selected': ''?> value="posted">Posted</option>
                                </select>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->

                    <div class="row g-3 mt-1">
                        <div class="col-lg-3 col-sm-6">
                            <select class="form-select select2" id="supplier" name="supplier">
                                <option value="">Select Supplier</option>
                                <? foreach($supplier_list as $supplier) : ?>
                                    <option value="<?=$supplier->id?>" <?=($request->supplier == $supplier->id) ? 'selected': ''?>  ><?=$supplier->supplier_name?></option>
                                <? endforeach;?>
                            </select>
                        </div>
                        <!--end col-->

                        <div class="col-lg-3 col-sm-6">
                            <select class="form-select select2" id="customer" name="customer">
                                <option value="">Select Customer</option>
                                <? foreach($client_list as $customer) : ?>
                                    <? if($customer->client_type == 'C') : ?>
                                        <option value="<?=$customer->id?>" <?=($request->customer == $customer->id) ? 'selected': ''?> ><?=$customer->client_name?></option>
                                    <? endif;?>
                                <? endforeach;?>
                            </select>
                        </div>
                        <!--end col-->

                        <div class="col-lg-3 col-sm-6">
                            <select class="form-select select2" id="company" name="company">
                                <option value="">Select Company</option>
                                <? foreach($client_list as $company) : ?>
                                    <? if($company->client_type == 'O') : ?>
                                        <option value="<?=$company->id?>"  <?=($request->company == $company->id) ? 'selected': ''?> ><?=$company->client_name?></option>
                                    <? endif;?>
                                <? endforeach;?>
                            </select>
                        </div>
                        <!--end col-->

                        <div class="col-lg-3 col-sm-3">
                            <div class="">
                                <div class="">
                                    <button type="submit" class="submit-receive-search btn btn-warning btn-label rounded-pill"><i class="ri-search-line label-icon align-middle rounded-pill fs-16 me-2"></i> Search</button>
                                    <a href="#" class="submit-receive-xls btn btn-secondary btn-label rounded-pill d-none"><i class="ri-file-excel-line label-icon align-middle rounded-pill fs-16 me-2"></i>Excel</a>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                    </div>
                </form>
            </div>
            <!--end card-body-->
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="sort" data-sort="id">RCV #</th>
                                <th class="sort" data-sort="id">PO #</th>
                                <th class="sort" data-sort="id">Invoice #</th>
                                <th class="sort" data-sort="supplier_name">Supplier Name</th>
                                <th class="sort" data-sort="customer_name">Customer Name</th>
                                <th class="sort" data-sort="company_name">Company Name</th>
                                <th class="sort" data-sort="store">Site</th>
                                <th class="sort" data-sort="status">Status</th>
                                <th class="sort" data-sort="action">Action</th>
                            </tr>
                        </thead>
       
                        <tbody class="list form-check-all">
                            <? if($receive_list->total() > 0 ) : ?>
                                <? foreach($receive_list as $receive) :?>
                                    <tr>
                                        <td class="rcv_no">{{ $receive->rcv_no}}</td>
                                        <td class="po_num">{{ $receive->po_num}}</td>
                                        <td class="sales_invoice">{{ $receive->sales_invoice}}</td>
                                        <td>{{ $receive->supplier_name}}</td>
                                        <td class="customer_name">{{ $receive->customer->client_name}}</td>
                                        <td class="company_name">{{ $receive->company->client_name}}</td>
                                        <td class="store">{{ $receive->store_name}}</td>
                                        <td class="status"><span class="badge {{ $receive->status }} text-uppercase fs-11">{{ $receive->status }}</span></td>
                                        <td class="action">
                                            <div class="hstack gap-3 fs-12">
                                                <a href="{{ URL::to('receive') }}/<?=_encode($receive->id)?>" data-id="{{$receive->id}}" class="link-info text-info d-inline-block"><i class="ri-eye-fill align-bottom me-1"></i> View</a>
                                                <? if($receive->status != 'posted') : ?>
                                                    <a href="{{ URL::to('receive') }}/<?=_encode($receive->id);?>/edit" data-id="{{$receive->id}} " class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> Edit </a> </div>
                                                <? endif; ?>
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                            <? else :?>
                                <div class="noresult" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sorry! No Result Found</h5>
                                        <p class="text-muted mb-0">We've searched more than 200k+ tasks We did not find any tasks
                                            for you search.</p>
                                    </div>
                                </div>
                            <? endif; ?>
                        </tbody>
                    </table>
                    <!--end table-->
                </div>
                <!-- Pagination -->
                {!! $receive_list->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="modal fade" id="show-po" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">List of PO Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
           
            <div class="modal-body">
                <table class="table align-middle table-nowrap" id="po-table" width="100%">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="sort" data-sort="po_date">PO Date</th>    
                            <th class="sort" data-sort="po_num">PO Number</th>
                            <th class="sort" data-sort="supplier_name">Supplier Name</th>
                            <th class="sort" data-sort="customer_name">Customer Name</th>
                            <th class="sort" data-sort="created_by">Created By</th>
                        </tr>
                    </thead>

                    <tbody class="list form-check-all">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal"><i class="ri-close-line label-icon align-middle rounded-pill fs-16 me-2"></i>Close</button>
                    <button type="button"  id="receive-po-btn" class=" btn btn-warning btn-label rounded-pill"><i class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Select</button>
                </div>
            </div>
        </div>
    </div>
</div>



	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/moment/moment.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
 <!-- autocomplete js -->
 <script src="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/datatables/dataTables.responsive.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/receive/receive.js') }}"></script>



@endsection