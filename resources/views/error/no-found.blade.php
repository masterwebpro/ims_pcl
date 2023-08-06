@extends('layouts.master')
@section('title') @lang('translation.dashboards') @endsection
@section('css')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') NO RESULT FOUND @endslot
@slot('title') NO RESULT FOUND @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-xl-5">
        <div class="card overflow-hidden">
            <div class="card-body p-4">
                <div class="noresult">
                    <div class="text-center">
                        <lord-icon src="{{URL::to('/assets/json/widgets')}}/search.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon>
                        <h5 class="mt-2">Sorry! No Result Found</h5>
                        <p class="text-muted">We've searched more than 150+ result We did not find any for you search.</p>
                        <button onclick="history.back()" class="btn btn-danger"><i class="mdi mdi-reply-all me-1"></i>Back to previous page</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>

	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/app.min.js') }}"></script>

@endsection