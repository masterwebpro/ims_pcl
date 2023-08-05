$(document).ready(function () {
    $(".select2").select2();

    if ( $( "#store_id" ).length ) {
        client_id = $("#client_id" ).val();
        store_id = $("#store_id" ).val();
        warehouse_id = $("#warehouse_id" ).val();
        populateStore(client_id, store_id);
        populateWarehouse(store_id, warehouse_id);
    }

});

$(document).on('change', '#client', function() {
    var client_id = $(this).val();
    populateStore(client_id, '');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '');
});

$(document).on('click', '.submit-location', function (e) {
    e.preventDefault();
    var form_data = new FormData(document.getElementById("form-location"));
    $.ajax({
        url: BASEURL + 'maintenance/location',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#form-location').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    Swal.fire({
                        title: data.message,
                        text: "You want to add more location?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Add more!'
                      }).then((result) => {
                        if (result.isConfirmed) {
                           $('#rack').val('');
                           $('#level').val('');
                           $('#rack').focus();
                        } else {
                            $('#preloading').modal('show');
                            setTimeout(function () {
                                window.location = BASEURL+'maintenance/location';
                            }, 800);
                        }
                    });
                } else {
                    toastr.error(data.message,'Error on saving'); 
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#form-location').find('span.'+prefix+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });

});