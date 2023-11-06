@extends('layouts.master')
@section('title') @lang('translation.dashboards') @endsection
@section('css')
<link href="{{ URL::asset('assets/libs/jsvectormap/jsvectormap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/swiper/swiper.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Dashboards @endslot
@slot('title') Dashboard @endslot
@endcomponent

<div class="row">
    <div class="col">
        <div class="h-100">
            <div class="row mb-3 pb-1">
                <div class="col-12">
                    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                        <div class="flex-grow-1">
                            <h4 class="fs-16 mb-1">Good Morning, {{Auth::user()->name}}</h4>
                        </div>
                        <div class="mt-3 mt-lg-0">
                           
                        </div>
                    </div><!-- end card header -->
                </div>
                <!--end col-->
            </div>
            <!--end row-->

            <!-- Requested DashBoard Display Info:
            1. Weekly Dwell Time graph (inbound/Outbound)
            2. Warehouse Utilization Graph by Warehouse (# of location vs occupied)
            3. Weekly Inbound count Truck
            4. Weekly Outbound count by Dispatch -->
      
            <div class="row">
                <? 
                $total_qty = 0; 
                foreach($warehouse_qty as $wh) : 
                    $total_qty += ($wh->wh_qty + $wh->reserve_qty);
                
                ?>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Total Qty of {{$wh->warehouse_name}}</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span
                                                class="counter-value" data-target="{{$wh->wh_qty + $wh->reserve_qty}}">0</span></h2>
                                        <p class="mb-0 text-muted"><span
                                                class="badge bg-light counter-value text-danger mb-0">0
                                            </span> with reserved qty</p>
                                    </div>
                                    
                                </div>
                            </div><!-- end card body -->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                <? endforeach?>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="fw-medium text-muted mb-0">Total All Warehouse Qty</p>
                                        <h2 class="mt-4 ff-secondary fw-semibold"><span
                                                class="counter-value" data-target="{{$total_qty}}">0</span></h2>
                                        <p class="mb-0 text-muted">Total qty as of today</p>
                                    </div>
                                    <!-- <div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-soft-info rounded-circle fs-2">
                                                <i data-feather="users" class="text-info"></i>
                                            </span>
                                        </div>
                                    </div> -->
                                </div>
                            </div><!-- end card body -->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
            </div> <!-- end col-->
        </div> <!-- end .h-100-->

    </div> <!-- end col -->
</div>
<div class="row">
    @include('dashboard.graphs.utilization');
</div>

<div class="row">
    <div class="col">
        <div class="h-100">
            <div class="row">
               
                <div class="col-xl-6">
                    @include('dashboard.graphs.inbound');
                </div>
                <div class="col-xl-6 d-none">
                    @include('dashboard.graphs.outbound');
                </div>

                
            </div>

        </div> <!-- end .h-100-->

    </div> <!-- end col -->

</div>


@endsection
@section('script')
<!-- apexcharts -->
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/swiper/swiper.min.js')}}"></script>
<script src="{{ URL::asset('/assets/js/dashboard/dashboard.js') }}"></script>
<!-- dashboard init -->
<!-- <script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script> -->
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
