$(document).ready(function () {
    $(".select2").select2();

    if ( $( "#store_id" ).length ) {
        client_id = $("#client_id" ).val();
        store_id = $("#store_id" ).val();
        populateStore(client_id, store_id);
    }

    $('#show-items-list tbody').on('click', 'tr', function (e) {
        $('#show-items-list tbody tr').removeClass('selected')
        $(this).addClass('selected');
    });

});

$(document).on('change', '#client', function() {
    var client_id = $(this).val();
    populateStore(client_id, '');
});

$(document).on('click', '.submit-warehouse', function (e) {
    e.preventDefault();
    var id = $('#id').val();

    var checkbox = document.getElementById("is_enabled");
    var is_enabled = (checkbox.checked) ? 1 : 0;

    var form_data = new FormData(document.getElementById("form-warehouse"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("is_enabled", is_enabled);

    $.ajax({
        url: BASEURL + 'maintenance/warehouse',
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
                    console.log(data.data)
                    setTimeout(function(){
                        window.location= BASEURL + 'maintenance/warehouse/'+data.id+'/edit';
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
