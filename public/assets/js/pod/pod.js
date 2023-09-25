$(document).ready(function () {
    $(".select").select2();

     Dropzone.options.imageUploadModal = {
        paramName: "attachment",
        maxFilesize: 2,
        acceptedFiles: "image/*",
        init: function() {
            this.on("success", function(file, response) {
                // Handle successful upload
                console.log(response);
            });
            this.on("error", function(file, errorMessage) {
                // Handle upload errors
                console.error(errorMessage);
            });
        }
    };
});

$(document).on('click', '.add-attachment', function (e) {
    $("#imageUploadModal").modal('show');
});

$(document).on('click', '.submit-pod', function (e) {
    e.preventDefault();
    var form_data = new FormData(document.getElementById("submit-pod"));
    form_data.append("_token", $('input[name=_token]').val());
    $.ajax({
        url: BASEURL + 'pod',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-pod').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'pod/'+data.id+'/edit';
						}, 300);
                    } else {
                        localStorage.clear();
                        toastr.success(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'pod';
						}, 300);
                    }
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-pod').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
});

$(document).on('click','#upload',function(e){
    e.preventDefault(); // Prevent the default form submission
    // Create a new FormData object
    var form_data = new FormData(document.getElementById("image-upload-form"));
    form_data.append("_token", $('input[name=_token]').val());
    var dropzone = Dropzone.forElement('#image-upload-form');
        dropzone.files.forEach(function (file) {
            console.log(file);
            form_data.append('attachment[]', file, file.name);
        });
    $.ajax({
        type: 'POST',
        url: BASEURL + 'upload-attachment', // Get the form's action URL from the form's "action" attribute
        data: form_data,
        dataType: 'json',
        processData: false, // Prevent jQuery from processing the data
        contentType: false, // Prevent jQuery from setting the content type
        success: function(response) {
            toastr.success(response.success);
            $("#imageUploadModal").modal('hide');
        },
        error: function(xhr, status, error) {
            // Handle any errors
            toastr.error(xhr.responseText);
        }
    });
});

