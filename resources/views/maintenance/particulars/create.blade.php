@extends('layouts.master')
@section('title') Particular @endsection
@section('css')
<!--datatable css-->
<link href="{{ URL::asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="{{ URL::asset('assets/css/responsive.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Maintenance @endslot
@slot('title') Particular @endslot
@endcomponent


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Particular Creation</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-particular"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <a  href="{{ URL::to('maintenance/particulars') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form name="submit-particular" id="submit-particular">
            @csrf
                <div class="card-body">
                    <div class="form-container">
                        <div class="row ">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-nowrap" id="particular-list">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col">Code</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col" class="text-center"> <button type="button" id="add-row" class="btn btn-info btn-label rounded-pill"><i
                                                    class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                Add</button></th>
                                            </tr>
                                        </thead>
                                        <tbody id="particular">
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" id="code"
                                                        name="code[]" placeholder="Enter code">
                                                    <span class="text-danger error-msg code0_error"></span>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" id="name"
                                                        name="name[]" placeholder="Enter name">
                                                    <span class="text-danger error-msg name0_error"></span>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control numeric" id="amount"
                                                        name="amount[]" placeholder="Enter amount">
                                                    <span class="text-danger error-msg amount0_error"></span>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <button type="button" class="remove-row btn btn-icon btn-danger remove-truck mx-2 waves-effect waves-light">
                                                            <i class="ri-delete-bin-5-fill"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
