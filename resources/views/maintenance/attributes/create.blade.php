@extends('layouts.master')
@section('title')
    Attributes
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
            Attributes
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
                    <h4 class="card-title mb-0 flex-grow-1">Attribute Creation</h4>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0">
                            <button type="button" data-status="open"
                                class="btn btn-success btn-label rounded-pill submit-attribute"><i
                                    class="ri-check-double-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Save</button>
                            <a href="{{ URL::to('maintenance/attributes') }}"
                                class="btn btn-primary btn-label rounded-pill"><i
                                    class="ri-arrow-go-back-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                Back</a>
                        </div>
                    </div>
                </div>
                <form method="POST" name="form-attribute" action="javascript:void(0);" id="form-attribute"
                    class="row g-3 needs-validation" novalidate>
                    @csrf
                    <div class="card-body">
                        <div class="form-container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <label for="attribute_code" class="form-label">Attribute Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="attribute_code" id="attribute_code" value=""
                                                placeholder="Enter Attributes Code">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="attribute_name" class="form-label">Attribute Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" required="required"
                                                name="attribute_name" id="attribute_name" value=""
                                                placeholder="Enter Attributes Name">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <label for="attribute_display_name" class="form-label">Label</label>
                                            <input type="text" class="form-control" name="attribute_display_name"
                                                id="attribute_display_name" value="" placeholder="Enter Display Name">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="customSwitchsizemd" class="form-label">Enable </label>
                                            <div class="form-check form-switch form-switch-md" dir="ltr">
                                                <input type="checkbox" class="form-check-input" id="customSwitchsizemd" name="is_enabled"
                                                    checked>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6 form-group">
                                            <label for="input_type" class="form-label">Attribute Property <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2" required="required" id="input_type"
                                                name="attribute_input_type">
                                                <option value="">Select Input Type</option>
                                                <?foreach($input_type as $input):?>
                                                <option value="<?= $input['datatype'] ?>">{{ $input['label'] }}</option>
                                                <?endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="category_id" class="form-label">Applicable Categories</label>
                                            <select class="form-control category_id" id="category_id" data-choices name="category_id"
                                                data-choices-removeItem multiple>
                                                <option value="">Select Category</option>
                                                <? foreach ($category as $key => $cat) :?>
                                                <option value="{{ $cat->category_id }}">{{ $cat->category_name }}
                                                </option>
                                                <? endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4" id="attr-prop">
                                <div class="col-md-6 d-none mb-1" id="is-required">
                                    <div class="form-group">
                                        <label for="">Is Required </label>
                                        <select class="form-select select2"
                                                name="is_required">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none" id="show-text">
                                    <div class="form-group">
                                        <label for="">Default Text(Optional) </label>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Enter Text" name="textfield">
                                </div>
                                <div class="col-md-6 d-none" id="show-textarea">
                                    <div class="form-group">
                                        <label>Default Message(Optional)</label>
                                        <textarea name="" class="form-control" id=""rows="3" placeholder="Enter Text" name="textarea"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12 d-none" id="show-dropdown-select">
                                    <div class="table-responsive">
                                        <table id="dynamic-table" class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Option Name</th>
                                                    <th>Option Value</th>
                                                    <th>Position</th>
                                                    <th><button type="submit"
                                                            class="btn btn-success btn-label rounded-pill"
                                                            id="add-row"><i
                                                                class="ri-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                            Add</button>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" id="option" name="name"
                                                            placeholder="Enter Option Name">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="option" name="value"
                                                            placeholder="Enter Option Value">
                                                    </td>
                                                    <td>
                                                        <input type="url" class="form-control" id="position" name="position"
                                                            placeholder="Enter Position">
                                                    </td>
                                                    <td>
                                                        <button type="submit"
                                                            class="btn btn-danger btn-label rounded-pill remove-row"><i
                                                                class="ri-close-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                                                            Remove</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none" id="show-checkbox">
                                    <div class="form-group">
                                        <label for="">Default Checkbox </label>
                                        <select class="form-select select2"
                                                name="checkbox">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-6 d-none" id="show-date">
                                    <div class="form-group">
                                        <label for="">Default Date(Optional) </label>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Enter Date" name="date">
                                </div>
                                <div class="col-md-6 d-none" id="show-price">
                                    <div class="form-group">
                                        <label for="">Default Price(Optional) </label>
                                    </div>
                                    <input type="text" class="form-control" placeholder="Enter Price" name="price">
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
    <script src="{{ URL::asset('/assets/js/maintenance/attributes.js') }}"></script>
@endsection
