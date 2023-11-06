$(document).ready(function () {
    // Get the current date
    const currentDate = new Date();

    // Calculate the first day of the current month
    const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

    // Calculate the last day of the current month
    const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

    // Initialize Flatpickr with the default date range
    flatpickr("#date-range-picker1", {
        mode: "range",
        defaultDate: [firstDayOfMonth, lastDayOfMonth],
        altInput: true, // This enables an alternative text input for readability
        altFormat: "d M, Y", // Format for the alternative input
        dateFormat: "Y-m-d", // Format for the actual date range value
    });

    flatpickr("#date-range-picker2", {
        mode: "range",
        defaultDate: [firstDayOfMonth, lastDayOfMonth],
        altInput: true, // This enables an alternative text input for readability
        altFormat: "d M, Y", // Format for the alternative input
        dateFormat: "Y-m-d", // Format for the actual date range value
    });

    getInboundCount("yearly");

});

$(document).on('change',"#date-range-picker1",function(){
    getInboundCount("daily");
});

$(document).on('click','#select-daily',function(){
    $("#yearly").addClass('d-none');
    $("#daily").removeClass('d-none');
    getInboundCount("daily");
});

$(document).on('click','#select-yearly',function(){
    $("#daily").addClass('d-none');
    $("#yearly").removeClass('d-none');
    getInboundCount("yearly");
});

function getInboundCount(type){
    $("#in-labels").text(type);
    var date = $("#date-range-picker1").val();
    var year = $("#year").val();
    $.ajax({
        url: BASEURL + 'dashboard/getInboundCount',
        data: {
            type : type,
            date : date,
            year : year,
            _token: $('input[name=_token]').val()
        },
        method: "get",
        dataType: 'json',
        success: function (data) {
            setTimeout(function () {
                $(".inbound-cnt").text(data.tot_trans);
                $(".inbound-qty").text(data.tot_qty);
                inboundChart(data);
            }, 300);
        }
    });
}

let myInboundChart;

function inboundChart(data){
    var lineChartCustomerColors = getChartColorsArray("inbound_chart");
    if (lineChartCustomerColors) {
        var options = {
            series: [{
            name: "Transaction",
            type: "area",
            data: data.transaction
            }, {
            name: "Quantity",
            type: "bar",
            data: data.quantity
            }],
            chart: {
            height: 370,
            type: "line",
            toolbar: {
                show: false
            }
            },
            stroke: {
            curve: "straight",
            dashArray: [0, 0, 8],
            width: [2, 0, 2.2]
            },
            fill: {
            opacity: [0.1, 0.9, 1]
            },
            markers: {
            size: [0, 0, 0],
            strokeWidth: 2,
            hover: {
                size: 4
            }
            },
            xaxis: {
            categories: data.labels,
            axisTicks: {
                show: false
            },
            axisBorder: {
                show: false
            }
            },
            grid: {
            show: true,
            xaxis: {
                lines: {
                show: true
                }
            },
            yaxis: {
                lines: {
                show: false
                }
            },
            padding: {
                top: 0,
                right: -2,
                bottom: 15,
                left: 10
            }
            },
            legend: {
            show: true,
            horizontalAlign: "center",
            offsetX: 0,
            offsetY: -5,
            markers: {
                width: 9,
                height: 9,
                radius: 6
            },
            itemMargin: {
                horizontal: 10,
                vertical: 0
            }
            },
            plotOptions: {
            bar: {
                columnWidth: "30%",
                barHeight: "70%"
            }
            },
            colors: lineChartCustomerColors,
            tooltip: {
            shared: true,
            y: [{
                formatter: function formatter(y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(0);
                }

                return y;
                }
            }, {
                formatter: function formatter(y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(2);
                }

                return y;
                }
            }, {
                formatter: function formatter(y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(0) + " Sales";
                }

                return y;
                }
            }]
            }
        };
        if (myInboundChart) {
            myInboundChart.destroy();
        }
        myInboundChart = new ApexCharts(document.querySelector("#inbound_chart"), options);
        myInboundChart.render();
    }
}

function getChartColorsArray(chartId) {
    if (document.getElementById(chartId) !== null) {
      var colors = document.getElementById(chartId).getAttribute("data-colors");

      if (colors) {
        colors = JSON.parse(colors);
        return colors.map(function (value) {
          var newValue = value.replace(" ", "");

          if (newValue.indexOf(",") === -1) {
            var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
            if (color) return color;else return newValue;
          } else {
            var val = value.split(",");

            if (val.length == 2) {
              var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
              rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
              return rgbaColor;
            } else {
              return newValue;
            }
          }
        });
      } else {
        console.warn('data-colors atributes not found on', chartId);
      }
    }
}

var outboundColors = getChartColorsArray("outbound_chart");

if (outboundColors) {
    var options = {
        series: [{
        name: "Transaction",
        type: "area",
        data: [34, 65, 46, 68, 49, 61, 42, 44, 78, 52, 63, 67]
        }, {
        name: "Quantity",
        type: "bar",
        data: [89.25, 98.58, 68.74, 108.87, 77.54, 84.03, 51.24, 28.57, 92.57, 42.36, 88.51, 36.57]
        }],
        chart: {
        height: 370,
        type: "line",
        toolbar: {
            show: false
        }
        },
        stroke: {
        curve: "straight",
        dashArray: [0, 0, 8],
        width: [2, 0, 2.2]
        },
        fill: {
        opacity: [0.1, 0.9, 1]
        },
        markers: {
        size: [0, 0, 0],
        strokeWidth: 2,
        hover: {
            size: 4
        }
        },
        xaxis: {
        categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        axisTicks: {
            show: false
        },
        axisBorder: {
            show: false
        }
        },
        grid: {
        show: true,
        xaxis: {
            lines: {
            show: true
            }
        },
        yaxis: {
            lines: {
            show: false
            }
        },
        padding: {
            top: 0,
            right: -2,
            bottom: 15,
            left: 10
        }
        },
        legend: {
        show: true,
        horizontalAlign: "center",
        offsetX: 0,
        offsetY: -5,
        markers: {
            width: 9,
            height: 9,
            radius: 6
        },
        itemMargin: {
            horizontal: 10,
            vertical: 0
        }
        },
        plotOptions: {
        bar: {
            columnWidth: "30%",
            barHeight: "70%"
        }
        },
        colors: outboundColors,
        tooltip: {
        shared: true,
        y: [{
            formatter: function formatter(y) {
            if (typeof y !== "undefined") {
                return y.toFixed(0);
            }

            return y;
            }
        }, {
            formatter: function formatter(y) {
            if (typeof y !== "undefined") {
                return y.toFixed(2);
            }

            return y;
            }
        }, {
            formatter: function formatter(y) {
            if (typeof y !== "undefined") {
                return y.toFixed(0) + " Sales";
            }

            return y;
            }
        }]
        }
    };
    var chart = new ApexCharts(document.querySelector("#outbound_chart"), options);
    chart.render();
}
