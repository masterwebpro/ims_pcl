@extends('layouts.master')
@section('title') Storage Location @endsection
@section('css')

@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') Maintenance @endslot
@slot('title') Storage Location > Create @endslot
@endcomponent


<div class="alert alert-danger d-none" id="error-handling" role="alert">
    <ul class="errors">
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-xxl-11">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Storage Location Creation</h4>
                <div class="flex-shrink-0">
                    <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                        <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-location"><i class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
                        <a  href="{{ URL::to('maintenance/location') }}" class="btn btn-primary btn-label rounded-pill"><i class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i> Back</a>
                    </div>
                </div>
            </div><!-- end card header -->
            <form  method="POST" name="form-location" action="javascript:void(0);" id="form-location" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="card-body">
                <div class="form-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row mb-4 mx-3">
                                <div class="col-md-3 form-group">
                                    <input type="hidden" name="storage_location_id" id="storage_location_id"/>
                                    <label for="client_id" class="form-label">Client Name <span class="text-danger">*</span></label>
                                    <select class="form-select select2" required="required" id="client" name="client">
                                        <option value="">Select Client</option>                                                            
                                        <? foreach($client_list as $client) : ?>
                                            <option value="<?=$client->id?>" ><?=$client->client_name?></option>
                                        <? endforeach;?>
                                    </select>
                                    <span class="text-danger error-msg client_error"></span>
                                    <div class="invalid-feedback text-danger error-msg client_error">Client Name is Required</div>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="store_id" class="form-label">Store Name <span class="text-danger">*</span></label>
                                    <select class="form-select select2" required="required" id="store" name="store">
                                        <option value="">Select Store/Warehouse</option>                                                            
                                    </select>
                                    <span class="text-danger error-msg store_error"></span>
                                </div>

                                <div class="col-md-3 form-group">
                                    <label for="warehouse_id" class="form-label">Warehouse Location <span class="text-danger">*</span></label>
                                    <select class="form-select select2" required="required" id="warehouse" name="warehouse">
                                        <option value="">Select warehouse location</option>                                                            
                                    </select>
                                    <span class="text-danger error-msg warehouse_error"></span>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="client_id" class="form-label">&nbsp;</label>
                                    <div class="form-check form-switch form-switch-custom form-switch-primary">
                                    
                                        <label class="form-check-label" for="is_enabled">Is enabled </label>
                                        <input class="form-check-input" type="checkbox" name="is_enabled" role="switch" id="is_enabled" checked>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4 mx-3">
                                <table class="table align-middle table-nowrap mb-0" id="location-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Rack</th>
                                            <th scope="col">Layer</th>
                                            <th scope="col">Row</th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="rack[]" id="rack" value="" placeholder="Enter Rack">
                                                <span class="text-danger error-msg rack0_error"></span>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="level[]" id="level" value="" placeholder="Enter Level">
                                                <span class="text-danger error-msg level0_error"></span>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" name="row[]" id="row" value="" placeholder="Enter Row No.">
                                                <span class="text-danger error-msg row0_error"></span>
                                            </td>
                                           
                                        </tr>   
                                    </tbody>
                                </table>
                                <div class="mt-2">
                                   
                                    <button type="button" id="add-row" class="btn btn-primary btn-small"><i class="ri-add-line align-middle"></i></button>
                                    <button type="button" id="remove-row" class="btn btn-danger btn-small"><i class="bx bx-trash align-middle"></i></button>
                                   
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
<script src="{{ URL::asset('/assets/js/maintenance/location.js') }}"></script>

@endsection
