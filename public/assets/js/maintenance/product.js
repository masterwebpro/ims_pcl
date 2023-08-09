$(document).ready(function () {
    var attribute_entity = [];
    var category_id = $("#category_id").val();
    var product_id = $("#product_id").val();
    var entity = getProductAttribute(product_id);
    attribute_entity = entity;
    if(product_id != '')
    {
        var brand_id = $("#brand").data('brand');
        populateBrand(category_id,brand_id);
        attributes(entity);
        console.log(brand_id);
    }
    $(".select2").select2();
    if ($("#brand").length) {
        var brand_id = $("#brand").data('brand');
        populateBrand(category_id, brand_id);
    }
    if ($("#brand").val() !== "" && $("#category_id").val() !== "" && $("#brand").val() &&  $("#product_code").val() !== "" && $("#product_name").val() !== ""){
        $("#steparrow-attributes").prop("disabled", false);
    }

    $('#category_id').on('change', function() {
        validate_general();
        var category_id = $(this).val();
        populateBrand(category_id,'');
        var entity = getCategoryAttribute(category_id);
        if(entity.length){
            attribute_entity = entity;
            attributes(entity);
            $("#steparrow-pricing").prop("disabled", false);
        }
    });

    $(".submit-product").on('click', function (e) {
        e.preventDefault();
        var id = $('#product_id').val();
        if($('#product_id').length)
        {
            var entity = getProductAttribute(id);
            attribute_entity = entity;
        }
        else{
            attribute_entity = getCategoryAttribute($('#category_id').val());
        }
        var selecteduom = $('#uom_id').val();

        if(selecteduom.length == 0)
        {
            alert("Uom is required");
            return false;
        }

        var checkbox = document.getElementById("is_enabled");
        var is_enabled = (checkbox.checked) ? 1 : 0;

        var serial = document.getElementById("is_serialize");
        var is_serialize = (serial.checked) ? 1 : 0;

        var form_data = new FormData(document.getElementById("form-product"));
        form_data.append("_token", $('input[name=_token]').val());
        form_data.append("is_enabled", is_enabled);
        form_data.append("is_serialize", is_serialize);
        form_data.append("uom_id", JSON.stringify(selecteduom));
        form_data.append("attribute_entity", JSON.stringify(attribute_entity));

        $.ajax({
            url: BASEURL + 'maintenance/product',
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
                        setTimeout(function(){
                            window.location= BASEURL + 'maintenance/product/'+data.id+'/edit';
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

$(document).on('click','#steparrow-attributes',function(){
    validate_general();
});



function attributes(entity){
    $("#attributes").empty();
    var list = `<div class="col-md-6"><ul class="list-group">`;
    entity.forEach(function(val){
        list += `<li class="list-group-item">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="d-flex">
                        <div class="flex-shrink-0 avatar-xs">
                            <i class="ri-file-list-line"></i>
                        </div>
                        <div class="flex-shrink-0 ms-2">
                            <h4 class="fs-14 mb-0">`+ ((val['attribute_name'])) +`</h4>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="form-check form-switch-md form-switch form-switch-success">
                        <input class="form-check-input" type="checkbox" role="switch" id="SwitchCheck3" checked>
                        <label class="form-check-label" for="SwitchCheck3">Enable</label>
                    </div>
                </div>
            </div>
        </li>`;
    })
    list += `</ul></div>`;
    $("#attributes").append(list);
}

$(document).on('change', '#supplier_id', function() {
    validate_general();
});

$(document).on('change', '#product_code', function() {
    validate_general();
});

$(document).on('change', '#product_name', function() {
    validate_general();
});

$(document).on('change', '#brand', function() {
    var category_id = $(this).val();
    validate_general();
});

$("#steparrow-pricing").on('click',function(){
    $("#steparrow-pricing-info-tab").prop("disabled", false);
});

$("#steparrow-units").on('click',function(){
    if ($("#msrp").val() && $("#supplier_price").val() && $("#product_srp").val() && $("#special_price").val()) {
        $("#steparrow-units-info-tab").prop("disabled", false);
    } else {
        alert("Please fill in all required fields.");
    }
});

function validate_general(){
    if ($("#brand").val() !== "" && $("#category_id").val() !== "" && $("#brand").val() &&  $("#product_code").val() !== "" && $("#product_name").val() !== ""){
        $("#steparrow-attributes").prop("disabled", false);
        $("#steparrow-attributes-info-tab").prop("disabled", false);
    }
    else{
        $("#steparrow-attributes").prop("disabled", true);
        $("#steparrow-attributes-info-tab").prop("disabled", true);
        $("#steparrow-pricing-info-tab").prop("disabled", true);
        $("#steparrow-units-info-tab").prop("disabled", true);

    }
}

$(document).on('change', '#uom_id', function() {
    var uom_id = $(this).val();
    if(uom_id != '')
    {
        $('.submit-product').prop("disabled",false);
    }
});




