$(document).on('click','#uploadBegInv', function (e) {
    e.preventDefault();
    var formData = new FormData($('#uploadForm')[0]);
    $.ajax({
        url: BASEURL + 'settings/uploadBeginningInv',
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
                        location.reload();
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