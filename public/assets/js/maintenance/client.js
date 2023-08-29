$(document).on('click', '.submit-client', function (e) {
    e.preventDefault();
    var id = $('#id').val();

    var checkbox = document.getElementById("is_enabled");
    var is_enabled = (checkbox.checked) ? 1 : 0;

    var vatable = document.getElementById("is_vatable");
    var is_vatable = (vatable.checked) ? 1 : 0;

    var form_data = new FormData(document.getElementById("form-client"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("is_enabled", is_enabled);
    form_data.append("is_vatable", is_vatable);


    $.ajax({
        url: BASEURL + 'maintenance/client',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#form-client').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    console.log(data.data)
                    setTimeout(function(){
                        window.location= BASEURL + 'maintenance/client/'+data.id+'/edit';
                    }, 300);
                    toastr.success(data.message);

                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#form-client').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
			$('#preloading').modal('hide');
		}
    });
});
