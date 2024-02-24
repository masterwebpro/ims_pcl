@extends('layouts.master')
@section('title') User List @endsection
@section('css')

<link rel="stylesheet" href="{{ URL::asset('/assets/libs/@tarekraafat/@tarekraafat.min.css') }} ">

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') User @endslot
@slot('title') User List  @endslot
@endcomponent


<?

?>

<div class="row">
    <div class="col-lg-12">
        <div class="card" id="tasksList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">&nbsp;</h5>
                    <div class="flex-shrink-0">
                        <a href="{{ URL::to('users/create') }}" class="btn btn-success btn-label rounded-pill"><i class="bx bx-user label-icon align-middle rounded-pill fs-16 me-2"></i> Create New</a>
                    </div>
                </div>
            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

        <div class="card">
            <div class="card-body">
                <div class="table-responsive table-card mb-4">
                    <table class="table align-middle table-nowrap mb-0" id="usersTable">
                        <thead class="table-light text-muted">
                            <tr>
                                <th class="sort" data-sort="username">Email Address</th>
                                <th class="sort" data-sort="first_name">First Name</th>
                                <th class="sort" data-sort="last_name">Last Name</th>
                                <th class="sort" data-sort="created_by">Created By</th>
                                <th class="sort" data-sort="action">Action</th>
                            </tr>
                        </thead>
    
                        <tbody class="list form-check-all">
                            <? if($user_list->total() > 0 ) : ?>
                                <? foreach($user_list as $user) :?>
                                    <tr>
                                        <td class="rcv_no">{{ $user->email}}</td>
                                        <td class="po_num">{{ $user->first_name}}</td>
                                        <td class="sales_invoice">{{ $user->last_name}}</td>
                                        <td>{{ $user->created_at}}</td>
                                        <td class="action">
                                            <div class="hstack gap-3 fs-12">
                                                <!-- <a href="{{ URL::to('users') }}/<?#=_encode($user->id)?>" data-id="{{$user->id}}" class="link-info text-info d-inline-block"><i class="ri-eye-fill align-bottom me-1"></i> View</a> -->
                                                <? if($user->status != 'posted') : ?>
                                                    <a href="{{ URL::to('users') }}/<?=_encode($user->id);?>/edit" data-id="{{$user->id}} " class="link-info edit-po"><i class="ri-pencil-fill align-bottom me-1"></i> Edit </a> </div>
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
                {!! $user_list->withQueryString()->links('pagination::bootstrap-5') !!}
            </div><!-- end card-body -->
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->

	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
 
<!-- <script src="{{ URL::asset('/assets/js/stock/movement.js') }}"></script> -->

@endsection