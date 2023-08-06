$(document).ready(function () {
  function addRow() {
      var newRow = `<tr>
            <td>
                <input type="text" class="form-control" id="option" placeholder="Enter Option">
            </td>
            <td>
                <input type="text" class="form-control" id="position" placeholder="Enter Position">
            </td>
            <td>
                <button type="submit" class="btn btn-danger btn-label rounded-pill remove-row"><i
                        class="ri-close-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                    Remove</button>
            </td>
        </tr>`;
      $("#dynamic-table tbody").append(newRow);
  }

  // Function to remove a row from the table
  function removeRow() {
    $(this).closest("tr").remove();
  }

  // Add click event to the "Add Row" button
  $("#add-row").on("click", function () {
    addRow();
  });

  // Add click event to the "Remove" button (using event delegation for dynamically added buttons)
  $(document).on("click", ".remove-row", function () {
    removeRow.call(this);
  });

  function getAllValues() {
    var values = [];
    $("#multiselect-table tbody tr").each(function () {
      var optionValue = $(this).find("select").val();
      var positionValue = $(this).find('input[type="text"]').val();
      values.push({
        option: optionValue,
        position: positionValue,
      });
    });
    return values;
  }
});

$(document).on("change", "#input_type", function () {
  var type = $(this).val();
  if (type.length == 0) {
    $("#is-required").addClass("d-none");
  } else {
    $("#is-required").removeClass("d-none");
  }
  switch (type) {
    case "text":
      $("#show-text").removeClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "dropdown":
      $("#show-dropdown-select").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "select":
      $("#show-dropdown-select").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "textarea":
      $("#show-textarea").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "date":
      $("#show-date").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "checkbox":
      $("#show-checkbox").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "price":
      $("#show-price").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "image":
      $("#show-image").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-dropdown").addClass("d-none");
      $("#show-multiselect").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");
      break;
    default:
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");
      break;
  }

  $('.submit-attribute').on('click', function (e) {
    e.preventDefault();
    var id = $('#id').val();

    var checkbox = document.getElementById("is_enabled");
    var is_enabled = (checkbox.checked) ? 1 : 0;

    var req = document.getElementById("is_required");
    var is_required = (req.checked) ? 1 : 0;

    var form_data = new FormData(document.getElementById("form-attribute"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("is_enabled", is_enabled);
    form_data.append("is_required", is_required);
    form_data.append("id", id);

    $.ajax({
        url: BASEURL + 'maintenance/attributes',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#error-handling ul').html('');
            $('#error-handling').addClass('d-none');
            $('#preloading').modal('show');
        },
        success: function (data) {
            if($.isEmptyObject(data.error)) {
                if(data.success == true) {
                    console.log(data.data)
                    setTimeout(function(){
                        window.location= BASEURL + 'maintenance/attribute/'+data.id+'/edit';
                    }, 300);
                    toastr.success(data.message);

                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $('#error-handling').removeClass('d-none');

                $.each(data.error, function(prefix, val) {
                    $('#error-handling ul').append('<li>'+val+'</li>');
                });
            }
        },
        complete: function() {
            $('#preloading').modal('hide');
        }
    });
});
});


