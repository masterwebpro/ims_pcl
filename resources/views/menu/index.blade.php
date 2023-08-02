@extends('layouts.master')
@section('title')
    Menu
@endsection
@section('css')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboards
        @endslot
        @slot('title')
            Menu
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">All Menu List</h5>
                        {{-- <div class="flex-shrink-0">
                            <a href="{{ URL::to('maintenance/menu/create') }}"class="btn btn-danger add-po"><i
                                    class="ri-add-line align-bottom me-1"></i> Create menu</a>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route('menu.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-xxl-4 col-sm-12">
                                <div class="search-box">
                                    <input type="text" name="q" class="form-control search"
                                        placeholder="Search for tasks or something...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->

                            <div class="col-xxl-2 col-sm-4">
                                <div class="input-light">
                                    <select class="form-control" name="filter_date" id="filter_date">
                                        <option value="created_at">Created Date</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xxl-2 col-sm-4">
                                <input type="text" class="form-control" name="date" id="date_picker"
                                    data-provider="flatpickr" data-date-format="Y-d-m" data-range-date="true"
                                    placeholder="Select date range">
                            </div>
                            <!--end col-->

                            <div class="col-xxl-3 col-sm-4">
                                <div class="input-light">
                                    <select class="form-control" data-choices data-choices-search-false name="status"
                                        id="status">
                                        <option value="all" selected>All</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-1 col-sm-4">
                                <button type="submit" class="btn btn-primary w-100"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Filters
                                </button>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
                <!--end card-body-->
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="d-flex flex-wrap gap-2 mb-3 mb-lg-0 ">
                            <button type="button" data-status="open" class="btn btn-warning btn-label rounded-pill submit-uom"><i class="ri-folder-add-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add Folder</button>
                            <button type="button" data-status="open" class="btn btn-success btn-label rounded-pill submit-uom"><i class="ri-folder-line label-icon align-middle rounded-pill fs-16 me-2"></i> Add Item</button>
                            <button class="btn btn-primary float-right ml-2" data-toggle="modal" data-target="#exampleModalgrid">Open Modal 2</button>
                        </div>
                    </div>
                    <!-- Accordions with Icons -->
                    <? foreach($menu_list as $menu) : ?>
                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-primary" id="{{ $menu->id }}">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="accordionwithiconExample{{ $menu->id }}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#accor_iconExamplecollapse{{ $menu->id }}" aria-expanded="true"
                                    aria-controls="accor_iconExamplecollapse{{ $menu->id }}">
                                    <i class="{{ $menu->icon }} text-warning mr-2"></i> {{ $menu->menu_name }}
                                </button>
                            </h2>
                            <div id="accor_iconExamplecollapse{{ $menu->id }}" class="accordion-collapse collapse show"
                                aria-labelledby="accordionwithiconExample{{ $menu->id }}" data-bs-parent="#id-{{ $menu->id }}">
                                <div class="accordion-body">
                                </div>
                            </div>
                        </div>
                    </div>
                    <? endforeach; ?>
                </div>
                <!--end card-body-->
            </div>
            <!--end card-->
        </div>

        <div class="modal fade" id="exampleModalgrid" tabindex="-1" aria-labelledby="exampleModalgridLabel" aria-modal="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalgridLabel">Grid Modals</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="javascript:void(0);">
                            <div class="row g-3">
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="Enter firstname">
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Enter lastname">
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <label for="genderInput" class="form-label">Gender</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
                                            <label class="form-check-label" for="inlineRadio1">Male</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
                                            <label class="form-check-label" for="inlineRadio2">Female</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
                                            <label class="form-check-label" for="inlineRadio3">Others</label>
                                        </div>
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="emailInput" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emailInput" placeholder="Enter your email">
                                    </div>
                                </div><!--end col-->
                                <div class="col-xxl-6">
                                    <div>
                                        <label for="passwordInput" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="passwordInput" value="451326546">
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->



@endsection
@section('script')
    <script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/modal.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
