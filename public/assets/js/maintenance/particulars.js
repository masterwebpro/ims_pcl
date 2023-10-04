$(document).ready(function () {
    $(".select").select2();
});

function addRow(val = "") {
    var rows = $("#particular-list").find("tr").length;
    rowCount = (rows == 0) ? 0 : rows - 1;
    var newRow = `<tr>
        <td>
            <input type="text" class="form-control" id="code"
                name="code[]" placeholder="Enter code">
            <span class="text-danger error-msg code`+(rowCount)+`_error"></span>

        </td>
        <td>
            <input type="text" class="form-control" id="name"
                name="name[]" placeholder="Enter name">
            <span class="text-danger error-msg name`+(rowCount)+`_error"></span>

        </td>
        <td>
            <input type="text" class="form-control" id="amount"
                name="amount[]" placeholder="Enter amount">
            <span class="text-danger error-msg amount`+(rowCount)+`_error"></span>

        </td>
        <td>
            <div class="text-center">
                <button type="button" class="remove-row btn btn-icon btn-danger remove-truck mx-2 waves-effect waves-light">
                    <i class="ri-delete-bin-5-fill"></i>
                </button>
            </div>
        </td>
    </tr>`;
    $("#particular-list tbody").append(newRow);
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

  $(document).on('click', '.submit-particular', function (e) {
    e.preventDefault();
    var form_data = new FormData(document.getElementById("submit-particular"));
    form_data.append("_token", $('input[name=_token]').val());

    $.ajax({
        url: BASEURL + 'maintenance/particulars',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-particular').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    toastr.success(data.message);
                    setTimeout(function () {
                        window.location = BASEURL+'maintenance/particulars';
                    }, 300);
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-particular').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
});
