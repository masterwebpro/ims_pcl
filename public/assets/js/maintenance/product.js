$(document).ready(function () {

    $(".select2").select2();

    var attribute_entity = [];
    var category_id = $("#category_id").val();
    var product_id = $("#product_id").val();
    var productAttributes = getProductAttribute(product_id);
    attribute_entity = productAttributes;
    if(product_id != '')
    {
        var brand_id = $("#brand").data('brand');
        populateBrand(category_id,brand_id);
        var categoryAttribute = getCategoryAttribute(category_id);
        if(attribute_entity.length)
        {
            var enabledMap = {};
            attribute_entity.forEach(function(item) {
                enabledMap[item.attribute_id] = 1;
            });

            var combinedArray = categoryAttribute.map(function(attribute) {
                return {
                    "attribute_id": attribute['attribute_id'],
                    "attribute_code": attribute['attribute_code'],
                    "attribute_name": attribute['attribute_name'],
                    "attribute_input_type": attribute['attribute_input_type'],
                    "is_enabled": enabledMap[attribute.attribute_id] || 0
                };
            });
            attribute_entity = combinedArray;
            attributes(combinedArray);
        }
    }
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
            $("#steparrow-pricing-info-tab").prop("disabled", false);
            $("#steparrow-pricing").prop("disabled", false);
            attributes(entity);
        }
    });

    $(".submit-product").on('click', function (e) {
        e.preventDefault();
        var id = $('#product_id').val();

        var listItems = $('.list-group-item');
        var values = [];
        attribute_entity = [];
        listItems.each(function() {
            var attribute_id = $(this).find('#attribute_id').val();
            var attribute_code = $(this).find('#attribute_code').val();
            var is_enabled = $(this).find('#is_enabled_attr').is(':checked');
            if(is_enabled == true){
                values.push({
                    'attribute_id': attribute_id,
                    'attribute_code': attribute_code,
                    'is_enabled': 1
                });
            }
        });
        attribute_entity = values;

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

    $(document).on('click','#steparrow-attributes',function(){
        validate_general();
    });

    function attributes(entity){
        $("#attributes").empty();
        var list = `<div class="col-md-6"><ul class="list-group" id="attribute_list">`;
        entity.forEach(function(val){
            var ischecked = (val['is_enabled'] == 1) ? 'checked' : '';
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
                            <input type="hidden" id="attribute_id" name="attribute_id" value="` + val['attribute_id']+ `">
                            <input type="hidden" id="attribute_code" name="attribute_code" value="` + val['attribute_code']+ `">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_enabled_attr" `+ ischecked +`>
                            <label class="form-check-label" for="is_enabled_attr">Enable</label>
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

    $(document).on('click','#steparrow-units',function(){
        $("#steparrow-unit-info-tab").prop('disabled',false);
        const nextTab = $(this).data("nexttab");
        $(`#${nextTab}`).click();
      
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

});





