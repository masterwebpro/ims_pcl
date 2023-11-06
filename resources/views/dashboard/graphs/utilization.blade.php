<? 
    $total_location = 0;
    $total_occupied = 0;
    $i=0; 
    foreach($location_cnt as $location) : 
        $total_location += $location->cnt_location;
        $total_occupied += $location->occupied;
    
    ?>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate overflow-hidden">
            <div class="position-absolute start-0" style="z-index: 0;">
                <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="200" height="120">
                    <style>
                        .s0 {
                            opacity: .05;
                            fill: var(--vz-success)
                        }
                    </style>
                    <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z" />
                </svg>
            </div>
            <div class="card-body" style="z-index:1 ;">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-3"> <?=$location->warehouse_name?> Utilization</p>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-0"><span class="counter-value" data-target="{{$location->occupied}}">0</span> / {{$location->cnt_location}}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div id="warehouse_<?=$i?>" data-colors='["--vz-success"]' class="apex-charts" dir="ltr"></div>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!--end col-->
    <?
        $series = number_format(($location->occupied / $location->cnt_location) * 100,2);

        if($series < 70) {
            $color = '#0ab39c';
        }
        if($series >= 70 && $series < 85) {
            $color = '#f7b84b';
        }
        if($series >= 85) {
            $color = '#f06548';
        }
    ?>
    <script>
        let myChart_<?=$i?> = new ApexCharts(document.querySelector("#warehouse_<?=$i?>"), {
            series: [<?=$series?>],
            chart: {
                type: "radialBar",
                width: 105,
                sparkline: {
                    enabled: !0
                }
            },
            dataLabels: {
                enabled: !1
            },
            plotOptions: {
                radialBar: {
                    hollow: {
                        margin: 0,
                        size: "70%"
                    },
                    track: {
                        margin: 1
                    },
                    dataLabels: {
                        show: !0,
                        name: {
                            show: !1
                        },
                        value: {
                            show: !0,
                            fontSize: "14px",
                            fontWeight: 600,
                            offsetY: 8
                        }
                    }
                }
            },
            colors:['<?=$color?>']
        })
        myChart_<?=$i?>.render();
    </script>

    <? $i++; endforeach?>


<div class="col-xl-3 col-md-6">
    <!-- card -->
    <div class="card card-animate overflow-hidden">
        <div class="position-absolute start-0" style="z-index: 0;">
            <svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 120" width="200" height="120">
                <style>
                    .s0 {
                        opacity: .05;
                        fill: var(--vz-success)
                    }
                </style>
                <path id="Shape 8" class="s0" d="m189.5-25.8c0 0 20.1 46.2-26.7 71.4 0 0-60 15.4-62.3 65.3-2.2 49.8-50.6 59.3-57.8 61.5-7.2 2.3-60.8 0-60.8 0l-11.9-199.4z" />
            </svg>
        </div>
        <div class="card-body" style="z-index:1 ;">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1 overflow-hidden">
                    <p class="text-uppercase fw-medium text-muted text-truncate mb-3"> Total WH Utilization</p>
                    <h4 class="fs-22 fw-semibold ff-secondary mb-0"><span class="counter-value" data-target="120">0</span> / {{$total_location}}</h4>
                </div>
                <div class="flex-shrink-0">
                    <div id="warehouse" data-colors='["--vz-success"]' class="apex-charts" dir="ltr"></div>
                </div>
            </div>
        </div><!-- end card body -->
    </div><!-- end card -->

    <?
         $oveall_series = number_format(($total_occupied / $total_location) * 100,2);

         if($oveall_series < 70) {
             $overall_color = '#0ab39c';
         }
         if($oveall_series >= 70 && $series < 85) {
             $overall_color = '#f7b84b';
         }
         if($oveall_series >= 85) {
             $overall_color = '#f06548';
         } 
    
    ?>
    <script>
        
        let warehouse = new ApexCharts(document.querySelector("#warehouse"), {
            series: [<?=$oveall_series?>],
            chart: {
                type: "radialBar",
                width: 105,
                sparkline: {
                    enabled: !0
                }
            },
            dataLabels: {
                enabled: !1
            },
            plotOptions: {
                radialBar: {
                    hollow: {
                        margin: 0,
                        size: "70%"
                    },
                    track: {
                        margin: 1
                    },
                    dataLabels: {
                        show: !0,
                        name: {
                            show: !1
                        },
                        value: {
                            show: !0,
                            fontSize: "14px",
                            fontWeight: 600,
                            offsetY: 8
                        }
                    }
                }
            },
            colors:['<?=$overall_color?>']
        })
        warehouse.render();
    </script>
</div><!-- end col -->