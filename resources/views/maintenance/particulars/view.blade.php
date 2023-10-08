@extends('layouts.master')
@section('title')
    Particular
@endsection
@section('css')
    <!--datatable css-->
    <link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Maintenance
        @endslot
        @slot('title')
            Particular
        @endslot
    @endcomponent


    <div class="alert alert-danger d-none" id="error-handling" role="alert">
        <ul class="errors">
        </ul>
    </div>

    <div class="row justify-content-center">
        <div class="col-xxl-10">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Particular View</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <a href="{{ URL::to('maintenance/particulars') }}/<?= _encode($particular->particular_id) ?>/edit"
                                class="btn btn-success btn-label rounded-pill"><i
                                    class="ri-edit-line label-icon align-middle rounded-pill fs-16 me-2"></i> Edit</a>
                            <a href="{{ URL::to('maintenance/particulars') }}"
                                class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div>
                    <form method="POST" name="form-particular" action="javascript:void(0);" id="form-particular"
                        class="row g-3 needs-validation" novalidate>
                        @csrf
                        <div class="card-body">
                            <div class="form-container">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row mb-4">
                                            <div class="col-md-4 form-group">
                                                <label for="particular_id" class="form-label">Code</label>
                                                <input type="hidden" class="form-control" name="particular_id"
                                                    id="particular_id" value="{{ $particular->particular_id }}">
                                                <input type="text" class="form-control" disabled name="code"
                                                    id="code" value="{{ $particular->code }}" placeholder="Enter Code">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="inputState" class="form-label">Name</label>
                                                <input type="text" id="name" name="name" disabled
                                                    class="form-control" value="{{ $particular->name }}"
                                                    placeholder="Enter Name">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="inputState" class="form-label">Amount</label>
                                                <input type="text" id="plate_no" name="plate_no" disabled
                                                    class="form-control" value="{{ $particular->amount }}"
                                                    placeholder="Enter Amount">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div> <!-- end col -->
        </div> <!-- end col -->
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
        <script src="{{ URL::asset('/assets/js/maintenance/particulars.js') }}"></script>
    @endsection
