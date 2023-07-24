document.addEventListener('DOMContentLoaded', function () {
    var table = new DataTable('#supplier-table', {
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function header(row) {
                var data = row.data();
                return 'Details for ' + data[0] + ' ' + data[1];
              }
            }),
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
              tableClass: 'table'
            })
          }
        }
    });
});

