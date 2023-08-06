@extends('layouts.master')
@section('title') Attributes @endsection
@section('css')

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboards @endslot
@slot('title') Attributes @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">All Attributes List</h5>
                    <div class="flex-shrink-0">
                        <a href="{{ URL::to('maintenance/attributes/create') }}"class="btn btn-danger add-po" ><i class="ri-add-line align-bottom me-1"></i> Create Attributes</a>
                    </div>
                </div>
            </div>
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{ route('attributes.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-xxl-4 col-sm-12">
                            <div class="search-box">
                                <input type="text" name="q" class="form-control search"
                                    placeholder="Search for tasks or something...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xxl-2 col-sm-4">
                            <div class="input-light">
                                <select class="form-control" name="filter_date" id="filter_date">
                                    <option value="created_at">Created Date</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-xxl-2 col-sm-4">
                            <input type="text" class="form-control" name="date" id="date_picker"
                                data-provider="flatpickr" data-date-format="Y-d-m" data-range-date="true"
                                placeholder="Select date range">
                        </div>

                        <div class="col-xxl-3 col-sm-4">
                            <div class="input-light">
                                <select class="form-control" data-choices data-choices-search-false
                                    name="status" id="status">
                                    <option value="all" selected>All</option>
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
                                <th class="sort" data-sort="attributes_code">Attributes Code</th>
                                <th class="sort" data-sort="attribute_name">Attribute Name</th>
                                <th class="sort" data-sort="attributes_name">Required</th>
                                <th class="sort" data-sort="brand_name">Enabled</th>
                                <th class="sort" data-sort="created_at">Created Date</th>
                                <th class="sort" data-sort="updated_at">Updated Date</th>
                                <th class="sort" data-sort="created_by">Updated By</th>
                                <th class="sort" data-sort="action">Action</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            <? if($attributes_list->total() > 0 ) : ?>
                                <? foreach($attributes_list as $attr) :?>
                                    <tr>
                                        <td>{{ $attr->attribute_code}}</td>
                                        <td>{{ $attr->attribute_name}}</td>
                                        <td>{{( $attr->is_required == 1) ? '<i class="ri-check-line text-success"></i>' : '<i class="ri-close-line text-danger"></i>'}}</td>
                                        <td>
                                            <div class="form-check form-switch form-switch-custom form-switch-primary">
                                                <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck9" name="is_enabled"  {{ (($attr->is_enabled == 1) ? "checked" : "") }} disabled>
                                            </div>
                                        </td>
                                        <td>{{ date('M d, Y H:i:s',strtotime($attr->created_at)) }}</td>
                                        <td>{{ $attr->updated_by }}</td>
                                        <td>{{ date('M d, Y H:i:s',strtotime($attr->updated_at)) }}</td>
                                        <td class="action">
                                            <div class="hstack gap-3 fs-12">
                                                <a href="{{ URL::to('maintenance/attributes') }}/<?=_encode($attr->attributes_id)?>" data-id="{{$attr->attributes_id}}" class="link-info text-info d-inline-block"><i class="ri-eye-fill align-bottom me-1"></i> View</a>
                                                <a href="{{ URL::to('maintenance/attributes') }}/<?=_encode($attr->attributes_id);?>/edit" data-id="{{$attr->attributes_id}} " class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> Edit </a> </div>
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
                {!! $attributes_list->withQueryString()->links('pagination::bootstrap-5') !!}
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
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/form-masks.init.js') }}"></script>

@endsection
