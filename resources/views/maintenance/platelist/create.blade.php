@extends('layouts.master')
@section('title') Plate No @endsection
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
@slot('title') Plate No @endslot
@endcomponent


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Plate No Creation</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-trucker"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <a  href="{{ URL::to('maintenance/plate') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form name="submit-trucker" id="submit-trucker">
            @csrf
                <div class="card-body">
                    <div class="form-container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row mb-4">
                                    <div class="col-md-6 form-group">
                                        <label for="trucker_name" class="form-label">Trucker Name <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2" required="required" id="trucker_name" name="trucker_id">
                                            <option value="">Select Truck Name</option>
                                            <? foreach($trucker as $trk) : ?>
                                                <option value="<?=$trk->id?>" ><?=$trk->trucker_name?></option>
                                            <? endforeach;?>
                                        </select>
                                        <span class="text-danger error-msg trucker_name_error"></span>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="is_enabled" class="form-label">Enable </label>
                                        <div class="form-check form-switch form-switch-success form-switch-md" dir="ltr">
                                            <input type="checkbox" class="form-check-input" id="is_enabled"
                                                name="is_enabled" checked>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-nowrap" id="truck-list">
                                            <thead>
                                                <tr class="table-active">
                                                    <th scope="col">Vehicle Type</th>
                                                    <th scope="col">Plate No.</th>
                                                    <th scope="col" class="text-center"> <button type="button" id="add-row" class="btn btn-info btn-label rounded-pill"><i
                                                        class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                    Add</button></th>
                                                </tr>
                                            </thead>
                                            <tbody id="truck">
                                                <tr>
                                                    <td>
                                                        <select class="form-select select2 truck_type" required="required" id="vehicle_type" name="vehicle_type[]">
                                                            <option value="">Select Vehicle Type</option>
                                                            <? foreach($truck_type as $truck) : ?>
                                                            <option value="<?=$truck->vehicle_code?>" ><?="(".$truck->vehicle_code.") ".$truck->vehicle_desc?></option>
                                                            <? endforeach;?>
                                                        </select>
                                                        <span class="text-danger error-msg vehicle_type0_error"></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="plate_no"
                                                            name="plate_no[]" placeholder="Enter Plate No.">
                                                        <span class="text-danger error-msg plate_no0_error"></span>
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
                                        <!--end table-->
                                    </div>
                            </div>
                            <!--end col-->
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
<script src="{{ URL::asset('/assets/js/maintenance/plate.js') }}"></script>

@endsection
