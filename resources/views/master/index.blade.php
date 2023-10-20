@extends('layouts.master')
@section('title') Master Data @endsection
@section('css')

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Master File @endslot
@slot('title') Master Data @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Master List</h5>
                    {{--  <div class="flex-shrink-0">
                        <form name="submit-master" id="submit-master">
                        <button data-status="posted" class="generate-data btn btn-success btn-label rounded-pill"><i class="ri-file-line label-icon align-middle rounded-pill fs-16 me-2"></i> Generate</button>
                        </form>
                    </div>  --}}
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{ route('withdraw.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-lg-3 col-sm-6">
                                <div class="search-box">
                                    <input type="text" name="q" class="form-control search"
                                        placeholder="Product Code / Name, Lot No, Location" value="{{isset($request->q) ? $request->q : ''}}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-lg-3 col-sm-6">
                                <div class="input-light">
                                    <select class="form-select" name="filter_date" id="filter_date">
                                        <option value="">Filter Date By</option>
                                        <option <?=($request->filter_date == 'received_date') ? 'selected': ''?> value="withdraw_date">Received Date</option>
                                        <option <?=($request->filter_date == 'created_at') ? 'selected': ''?> value="created_at">Created Date</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 col-sm-6">
                                <input type="text" class="form-control" name="date" id="date_picker"
                                    data-provider="flatpickr" data-date-format="Y-m-d" value="{{isset($request->date) ? $request->date : ''}}" data-range-date="true"
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
                                        <option <?=($request->status == 'posted') ? 'selected': ''?> value="posted">Posted</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->

                        <div class="row g-3 mt-1">
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
                    <!--end row-->
                </form>
            </div>
            <!--end card-body-->
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0" id="tasksTable">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="sort" data-sort="store">Site Name</th>
                                <th class="sort" data-sort="store">Warehouse</th>
                                <th class="sort" data-sort="store">Product</th>
                                <th class="sort" data-sort="store">Item Type</th>
                                <th class="sort" data-sort="store">Inv Qty</th>
                                <th class="sort" data-sort="store">Unit</th>
                                <th class="sort" data-sort="store">Whse Qty</th>
                                <th class="sort" data-sort="store">Unit</th>
                                <th class="sort" data-sort="store">Reserve Qty</th>
                                <th class="sort" data-sort="store">Location</th>
                                <th class="sort" data-sort="store">Lot No</th>
                                <th class="sort" data-sort="store">Expiry Date</th>
                                <th class="sort" data-sort="status">Mfg. Date</th>
                                <th class="sort" data-sort="withdraw_date">Received Date</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            <? if($master_list->total() > 0 ) : ?>
                                <? foreach($master_list as $master) :?>
                                    <tr>
                                        <td class="store">{{ $master->store_name}}</td>
                                        <td class="store">{{ $master->warehouse_name}}</td>
                                        <td class="text-wrap"><b>{{ $master->product_code }}</b> <br/> <em> {{ $master->product_name}} <em/></td>
                                        <td class="store">{{ $master->item_type}}</td>
                                        <td class="store">{{ $master->inv_qty}}</td>
                                        <td class="store">{{ $master->ui_code}}</td>
                                        <td class="store">{{ $master->whse_qty}}</td>
                                        <td class="store">{{ $master->uw_code}}</td>
                                        <td class="store">{{ $master->reserve_qty}}</td>
                                        <td class="store">{{ $master->location}}</td>
                                        <td class="store">{{ $master->lot_no}}</td>
                                        <td class="store">{{ $master->expiry_date}}</td>
                                        <td class="store">{{ $master->manufacture_date}}</td>
                                        <td class="order_date">{{ date('M d, Y',strtotime($master->received_date)) }}</td>
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
                {!! $master_list->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->
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

<script src="{{ URL::asset('/assets/js/master/master.js') }}"></script>



@endsection
