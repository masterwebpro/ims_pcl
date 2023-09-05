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
                    // Swal.fire({
                    //     title: data.message,
                    //     text: "You want to add more location?",
                    //     icon: 'question',
                    //     showCancelButton: true,
                    //     confirmButtonColor: '#3085d6',
                    //     cancelButtonColor: '#d33',
                    //     confirmButtonText: 'Yes, Add more!'
                    //   }).then((result) => {
                    //     if (result.isConfirmed) {
                    //        $('#rack').val('');
                    //        $('#level').val('');
                    //        $('#rack').focus();
                    //     } else {
                    //         $('#preloading').modal('show');
                    //         setTimeout(function () {
                    //             window.location = BASEURL+'maintenance/location';
                    //         }, 300);
                    //     }
                    // });
                } else {
                    toastr.error(data.message,'Error on saving'); 
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#form-location').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });

});


/* This event will fire on 'Add New Row' button click */
$(document).on('click', '#add-row', function (e) {
    addNewRow();        
});
 
/* This event will fire on 'Delete Row' button click */
$(document).on('click', '#remove-row', function (e) {
    var table = $('#location-table')[0];
    var rowCount = table.rows.length;
    console.log(rowCount);
    if(rowCount <= 2){
        alert("Unable to delete, Must have atleast one row.");
        return;
    }

    deleteRow();  

      
});
 
 /* This method will add a new row */
function addNewRow(){
    var table = $('#location-table')[0];
    var rowCnt = table.rows.length;
    rowCount = rowCnt - 1;

    var rowHtml='<tr>'
            +'<td>'
                +'<input type="text" class="form-control" name="rack[]" id="rack" value="" placeholder="Enter Rack">'
                +'<span class="text-danger error-msg rack'+rowCount+'_error"></span>'
            +'</td>'
            +'<td>'
                +'<input type="text" class="form-control" name="level[]" id="level" value="" placeholder="Enter Level">'
                +'<span class="text-danger error-msg level'+rowCount+'_error"></span>'
            +'</td>'
            +'<td>'
                +'<input type="number" class="form-control" name="row[]" id="row" value="" placeholder="Enter Row No.">'
                +'<span class="text-danger error-msg row'+rowCount+'_error"></span>'
            +'</td>'
        +'</tr>';
         
    $("#location-table tbody").append(rowHtml);
}
 
/* This method will delete a row */
function deleteRow(ele){
    var table = $('#location-table')[0];
    var rowCount = table.rows.length;
    if(rowCount <= 1){
        alert("There is no row available to delete!");
        return;
    }
    if(ele){
        //delete specific row
        $(ele).parent().parent().remove();
    }
    else{
        //delete last row
        table.deleteRow(rowCount-1);
    }
}