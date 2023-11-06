
<div class="card">
    <div class="card-header border-0 align-items-center d-flex">
        <h4 class="card-title mb-0 flex-grow-1">Inbound <span class="badge badge-gradient-danger text-uppercase" id="in-labels">Daily</span></h4>
        <div>
            <div class="mt-lg-0">
                <form action="javascript:void(0);">
                    <div class="row g-3 mb-0 align-items-center">
                        <div class="col-sm-auto">
                            <div class="input-group d-none" id="daily">
                                <input type="text"
                                    class="form-control border-0 dash-filter-picker shadow"  id="date-range-picker1"
                                    data-provider="flatpickr" data-range-date="true"
                                    data-date-format="d M, Y"
                                    placeholder="Select date range">
                                <div
                                    class="input-group-text bg-primary border-primary text-white">
                                    <i class="ri-calendar-2-line"></i>

                                </div>
                                <button type="button" class="btn btn-primary" id="select-yearly">
                                    <i class="ri-globe-line align-bottom"></i> Yearly
                                </button>
                            </div>
                            <div class="input-group" id="yearly">
                                <input type="number"
                                    class="form-control" id="year" name="year" value="{{ date('Y') }}">
                                <button type="button" class="btn btn-primary" id="select-daily">
                                    <i class="ri-sun-line align-bottom"></i> Daily
                                </button>
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
                    <h5 class="mb-1"><span class="counter-value inbound-cnt"
                            data-target="0">0</span></h5>
                    <p class="text-muted mb-0">Transaction</p>
                </div>
            </div>
            <!--end col-->
            <div class="col-6 col-sm-6">
                <div class="p-3 border border-dashed border-start-0">
                    <h5 class="mb-1"><span class="counter-value inbound-qty"
                            data-target="0">0</span></h5>
                    <p class="text-muted mb-0">Quantity</p>
                </div>
            </div>
            <!--end col-->
        </div>
    </div><!-- end card header -->

    <div class="card-body p-0 pb-2">
        <div class="w-100">
            <div id="inbound_chart"
                data-colors='["--vz-success", "--vz-danger"]'
                class="apex-charts" dir="ltr"></div>
        </div>
    </div><!-- end card body -->
</div><!-- end card -->
