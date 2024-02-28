$(document).ready(function () {
    $(".select2").select2();

    $(".select3").select2({
        dropdownParent: $("#show-items")
    });

    if ( $( "#store_id" ).length ) {
        company_id = $("#source_company" ).val();
        store_id = $("#store_id" ).val();
        populateStore(company_id, store_id, 'source_site');
    }

    var ref_no = $('#ref_no').val();

    if(ref_no) {
        $("table#product-list").find('.destWarehouseId').each(function () {
            warehouse_id = $(this).val();
            var id = $(this).data('id');
            location_id = $("#location_"+id ).val();
            populateLocation('dest_location_'+id, warehouse_id, location_id);
        });    
    }

    $('#show-items-list tbody').on('click', 'tr', function (e) {
        //$('#show-items-list tbody tr').removeClass('selected')
        //$(this).addClass('selected');
        $(this).toggleClass('selected');
    });
});

$(document).on('change', '#source_company', function() {
    var company_id = $(this).val();
    populateStore(company_id, '', 'source_site');
    $('#source_warehouse').find('option').not(':first').remove();
});

$(document).on('change', '#source_site', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '', 'source_warehouse');
});

$(document).on('change', '#source_warehouse', function() {
    var warehouse_id = $(this).val();
    populateLocation('source_location', warehouse_id, '') 
});

$(document).on('change', '#dest_company', function() {
    var company_id = $(this).val();
    populateStore(company_id, '', 'dest_site');
});


$(document).on('click', '.add-item', function() {

    var source_site = $('#source_site').val();

    if(source_site) {
        $('#show-items').modal('show'); 
        if ($.fn.DataTable.isDataTable("#show-items-list")) {
            $('#show-items-list').DataTable().clear().destroy();
        }
    } else {
        showError("Please select source site.");
    }
    
});


$(document).on('click', '.search-item', function() {

    var warehouse_id = $('#source_warehouse').val();
    var store_id = $('#source_store').val();
    var company_id = $('#source_company').val();
    var source_location = $('#source_location').val();
    var product_name = $('#product_name').val();
    
    if(warehouse_id) {   

        $('#show-items').modal('show'); 
        $('#show-items-list').DataTable().clear().destroy();
        
        if ($.fn.DataTable.isDataTable("#show-items-list")) {
            $('#show-items-list').DataTable().clear().destroy();
        }

        new DataTable("#show-items-list",{
            order: [[2, 'asc'], [3, 'asc']],
            searching: false,
            paging: true,
            ajax: {
                type: 'GET',
                url: BASEURL+"settings/masterfile",
                data: {
                    location : source_location,
                    store_id: store_id,
                    client_id: company_id,
                    product_name : product_name,
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
                { data: 'rcv_dtl_id', visible: false},
                
            ],
            "pageLength": 25,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ]
        });
     
    } else {
        alert("Warehouse ID required");
    }
});

$(document).on('click', '#add-product', function() {
    var table = $('#show-items-list').DataTable();
    var data = (table.rows('.selected').data());
    var warehouse_id = $('#warehouse_id').val();
    var source_site = $('#source_site').val();
    var source_warehouse =  $('#source_warehouse').val();
 
    if(data.length > 0) {
        for(x=0; x<data.length; x++) {
            // var new_location = getNewLocation(warehouse_id);
            var rowCount = ($('#product-list tr').length) - 1;

            populateWarehouse(source_site, '', 'dest_warehouse_'+(rowCount-1));

            var idx = rowCount - 3;
            var btn = '<div class="text-center">';
            btn += '<a href="javascript:void(0)" class="text-info split-product" data-id="'+(rowCount-1)+'"><i class=" ri-menu-add-line label-icon align-middle rounded-pill fs-16 me-2"></i>Split</a>';
            btn += '&nbsp; <a href="javascript:void(0)" class="text-danger remove-product" data-id="'+(rowCount-1)+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';
            
            btn += '</div>'

            $('#product-list tbody').append('<tr id="product_'+(rowCount-1)+'"> \
            <td class="text-start d-none"> \
                <input type="hidden" name="product_id[]" readonly id="product_id_'+data[x].product_id+'" value="'+data[x].product_id+'" /> \
                <input type="hidden" name="product_name[]" readonly id="product_name_'+data[x].product_name+'" value="'+data[x].product_name+'" /> \
                <input type="hidden" name="product_code[]" readonly id="product_code_'+data[x].product_code+'" value="'+data[x].product_code+'" /> \
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
                '+$( "#source_warehouse option:selected" ).text()+' \
                <input type="hidden" readonly class="form-control" name="source_warehouse[]" id="source_warehouse_'+(rowCount-1)+'" value="'+source_warehouse+'"> \
            </td> \
            <td class="text-center ps-1 fs-13"> \
                <input type="hidden" readonly class="form-control" name="source_location[]" data-id="'+data[x].product_id+'" id="source_location_'+(rowCount-1)+'" value="'+data[x].old_location_id+'"> \
                '+data[x].old_location+' \
            </td> \
            <td class="text-start ps-1 fs-13"> \
                <div class="input-group"  style="width: 140px;"> \
                    <input type="text" readonly class="form-control source_inv_qty input-group-text numeric transter_item" name="source_inv_qty[]" data-id="'+data[x].product_id+'" id="source_inv_qty_'+(rowCount-1)+'" value="'+data[x].inv_qty+'"> \
                    <input type="hidden" readonly class="form-control" name="source_inv_uom[]" data-id="'+data[x].product_id+'" id="source_inv_uom_'+(rowCount-1)+'" value="'+data[x].i_uom_id+'"> \
                    <span class="input-group-text">'+data[x].i_code+'</span> \
                </div> \
                <span class="text-danger error-msg old_inv_qty'+(rowCount-1)+'_error"></span> \
            </td> \
            <td class="text-center ps-1 fs-13"> \
                <select style="width: 150px;" name="dest_warehouse[]" data-id="'+(rowCount-1)+'" id="dest_warehouse_'+(rowCount-1)+'" class="form-select dest_warehouse select2"><option value="">Select Warehouse</option></select> \
                <span class="text-danger error-msg dest_warehouse'+(rowCount-1)+'_error"></span> \
            </td> \
            <td class="text-start ps-1"><select style="width: 100px;" name="dest_location[]" id="dest_location_'+(rowCount-1)+'" class="form-select dest_location select2"><option value="">Select Location</option></select> \
                <span class="text-danger error-msg dest_location'+(rowCount-1)+'_error"></span> \
            </td> \
            <td class="text-start ps-1"> \
                <div class="input-group"  style="width: 140px;"> \
                    <input type="text" class="form-control new_inv_qty numeric transfer_item" name="dest_inv_qty[]" data-id="'+data[x].product_id+'" id="dest_inv_qty_'+(rowCount-1)+'" value="'+data[x].inv_qty+'"> \
                    <input type="hidden" readonly class="form-control" name="dest_inv_uom[]" data-id="'+data[x].product_id+'" id="dest_inv_uom_'+(rowCount-1)+'" value="'+data[x].i_uom_id+'"> \
                    <span class="input-group-text">'+data[x].i_code+'</span> \
                </div> \
                <span class="text-danger error-msg dest_inv_qty'+(rowCount-1)+'_error"></span> \
            </td> \
            <td>'+btn+'</td> \
            </tr>');

            toastr.success(data[x].product_name + ' successfully added');
            // $('.select2').select2();
        }
    }

    $('#show-items-list tbody tr').removeClass('selected')
   
    $('#show-items').modal('hide');
});

$(document).on('change', '.dest_warehouse', function(){
    var id = $(this).data('id');
    var warehouse_id = $(this).val();

    let elem = $(this)

    $.ajax({
        url: BASEURL + 'settings/getLocation',
        method: 'get',
        data: {"warehouse_id": warehouse_id},
        dataType: 'json',
        success: function(response){
			// Remove options
            elem.closest('tr').find('.dest_location').find('option').not(':first').remove();
		    // Add options
            $.each(response,function(index,data){
                elem.closest('tr').find('.dest_location').append('<option value="'+data['storage_location_id']+'">'+data['location']+'</option>');
            });
			elem.closest('tr').find('.dest_location').append('<option value="ra">Receiving Area</option>');

        }, complete: function () {
            elem.closest('tr').find('.dest_location').select2();
        }
    });
});

var cloneCount = 1;
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
});

$(document).on('click', '.submit-open', function (e) {
    e.preventDefault();
   
    var form_data = new FormData(document.getElementById("submit-transfer"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'open');

    _submitData(form_data);
});


$(document).on('click', '.submit-posted', function (e) {
    e.preventDefault();
   
    var form_data = new FormData(document.getElementById("submit-transfer"));
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
        url: BASEURL + 'stock/transfer',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $(".errors").html('');
            $('#submit-transfer').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                console.log(data.data);
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'stock/transfer/'+data.id+'/edit';
						}, 300);
                    } else {
                        toastr.success(data.message); 
                        setTimeout(function () {
							window.location = BASEURL+'stock/transfer';
						}, 300);
                    }
                } else {
                    // alert('test');
                    toastr.error(data.message,'Error on saving'); 

                    if(data.error_msg) {
                        $.each(data.error_msg, function(prefix, val) {
                            $('#errMsg').removeClass('d-none');
                            $(".errors").append('<li>'+val+'</li>');
                        });
                    }
                }
            } else {
                // alert('ert');
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-transfer').find('span.'+prefix.replace('.','')+'_error').text(val);
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
    // $('#product_'+id).remove();
    $(this).closest("tr").remove();
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
                url: BASEURL + 'stock/transfer',
                data: {
                    ref_no : $('#ref_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "DELETE",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-transfer').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location = BASEURL+'stock/transfer';
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
                url: BASEURL + 'stock/transfer/unpost',
                data: {
                    ref_no : $('#ref_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "post",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-transfer').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location = BASEURL+'stock/transfer';
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

