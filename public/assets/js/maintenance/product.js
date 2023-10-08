$(document).ready(function () {

    $(".select2").select2();

    var category_id = $("#category_id").val();
    var product_id = $("#product_id").val();
    if(product_id != '')
    {
        var brand_id = $("#brand").data('brand');
        populateBrand(category_id,brand_id);
    }
    if ($("#brand").length) {
        var brand_id = $("#brand").data('brand');
        populateBrand(category_id, brand_id);
    }

});

$(document).on('change', '#category_id', function() {
    var category_id = $(this).val();
    var selectedOption = $(this).find(':selected');
    var dataCategory = selectedOption.data('category');
    $("#category").val(dataCategory);
    populateBrand(category_id,'');
});

$(document).on('change', '#brand', function() {
    var category_id = $(this).val();
});

$(document).on('click', '.submit-product', function (e) {
    e.preventDefault();
    var checkbox = document.getElementById("is_enabled");
    var is_enabled = (checkbox.checked) ? 1 : 0;

    var serial = document.getElementById("is_serialize");
    var is_serialize = (serial.checked) ? 1 : 0;

    var form_data = new FormData(document.getElementById("submit-product"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("is_enabled", is_enabled);
    form_data.append("is_serialize", is_serialize);

    $.ajax({
        url: BASEURL + 'maintenance/product',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-product').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    setTimeout(function(){
                        window.location= BASEURL + 'maintenance/product/'+data.id+'/edit';
                    }, 300);
                    toastr.success(data.message);

                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-product').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
            $('#preloading').modal('hide');
        }
    });
});

$(document).on('click','.upload-product', function(){
    $("#show-upload").modal('show');
});

$(document).on('click','#downloadTemplate', function (e) {
    e.preventDefault();

    $.ajax({
        url: BASEURL + 'productTemplate',
        method: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        beforeSend: function () {
            $('#preloading').modal('show');
        },
        success: function (data) {
            var a = document.createElement('a');
            var url = window.URL.createObjectURL(data);
            a.href = url;
            a.download = 'excel_template.xlsx';
            document.body.append(a);
            a.click();
            window.URL.revokeObjectURL(url);
            $('#templateMessage').html('Excel template downloaded successfully.');
        },
        error: function (error) {
            $('#templateMessage').html('Error downloading Excel template.');
        },
        complete: function() {
            $('#preloading').modal('hide');
        }
    });
});
$(document).on('click','#uploadProduct', function (e) {
    e.preventDefault();
    var formData = new FormData($('#uploadForm')[0]);
    $.ajax({
        url: BASEURL + 'uploadProduct',
        method: 'POST',
        data: formData,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    setTimeout(function(){
                        window.location= BASEURL + 'maintenance/product';
                    }, 300);
                    toastr.success(data.message);

                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#uploadForm').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Error on uploading');
            }
        },
        error: function (error) {
            toastr.error('Error on uploading');
        },
        complete: function() {
            $('#preloading').modal('hide');
        }
    });
});






