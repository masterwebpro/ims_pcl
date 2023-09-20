$(document).on('click', '.submit-supplier', function (e) {
    e.preventDefault();
    var id = $('#id').val();
    var client = $('#client').val();
    var address = document.getElementById("address").value;

    var form_data = new FormData(document.getElementById("form-supplier"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("supplier_address", address);
    form_data.append("client", JSON.stringify(client));

    $.ajax({
        url: BASEURL + 'maintenance/supplier',
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
                        window.location= BASEURL + 'maintenance/supplier/'+data.id+'/edit';
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
