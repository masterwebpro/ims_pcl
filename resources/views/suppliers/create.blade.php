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

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'suppliers.store']) !!}

		<div class="mb-3">
			{{ Form::label('supplier_name', 'Supplier_name', ['class'=>'form-label']) }}
			{{ Form::text('supplier_name', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('supplier_code', 'Supplier_code', ['class'=>'form-label']) }}
			{{ Form::text('supplier_code', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('contact_no', 'Contact_no', ['class'=>'form-label']) }}
			{{ Form::text('contact_no', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('supplier_address', 'Supplier_address', ['class'=>'form-label']) }}
			{{ Form::textarea('supplier_address', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}



	@endsection
@section('script')
<!-- apexcharts -->
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/swiper/swiper.min.js')}}"></script>
<!-- dashboard init -->
<script src="{{ URL::asset('/assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection