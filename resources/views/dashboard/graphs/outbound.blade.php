<div class="card">
    <div class="card-header border-0 align-items-center d-flex">
        <h4 class="card-title mb-0 flex-grow-1">Outbound</h4>
        <div>
            <div class="mt-lg-0">
                <form action="javascript:void(0);">
                    <div class="row g-3 mb-0 align-items-center">
                        <div class="col-sm-auto">
                            <div class="input-group">
                                <input type="text"
                                    class="form-control border-0 dash-filter-picker shadow" id="date-range-picker2"
                                    data-provider="flatpickr" data-range-date="true"
                                    data-date-format="d M, Y"
                                    placeholder="Select date range">
                                <div
                                    class="input-group-text bg-primary border-primary text-white">
                                    <i class="ri-calendar-2-line"></i>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-auto d-none">
                            <button type="button"
                                class="btn btn-soft-info btn-icon waves-effect waves-light layout-rightside-btn"><i
                                    class="ri-pulse-line"></i></button>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>
        </div>
    </div><!-- end card header -->

    <div class="card-header p-0 border-0 bg-soft-light">
        <div class="row g-0 text-center">
            <div class="col-6 col-sm-6">
                <div class="p-3 border border-dashed border-start-0">
                    <h5 class="mb-1"><span class="counter-value"
                            data-target="7585">0</span></h5>
                    <p class="text-muted mb-0">Transaction</p>
                </div>
            </div>
            <!--end col-->
            <div class="col-6 col-sm-6">
                <div class="p-3 border border-dashed border-start-0">
                    <h5 class="mb-1"><span class="counter-value"
                            data-target="22">0</span></h5>
                    <p class="text-muted mb-0">Quantity</p>
                </div>
            </div>
            <!--end col-->
        </div>
    </div><!-- end card header -->

    <div class="card-body p-0 pb-2">
        <div class="w-100">
            <div id="outbound_chart"
                data-colors='["--vz-primary", "--vz-warning"]'
                class="apex-charts" dir="ltr"></div>
        </div>
    </div><!-- end card body -->
</div><!-- end card -->
