$(document).ready(function () {
    $(".select2").select2({
        dropdownParent: $("#movement-form")
    });

    $(".select3").select2({
        dropdownParent: $("#show-items")
    });



    //$(".select2").select2();

    if ( $( "#store_id" ).length ) {
        company_id = $("#company_id" ).val();
        store_id = $("#store_id" ).val();
        populateStore(company_id, store_id, 'store');
        populateWarehouse(store_id, '', 'warehouse');
    }

    if ( $( "#warehouse_id" ).length ) {
        warehouse_id = $("#warehouse_id" ).val();
        store_id = $("#store_id" ).val();
        populateWarehouse(store_id, warehouse_id, 'warehouse');
    }

    $('#show-items-list tbody').on('click', 'tr', function (e) {
        //$('#show-items-list tbody tr').removeClass('selected')
        //$(this).addClass('selected');
        $(this).toggleClass('selected');
    });
});

$(document).on('click', '.create-movement', function() {
   $('#show-form').modal('show');
});

$(document).on('change', '#company', function() {
    var company_id = $(this).val();
    populateStore(company_id, '', 'store');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '', 'warehouse');
});

$(document).on('change', '#rack', function() {
    var rack_id = $(this).val();
    var warehouse_id = $('#warehouse_id').val();
    populateLevel(warehouse_id, rack_id, '');
});

$(document).on('click', '#movement-next-btn', function() {
    var store = $('#store').val();
    var company = $('#company').val();
    var warehouse = $('#warehouse').val();

    $.ajax({
        url: BASEURL + 'stock/movement/validate',
        method: "POST",
        data: {
            store : store,
            company : company,
            warehouse : warehouse,
            _token : $('input[name=_token]').val()
        },
        dataType: 'json',
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#show-form').find('span.error-msg').text('');
        },
        success: function (response) {
            if($.isEmptyObject(response.errors)) {
                window.location = BASEURL+'stock/movement/create';

            } else {
                $.each(response.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#show-form').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });

});

$(document).on('click', '#find-items', function() {

    $('#show-items').modal('show');
    if ($.fn.DataTable.isDataTable("#show-items-list")) {
        $('#show-items-list').DataTable().clear().destroy();
    }
});

$(document).on('click', '.search-item', function() {

    var warehouse_id = $('#warehouse_id').val();
    var store_id = $('#store_id').val();
    var company_id = $('#company_id').val();
    var location = $('#location').val();
    var rcv_no = $('#rcv_no').val();
    //var storage_location_id = $('#storage_location_id').val();


    if(warehouse_id) {

        $('#show-items').modal('show');
        $('#show-items-list').DataTable().clear().destroy();

        if ($.fn.DataTable.isDataTable("#show-items-list")) {
            $('#show-items-list').DataTable().clear().destroy();
        }
        $.ajax({
            url: BASEURL + 'settings/getStorageLocationId',
            method: "get",
            data: {
                location : location,
                warehouse_id : warehouse_id,
                _token : $('input[name=_token]').val()
            },
            dataType: 'json',
            beforeSend: function () {
                // $('#preloading').modal('show');
                $('#show-form').find('span.error-msg').text('');
            },
            success: function (response) {
                if($.isEmptyObject(response.errors)) {

                    let storage_id = response.map(x => x.storage_location_id).join(", ");

                    //console.log(storage_id+" - "+store_id+" - "+company_id+" - "+rcv_no);

                    if ($.fn.DataTable.isDataTable("#show-items-list")) {
                        $('#show-items-list').DataTable().clear().destroy();
                    }

                    new DataTable("#show-items-list",{
                        order: [[1, 'desc']],
                        paging: true,
                        ajax: {
                            type: 'GET',
                            url: BASEURL+"settings/masterfile",
                            data: {
                                storage_id: storage_id,
                                location : location,
                                store_id: store_id,
                                client_id: company_id,
                                rcv_no : rcv_no,
                                warehouse_id:warehouse_id
                            },
                            dataSrc:""
                        },
                        columns: [
                            { data: 'product_id', visible: false },
                            { data: 'product_code' },
                            { data: 'product_name' },
                            { data: 'old_location' },
                            { data: 'item_type' },
                            { data: 'inv_qty' },
                            { data: 'i_code' },
                            { data: 'whse_qty' },
                            { data: 'w_code' },
                            { data: 'rcv_dtl_id' },

                        ],
                        "pageLength": 50,
                        lengthMenu: [
                            [10, 25, 50, -1],
                            [10, 25, 50, 'All']
                        ]
                    });
                } else {
                    $.each(response.errors, function(prefix, val) {
                        $('#errMsg').removeClass('d-none');
                        $('#show-form').find('span.'+prefix.replace('.','')+'_error').text(val);
                    });
                    toastr.error('Some fields are required');
                }
            },
            complete: function() {
               $('#preloading').modal('hide');
            }
        });
    } else {
        alert("Supplier Name required");
    }
});

$(document).on('click', '#add-product', function() {
    var table = $('#show-items-list').DataTable();
    var data = (table.rows('.selected').data());
    var warehouse_id = $('#warehouse_id').val();

    if(data.length > 0) {
        for(x=0; x<data.length; x++) {
            var new_location = getNewLocation(warehouse_id);
            var rowCount = ($('#product-list tr').length) - 1;
            var idx = rowCount - 3;
            var btn = '<div class="text-center">';
            btn += '<a href="javascript:void(0)" class="text-info split-product" data-id="'+(rowCount-1)+'"><i class=" ri-menu-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>Split</a>';
            btn += '&nbsp; <a href="javascript:void(0)" class="text-danger remove-product" data-id="'+(rowCount-1)+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';

            btn += '</div>'

            $('#product-list tbody').append('<tr id="product_'+(rowCount-1)+'"> \
            <td class="text-start"> \
                <input type="hidden" name="product_id[]" readonly id="product_id_'+data[x].product_id+'" value="'+data[x].product_id+'" /> \
                <input type="hidden" name="rcv_dtl_id[]" readonly id="rcv_dtl_id_'+data[x].rcv_dtl_id+'" value="'+data[x].rcv_dtl_id+'" /> \
            '+rowCount+' </td> \
            <td class="text-start  fs-13"> \
                '+data[x].product_name+'<br/><small>'+data[x].product_code+'</small> \
            </td> \
            <td class="text-center ps-1 fs-13"> \
                '+data[x].item_type+' \
                <input type="hidden" readonly class="form-control" name="item_type[]" data-id="'+data[x].item_type+'" id="item_type_'+(rowCount-1)+'" value="'+data[x].item_type+'"> \
            </td> \
            <td class="text-center ps-1 fs-13"> \
                <input type="hidden" readonly class="form-control" name="old_location[]" data-id="'+data[x].product_id+'" id="old_location_'+(rowCount-1)+'" value="'+data[x].old_location_id+'"> \
                '+data[x].old_location+' \
            </td> \
            <td class="text-start ps-1 fs-13"> \
                <div class="input-group"  style="width: 140px;"> \
                    <input type="text" readonly class="form-control old_inv_qty input-group-text numeric movement_item" name="old_inv_qty[]" data-id="'+data[x].product_id+'" id="old_inv_qty_'+(rowCount-1)+'" value="'+data[x].inv_qty+'"> \
                    <input type="hidden" readonly class="form-control" name="old_inv_uom[]" data-id="'+data[x].product_id+'" id="old_inv_uom_'+(rowCount-1)+'" value="'+data[x].i_uom_id+'"> \
                    <span class="input-group-text">'+data[x].i_code+'</span> \
                </div> \
                <span class="text-danger error-msg old_inv_qty'+(rowCount-1)+'_error"></span> \
            </td> \
            <td class="text-start ps-1"><select style="width: 100px;" name="new_location[]" id="new_location_'+(rowCount-1)+'" class="form-select select2">'+new_location+'</select> \
                <span class="text-danger error-msg new_location'+(rowCount-1)+'_error"></span> \
            </td> \
            <td class="text-start ps-1"> \
                <div class="input-group"  style="width: 140px;"> \
                    <input type="text" class="form-control new_inv_qty numeric movement_item" name="new_inv_qty[]" data-id="'+data[x].product_id+'" id="new_inv_qty_'+(rowCount-1)+'" value="'+data[x].inv_qty+'"> \
                    <input type="hidden" readonly class="form-control" name="new_inv_uom[]" data-id="'+data[x].product_id+'" id="new_inv_uom_'+(rowCount-1)+'" value="'+data[x].i_uom_id+'"> \
                    <span class="input-group-text">'+data[x].i_code+'</span> \
                </div> \
                <span class="text-danger error-msg new_inv_qty'+(rowCount-1)+'_error"></span> \
            </td> \
            <td>'+btn+'</td> \
            </tr>');

            toastr.success(data[x].product_name + ' successfully added');
        }
    }

    $('#show-items-list tbody tr').removeClass('selected')

    $('#show-items').modal('hide');
});

$(document).on('click', '.submit-open', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-receive"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'open');

    _submitData(form_data);
});

$(document).on('click', '.submit-posted', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-receive"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'posted');

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to POST this transaction?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Post it!'
      }).then((result) => {
        if (result.isConfirmed) {
            _submitData(form_data);
        }
    });
});

function _submitData(form_data) {
    $.ajax({
        url: BASEURL + 'stock/movement',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-receive').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'stock/movement/'+data.id+'/edit';
						}, 300);
                    } else {
                        toastr.success(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'stock/movement';
						}, 300);
                    }
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-receive').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
}

$(document).on('click', '.remove-product', function() {
    var id = $(this).data('id');
    $('#product_'+id).remove();
});

$(document).on('blur', '.new_inv_qty', function() {
    var id = $(this).data('id');

    var old_inv = $('#old_inv_qty_'+id).val()
    var new_inv = $(this).val();

    if(old_inv < new_inv) {
        $('.add_btn'+id).prop("disabled", false);
        prefix = 'new_inv_qty'+id;
        $('#submit-receive').find('span.'+prefix.replace('.','')+'_error').text('Insufficient Qty');
    }
});

$(document).on('blur', '#item_code', function(e) {
    var val = $(this).val();
    scanItem(val);
});

$(document).on('keyup', '#item_code', function(e) {
    var val = $(this).val();
    if (e.keyCode === 13)  {
        scanItem(val);
    }
});

function scanItem(val) {
    console.log(val)
}

// $(document).on('click', '.split-product', function(e) {
//     e.preventDefault();
//     var thisRow = $( this ).closest( 'tr' )[0];
//     $( thisRow ).clone().insertAfter( thisRow ).find( '.new_inv_qty' ).val('');
// });

$(document).on('click', '.split-product', function(e) {
    e.preventDefault();
    var id=$(this).data('id');

    var thisRow = $( this ).closest( 'tr' )[0];
    value = $(thisRow).find( '.new_inv_qty' ).val();
    var rem  = value % 2;
    var parent_val = (value / 2);
    var second_val = (value / 2);

    if(rem != 0 ) {
        parent_val = (value / 2) + (rem/2);
        second_val = (value / 2) - (rem/2);
    }

    $(thisRow).find( '.new_inv_qty' ).val(parent_val);
    $( thisRow ).clone().insertAfter( thisRow )
        .find( '.new_inv_qty' ).val(second_val);
    // $( thisRow ).clone().insertAfter( thisRow );
});



$(document).on('click', '.submit-unpost', function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to UNPOST this transaction?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, UNPOST it!'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASEURL + 'stock/movement/unpost',
                data: {
                    ref_no : $('#ref_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "post",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-movement').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location = BASEURL+'stock/movement';
                            }, 300);

                        } else {
                            toastr.error(data.message,'Error on saving');

                        }
                    } else {
                        toastr.error('Some fields are required');
                    }
                },
                complete: function() {
                   $('#preloading').modal('hide');
                }
            });
        }
    });
});

$(document).on('click', '.submit-delete', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to DELETE this transaction?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, DELETE it!'
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASEURL + 'stock/movement',
                data: {
                    ref_no : $('#ref_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "DELETE",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-movement').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location = BASEURL+'stock/movement';
                            }, 300);

                        } else {
                            toastr.error(data.message,'Error on saving');

                        }
                    } else {
                        toastr.error('Some fields are required');
                    }
                },
                complete: function() {
                   $('#preloading').modal('hide');
                }
            });
        }
    });
});

$(document).on('click', '#select-all', function() {
    toggleSelectAllRows();
});

function toggleSelectAllRows() {
    var $table = $('#show-items-list').DataTable();
    var rows = $table.rows().nodes(); // Get the DOM nodes of the rows

    var rowsArray = Array.from(rows);

    // Check if any rows are selected
    var anySelected = false;
    rowsArray.forEach(function(row) {
        if ($(row).hasClass('selected')) {
            anySelected = true;
            return; // break the loop once a selected row is found
        }
    });

    // If any row is selected, deselect all rows
    if (anySelected) {
        rowsArray.forEach(function(row) {
            $(row).removeClass('selected');
        });
        $('#select-all').html('<i class="ri-check-double-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Select All');
    } else {
        // If no row is selected, select all rows
        rowsArray.forEach(function(row) {
            $(row).addClass('selected');
        });
        $('#select-all').html('<i class=" ri-arrow-go-back-fill label-icon align-middle rounded-pill fs-16 me-2"></i> Deselect All');
    }
}

