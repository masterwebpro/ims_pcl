$(document).ready(function () {
    $(".select").select2();
});
  $(document).on('click', '.submit-trucker', function (e) {
    e.preventDefault();
    var checkbox = document.getElementById("is_enabled");
    if(checkbox){
     var is_enabled = checkbox.checked ? 1 : 0;
    }
    var form_data = new FormData(document.getElementById("submit-trucker"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("is_enabled", is_enabled);

    $.ajax({
        url: BASEURL + 'maintenance/trucker',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-withdrawal').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'maintenance/trucker/'+data.id+'/edit';
						}, 300);
                    } else {
                        localStorage.clear();
                        toastr.success(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'maintenance/trucker';
						}, 300);
                    }
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-trucker').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
});
