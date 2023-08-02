$(document).on('click', '.submit-brand', function (e) {
    e.preventDefault();
    var id = $('#brand_id').val();

    var selectedCategory = $('#category_id').val();

    var checkbox = document.getElementById("is_enabled");
    var is_enabled = (checkbox.checked) ? 1 : 0;

    var form_data = new FormData(document.getElementById("form-brand"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("is_enabled", is_enabled);
    form_data.append("category", JSON.stringify(selectedCategory));

    $.ajax({
        url: BASEURL + 'maintenance/brand',
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
                        window.location= BASEURL + 'maintenance/brand/'+data.id+'/edit';
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
