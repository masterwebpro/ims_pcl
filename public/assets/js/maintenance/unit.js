$(document).on('click', '.submit-uom', function (e) {
    e.preventDefault();
    var id = $('#uom_id').val();

    var form_data = new FormData(document.getElementById("form-uom"));
    form_data.append("_token", $('input[name=_token]').val());

    $.ajax({
        url: BASEURL + 'maintenance/unit',
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
                        window.location= BASEURL + 'maintenance/unit/'+data.id+'/edit';
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
