$(document).ready(function () {
  var attribute_id = $("#attribute_id").val();
  var entity = getAttributeEntity(attribute_id);
  var input_type = $("#input_type").val();
  entity.forEach(function (val) {
    switch (input_type) {
      case "text_field":
        $("#show-text").removeClass("d-none");
        $("#show-dropdown").addClass("d-none");
        $("#show-dropdown-select").addClass("d-none");
        $("#show-textarea").addClass("d-none");
        $("#show-date").addClass("d-none");
        $("#show-checkbox").addClass("d-none");
        $("#show-price").addClass("d-none");
        $("#show-image").addClass("d-none");
        $("input[name=textfield]").val(val["attribute_entity_default_value"]);
        $("input[name=attribute_entity_id]").val(val["attribute_entity_id"]);
        break;
      case "dropdown":
        $("#show-dropdown").removeClass("d-none");
        $("#show-text").addClass("d-none");
        $("#show-dropdown-select").addClass("d-none");
        $("#show-textarea").addClass("d-none");
        $("#show-date").addClass("d-none");
        $("#show-checkbox").addClass("d-none");
        $("#show-price").addClass("d-none");
        $("#show-image").addClass("d-none");
        addDropdown(val);
        break;
      case "select":
        $("#show-dropdown").addClass("d-none");
        $("#show-text").addClass("d-none");
        $("#show-dropdown-select").removeClass("d-none");
        $("#show-textarea").addClass("d-none");
        $("#show-date").addClass("d-none");
        $("#show-checkbox").addClass("d-none");
        $("#show-price").addClass("d-none");
        $("#show-image").addClass("d-none");
        addRow(val);
        break;
      case "textarea":
        $("#show-text").addClass("d-none");
        $("#show-dropdown").addClass("d-none");
        $("#show-dropdown-select").addClass("d-none");
        $("#show-textarea").removeClass("d-none");
        $("#show-date").addClass("d-none");
        $("#show-checkbox").addClass("d-none");
        $("#show-price").addClass("d-none");
        $("#show-image").addClass("d-none");
        $("input[name=textarea]").val(val["attribute_entity_default_value"]);
        $("input[name=attribute_entity_id]").val(val["attribute_entity_id"]);
        break;
      case "date":
        $("#show-text").addClass("d-none");
        $("#show-dropdown").addClass("d-none");
        $("#show-dropdown-select").addClass("d-none");
        $("#show-textarea").addClass("d-none");
        $("#show-date").removeClass("d-none");
        $("#show-checkbox").addClass("d-none");
        $("#show-price").addClass("d-none");
        $("#show-image").addClass("d-none");
        $("input[name=date]").val(val["attribute_entity_default_value"]);
        $("input[name=attribute_entity_id]").val(val["attribute_entity_id"]);
        break;
    }
  });
  function addRow(val = "") {
    var newRow = `<tr>
    <td>
        <input type="hidden" name="attribute_entity_id">
        <input type="text" class="form-control" id="option" placeholder="Enter Option" name="opt" value="`+ ((val['attribute_entity_value']) ? val['attribute_entity_value'] : '')+`">
    </td>
    <td>
        <input type="text" class="form-control" id="position" placeholder="Enter Position" name="posts" value="`+ ((val['attribute_entity_position']) ? val['attribute_entity_position'] : '')+`">
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

  $("#add-dropdown").on("click", function () {
    addDropdown();
  });
  // Function to remove a row from the card body div row?
  $(document).on("click", "#remove-dropdown", function () {
    $(this).closest(".row-to-remove").remove();
  });

  function addDropdown(val = "") {
    var type = $("#attribute_id").data('type');
    var newdropdown =
      `
<div class="row m-1 row-to-remove">
    <div class="col-md-4">
        <div class="form-group">
            <input type="hidden" name="attribute_entity_id">
            <label for="option">Option</label>
            <input type="text" class="form-control" name="option" value="` +
      ((val['attribute_entity_name']) ? val['attribute_entity_name'] : '') +
      `">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="option">Position</label>
            <input type="text" class="form-control" name="position" value="` +
      ((val['attribute_entity_position']) ? val['attribute_entity_position'] : '') +
      `">
        </div>
    </div>

    <div class="col-md-2 text-center">
        <div class="form-group">
            <label for="option">Default</label>
            <input type="checkbox" name="is_default" class="is_default" ` +
      ((val["is_default"] == 1) ? " checked " : "") +
      `>
        </div>
    </div>`;

    newdropdown +=
      `<div class="col-md-2">
        <div class="form-group">
            <button type="submit" ` +
      ((type != "edit") ? "disabled" : "") +
      ` class="btn btn-danger btn-label rounded-pill"
                id="remove-dropdown"><i class="ri-close-line label-icon align-middle rounded-pill fs-16 me-2"></i>
                Remove</button>
        </div>
    </div> `;
    newdropdown +=
      ` <div class="col-md-10 mt-1">
        <div class="form-group">
            <label for="">Description</label>
            <textarea name="description" id="description" rows="3"
                class="form-control">` +
      ((val["attribute_entity_description"]) ? val["attribute_entity_description"] : '') +
      `</textarea>
        </div>
    </div>
</div>`;
    $("#dynamic-dropdown .card-body").append(newdropdown);
  }
});

$(document).on("change", "#input_type", function () {
  var type = $(this).val();
  if (type != "checkbox" && type.length > 0) {
    $("#is-required").removeClass("d-none");
  } else {
    $("#is-required").addClass("d-none");
  }
  switch (type) {
    case "text_field":
      $("#show-text").removeClass("d-none");
      $("#show-dropdown").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "dropdown":
      $("#show-dropdown").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "select":
      $("#show-dropdown-select").removeClass("d-none");
      $("#show-dropdown").addClass("d-none");
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
      $("#show-dropdown").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "date":
      $("#show-date").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-dropdown").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "price":
      $("#show-price").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-dropdown").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-image").addClass("d-none");

      break;
    case "image":
      $("#show-image").removeClass("d-none");
      $("#show-text").addClass("d-none");
      $("#show-multiselect").addClass("d-none");
      $("#show-dropdown").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");
      break;
    default:
      $("#show-text").addClass("d-none");
      $("#show-dropdown-select").addClass("d-none");
      $("#show-dropdown").addClass("d-none");
      $("#show-textarea").addClass("d-none");
      $("#show-date").addClass("d-none");
      $("#show-checkbox").addClass("d-none");
      $("#show-price").addClass("d-none");
      $("#show-image").addClass("d-none");
      break;
  }
});
$(".submit-attribute").on("click", function (e) {
  e.preventDefault();
  var id = $("#attribute_id").val();

  var checkbox = document.getElementById("is_enabled");
  var is_enabled = checkbox.checked ? 1 : 0;

  //var req = document.getElementById("is_required");
  //var is_required = (req.checked) ? 1 : 0;
  var category = [];
  $("#category_id option:selected").each(function () {
    category.push({ category_id: $(this).attr("value") });
  });

  var entity = [];
  var input_type = $("#input_type").val();
  switch (input_type) {
    case "text_field":
      entity.push({
        attribute_entity_id: $("input[name=attribute_entity_id]").val(),
        attribute_entity_default_value: $("input[name=textfield]").val(),
        attribute_entity_value: $("input[name=attribute_code]").val(),
        attribute_entity_name: $("input[name=attribute_name]").val(),
        attribute_entity_default_value: $("input[name=textfield]").val(),
      });
      break;
    case "dropdown":
      $("#dynamic-dropdown .card-body .row").each(function () {
        var checkbox = $(this).find(".is_default");
        var isDefault = checkbox.prop("checked");
        entity.push({
          attribute_entity_id: $("input[name=attribute_entity_id]").val(),
          attribute_entity_value: $(this).find("input[name=option]").val(),
          attribute_entity_name: $(this).find("input[name=option]").val(),
          attribute_entity_position: $(this).find("input[name=position]").val(),
          attribute_entity_description: $(this).find("#description").val(),
          is_default: isDefault == true ? 1 : 0,
        });
      });
      break;

    case "select":
      $("#dynamic-table tbody tr").each(function () {
        entity.push({
          attribute_entity_id: $("input[name=attribute_entity_id]").val(),
          attribute_entity_value: $(this).find("input[name=opt]").val(),
          attribute_entity_name: $(this).find("input[name=opt]").val(),
          attribute_entity_position: $(this).find("input[name=posts]").val(),
          attribute_entity_description: $(this).find("#description").val(),
        });
      });
      break;
    case "textarea":
      entity.push({
        attribute_entity_id: $("input[name=attribute_entity_id]").val(),
        attribute_entity_value: $("input[name=attribute_code]").val(),
        attribute_entity_name: $("input[name=attribute_name]").val(),
        attribute_entity_default_value: $("#textarea").val(),
      });
      break;
    case "date":
      entity.push({
        attribute_entity_id: $("input[name=attribute_entity_id]").val(),
        attribute_entity_value: $("input[name=attribute_code]").val(),
        attribute_entity_name: $("input[name=attribute_name]").val(),
        attribute_entity_default_value: $("input[name=date]").val(),
      });
      break;
    case "price":
      entity.push({
        attribute_entity_id: $("input[name=attribute_entity_id]").val(),
        attribute_entity_value: $("input[name=attribute_code]").val(),
        attribute_entity_name: $("input[name=attribute_name]").val(),
        attribute_entity_default_value: $("input[name=price]").val(),
      });
      break;
  }

  var form_data = new FormData(document.getElementById("form-attribute"));
  form_data.append("_token", $("input[name=_token]").val());
  form_data.append("is_enabled", is_enabled);
  // form_data.append("is_required", is_required);
  form_data.append("category_attribute", JSON.stringify(category));
  form_data.append("entity", JSON.stringify(entity));

  $.ajax({
    url: BASEURL + "maintenance/attributes",
    method: "POST",
    data: form_data,
    processData: false,
    dataType: "json",
    contentType: false,
    beforeSend: function () {
      $("#error-handling ul").html("");
      $("#error-handling").addClass("d-none");
      $("#preloading").modal("show");
    },
    success: function (data) {
      if ($.isEmptyObject(data.error)) {
        if (data.success == true) {
          setTimeout(function () {
            window.location =
              BASEURL + "maintenance/attributes/" + data.id + "/edit";
          }, 300);
          toastr.success(data.message);
        } else {
          toastr.error(data.message, "Error on saving");
        }
      } else {
        $("#error-handling").removeClass("d-none");

        $.each(data.error, function (prefix, val) {
          $("#error-handling ul").append("<li>" + val + "</li>");
        });
      }
    },
    complete: function () {
      $("#preloading").modal("hide");
    },
  });
});
