@extends('layouts.master')
@section('title') Dispatch @endsection
@section('css')

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Outbound @endslot
@slot('title') Dispatch @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Dispatch List</h5>
                    <div class="flex-shrink-0">
                        <button data-status="open" class="create-dispatch btn btn-success btn-label rounded-pill"><i class="ri-file-line label-icon align-middle rounded-pill fs-16 me-2"></i> Create Dispatch</button>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{ route('dispatch.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-xxl-4 col-sm-12">
                            <div class="search-box">
                                <input type="text" name="q" class="form-control search"
                                    placeholder="Search for tasks or something...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <!--end col-->

                        <div class="col-xxl-2 col-sm-4">
                            <div class="input-light">
                                <select class="form-control" name="filter_date" id="filter_date">
                                    <option value="dispatch_date">Dispatch Date</option>
                                    <option value="created_at">Created Date</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xxl-2 col-sm-4">
                            <input type="text" class="form-control" name="date" id="date_picker"
                                data-provider="flatpickr" data-date-format="Y-d-m" data-range-date="true"
                                placeholder="Select date range">
                        </div>
                        <!--end col-->

                        <div class="col-xxl-3 col-sm-4">
                            <div class="input-light">
                                <select class="form-control" data-choices data-choices-search-false
                                    name="status" id="status">
                                    <option value="">Status</option>
                                    <option value="all" selected>All</option>
                                    <option value="open">Open</option>
                                    <option value="posted">Posted</option>
                                </select>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-xxl-1 col-sm-4">
                            <button type="submit" class="btn btn-primary w-100"> <i
                                    class="ri-equalizer-fill me-1 align-bottom"></i>
                                Filters
                            </button>
                        </div>
                        <!--end col-->
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
                                <th class="sort" data-sort="dispatch_no">Dispatch #</th>
                                <th class="sort" data-sort="dispatch_date">Dispatcher</th>
                                <th class="sort" data-sort="dispatch_date">Dispatch Date</th>
                                <th class="sort" data-sort="dispatch_no">Trucker Name </th>
                                <th class="sort" data-sort="dispatch_no">Truck Type</th>
                                <th class="sort" data-sort="dispatch_no">Plate No.</th>
                                <th class="sort" data-sort="dispatch_no">Seal No.</th>
                                <th class="sort" data-sort="dispatch_no">Driver Name</th>
                                <th class="sort" data-sort="dispatch_date">Start Picking</th>
                                <th class="sort" data-sort="dispatch_date">Finish Picking</th>
                                <th class="sort" data-sort="dispatch_date">Start Loading</th>
                                <th class="sort" data-sort="dispatch_date">Finish Loading</th>
                                <th class="sort" data-sort="dispatch_date">Arrival Date</th>
                                <th class="sort" data-sort="dispatch_date">Depart Date</th>
                                <th class="sort" data-sort="wd_count">Created By</th>
                                <th class="sort" data-sort="wd_count">Created At</th>
                                <th class="sort" data-sort="status">Status</th>
                                <th class="sort" data-sort="action">Action</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            <? if($dispatch_list->total() > 0 ) : ?>
                                <? foreach($dispatch_list as $dispatch) :?>
                                    <tr>
                                        <td class="dispatch_no"><a href="{{ URL::to('dispatch') }}/<?=_encode($dispatch->id)?>" data-id="{{$dispatch->id}}" class="link-info text-info d-inline-block">{{ $dispatch->dispatch_no}} </a></td>
                                        <td class="">{{ $dispatch->dispatch_by }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y',strtotime($dispatch->dispatch_date)) }}</td>
                                        <td class="">{{ $dispatch->trucker_name }}</td>
                                        <td class="">{{ $dispatch->truck_type }}</td>
                                        <td class="">{{ $dispatch->plate_no }}</td>
                                        <td class="">{{ $dispatch->seal_no }}</td>
                                        <td class="">{{ $dispatch->driver }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y H:i:s',strtotime($dispatch->start_picking_datetime)) }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y H:i:s',strtotime($dispatch->finish_picking_datetime)) }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y H:i:s',strtotime($dispatch->start_datetime)) }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y H:i:s',strtotime($dispatch->finish_datetime)) }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y H:i:s',strtotime($dispatch->arrival_datetime)) }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y H:i:s',strtotime($dispatch->depart_datetime)) }}</td>
                                        <td class="">{{ $dispatch->name }}</td>
                                        <td class="dipatch_date">{{ date('M d, Y H:i:s',strtotime($dispatch->created_at)) }}</td>
                                        <td class="status"><span class="badge {{ $dispatch->status }} text-uppercase fs-11">{{ $dispatch->status }}</span></td>
                                        <td class="action">
                                            <div class="hstack gap-3 fs-12">
                                                <a href="{{ URL::to('dispatch') }}/<?=_encode($dispatch->id)?>" data-id="{{$dispatch->id}}" class="link-info text-info d-inline-block"><i class="ri-eye-fill align-bottom me-1"></i> View</a>
                                                <? if($dispatch->status != 'posted') : ?>
                                                    <a href="{{ URL::to('dispatch') }}/<?=_encode($dispatch->id);?>/edit" data-id="{{$dispatch->id}} " class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> Edit </a> </div>
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
                {!! $dispatch_list->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->
    </div>
    <!--end col-->
</div>
<!--end row-->

<div class="modal fade" id="show-po" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="exampleModalLabel">Search PO Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-12 form-group">
                        <input type="text" class="form-control" dir="ltr" spellcheck=false autocomplete="off" autocapitalize="off" name="po_num_holder" id="po_num_holder" value="" placeholder="Enter PO Number">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="hstack gap-2 justify-content-end">
                    <button type="button" class="btn btn-success" id="do-po-btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>



	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
 <!-- autocomplete js -->
 <script src="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/dispatch/dispatch.js') }}"></script>



@endsection
