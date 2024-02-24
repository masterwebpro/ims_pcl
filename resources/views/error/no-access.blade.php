@extends('layouts.master')
@section('title') @lang('translation.dashboards') @endsection
@section('css')
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') NO ACCESS FOUND @endslot
@slot('title') NO ACCESS FOUND @endslot
@endcomponent

<div class="row justify-content-center">
    <div class="col-xl-12">
    <div class="auth-page-content overflow-hidden p-0">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-12 text-center">
                    <div class="error-500 position-relative">
                        <img src="{{ URL::asset('assets/images/error500.png') }}" alt="" class="img-fluid error-500-img error-img" />
                        <h1 class="title text-muted">500</h1>
                    </div>
                    <div>
                        <h5 class="text-danger">You don't have access on this module</h5>
                        <a href="#" onclick="history.back()" class="btn btn-primary"><i class="mdi mdi-home me-1"></i>Back</a>
                    </div>
                </div><!-- end col-->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
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