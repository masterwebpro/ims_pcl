$(document).ready(function () {
    $(".select").select2();
});

function addRow(val = "") {
    populateTruckType();
    var rows = $("#truck-list").find("tr").length;
    rowCount = (rows == 0) ? 0 : rows - 1;
    var newRow = `<tr>
        <td>
            <select class="form-select select2 truck_type" required="required" id="vehicle_type" name="vehicle_type[]">
                <option value="">Select Truck Type</option>
            </select>
            <span class="text-danger error-msg vehicle_type`+(rowCount)+`_error"></span>
        </td>
        <td>
            <input type="text" class="form-control" id="plate_no"
                name="plate_no[]" placeholder="Enter Plate No.">
            <span class="text-danger error-msg plate_no`+(rowCount)+`_error"></span>

        </td>
        <td>
            <div class="text-center">
                <button type="button" class="remove-row btn btn-icon btn-danger remove-truck mx-2 waves-effect waves-light">
                    <i class="ri-delete-bin-5-fill"></i>
                </button>
            </div>
        </td>
    </tr>`;
    $("#truck-list tbody").append(newRow);
  }

  function removeRow() {
    $(this).closest("tr").remove();
  }

  $("#add-row").on("click", function () {
    addRow();
  });

  $(document).on("click", ".remove-row", function () {
    removeRow.call(this);
  });

  $(document).on('click', '.submit-trucker', function (e) {
    e.preventDefault();
    var checkbox = document.getElementById("is_enabled");
    if(checkbox){
     var is_enabled = checkbox.checked ? 1 : 0;
    }
    var form_data = new FormData(document.getElementById("submit-trucker"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("is_enabled", is_enabled);

    $.ajax({
        url: BASEURL + 'maintenance/plate',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-withdrawal').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    showSuccess(data.message);
                    setTimeout(function () {
                        window.location = BASEURL+'maintenance/plate';
                    }, 300);
                    $('#preloading').modal('hide');
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-trucker').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
});
