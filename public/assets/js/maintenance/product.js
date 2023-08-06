$(document).ready(function () {
    $(".select2").select2();
    if ($("#brand").length ) {
        var category_id = $("#category_id" ).val();
        var brand_id = $("#brand").val();
        populateBrand(category_id, brand_id);
    }

});

$(document).on('change', '#category_id', function() {
    var category_id = $(this).val();
    populateBrand(category_id,'');
});

$(document).on('click', '.submit-product', function (e) {
    e.preventDefault();
    var id = $('#product_id').val();

    var checkbox = document.getElementById("is_enabled");
    var is_enabled = (checkbox.checked) ? 1 : 0;

    var serial = document.getElementById("is_serialize");
    var is_serialize = (serial.checked) ? 1 : 0;

    var form_data = new FormData(document.getElementById("form-product"));
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

