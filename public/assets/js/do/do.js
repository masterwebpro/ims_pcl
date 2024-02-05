// create a program for odd even number

$(document).ready(function () {
    $(".select2").select2();

    if ( $( "#store_id" ).length ) {
        client_id = $("#customer" ).val();
        store_id = $("#store_id" ).val();
        populateStore(client_id, store_id);
        populateWarehouse(store_id, '');
    }

    if ( $( "#warehouse_id" ).length ) {
        warehouse_id = $("#warehouse_id" ).val();
        store_id = $("#store_id" ).val();
        populateWarehouse(store_id, warehouse_id);
    }

    $('#show-items-list tbody').on('click', 'tr', function (e) {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected')
        }
        else{
            $(this).addClass('selected');
        }
    });

});

$(document).on('change', '#company', function() {
    var company_id = $(this).val();
    populateStore(company_id, '','store');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '');
});

$(document).on('click', '#find-items', function() {

    var customer_id = $('#customer').val();
    var store_id = $('#store').val();
    var warehouse_id = $('#warehouse').val();
    var company_id = $('#company').val();

    if(customer_id) {
        $('#show-items').modal('show');
        if ($.fn.DataTable.isDataTable("#show-items-list")) {
            $('#show-items-list').DataTable().clear().destroy();
        }
        new DataTable("#show-items-list",{
            order: [[1, 'desc']],
            paging: true,
              columnDefs : [
                { targets: [4], className: 'dt-body-right' },
            ],
            ajax: {
                url : BASEURL+"settings/getAvailableItems",
                data : {
                    customer_id : customer_id,
                    store_id : store_id,
                    warehouse_id : warehouse_id,
                    company_id : company_id,
                },
                dataSrc:""
            },
            columns: [
                { data: 'product_id',  visible: false },
                { data: 'sap_code' },
                { data: 'product_code' },
                { data: 'product_name' },
                { data: 'inv_qty' },
                { data: 'ui_code' },
            ],
        });
    } else {
        alert("Customer Name required");
    }
});

$(document).on('click', '.remove-product', function() {
    var id = $(this).data('id');
    $('#product_'+id).remove();
});

$(document).on('click', '#add-product', function() {
    var table = $('#show-items-list').DataTable();
    var data = ( table.rows('.selected').data());
    if(data.length > 0) {
        for(x=0; x<data.length; x++) {
            var uom = getUom();
            var rowCount = $('#product-list tr').length;
            var idx = rowCount - 3;
            var btn = '<div class="text-center">';
            btn += '<a href="javascript:void(0)" class="text-danger remove-product" data-id="'+(rowCount-1)+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';
            btn += '</div>'

            $('#product-list tbody').append('<tr id="rows_'+(rowCount-1)+'"> \
            <td class="text-start"> \
                <input type="hidden" name="product_id[]" readonly id="product_id_'+data[x].product_id+'" value="'+data[x].product_id+'" /> \
                <input type="hidden" name="inv_uom[]" readonly id="inv_uom'+data[x].inv_uom+'" value="'+data[x].inv_uom+'" /> \
            '+rowCount+' </td> \
            <td class="text-start  fs-14"> \
                '+data[x].product_name+'<br/><small><em><b>'+((data[x].sap_code) ? data[x].sap_code : data[x].product_code )+'</b></em></small> \
            </td> \
            <td class="text-center  fs-14"> \
                '+data[x].inv_qty.toFixed(2)+'\
            </td> \
            <td class="text-start ps-1"> \
                <input type="text" class="form-control inv_qty numeric uom_select w-100" name="inv_qty[]" data-id="'+data[x].product_id+'" id="inv_qty_'+(rowCount-1)+'" value="1" placeholder="Order Quantity" /> \
                <span class="text-danger error-msg inv_qty'+(rowCount-1)+'_error"></span> \
            </td> \
            <td class="text-center ps-1"> \
                '+data[x].ui_code+'\
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

    var form_data = new FormData(document.getElementById("submit-do"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'open');

    _submitData(form_data);
});

$(document).on('click', '.submit-posted', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-do"));
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
$(document).on('click', '.create-do', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    setTimeout(function () {
        window.location = BASEURL+'do/create';
    }, 300);
});

$(document).on('click', '.submit-withdrawal', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-do"));
    form_data.append("_token", $('input[name=_token]').val());
    //form_data.append("status", 'posted');

    Swal.fire({
        title: 'Are you sure?',
        text: "You want to withdraw this transaction?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Withdraw it!'
      }).then((result) => {
        if (result.isConfirmed) {
            //_submitData(form_data);
        }
    });
});

function _submitData(form_data) {
    $.ajax({
        url: BASEURL + 'do',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-do').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'do/'+data.id+'/edit';
						}, 300);
                    } else {
                        toastr.success(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'do';
						}, 300);
                    }
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-do').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
}

