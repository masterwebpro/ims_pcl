// create a program for odd even number

$(document).ready(function () {
    $(".select2").select2();

    if ( $( "#store_id" ).length ) {
        company_id = $("#company_id" ).val();
        store_id = $("#store_id" ).val();
        populateStore(company_id, store_id);
        populateWarehouse(store_id, '');
    }

    if ( $( "#warehouse_id" ).length ) {
        warehouse_id = $("#warehouse_id" ).val();
        store_id = $("#store_id" ).val();
        populateWarehouse(store_id, warehouse_id);
    }

    $('#show-items-list tbody').on('click', 'tr', function (e) {
        $('#show-items-list tbody tr').removeClass('selected')
        $(this).addClass('selected');
    });

    $('#po-table tbody').on('click', 'tr', function (e) {
        $('#po-table tbody tr').removeClass('selected')
        $(this).addClass('selected');
    });

});

$(document).on('change', '#company', function() {
    var company_id = $(this).val();
    populateStore(company_id, '');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '');
});

$(document).on('click', '#find-items', function() {
   
    var sup_id = $('#supplier').val();

    // var supplier_id = (sup_id != '') ? sup_id : 0;

    //if(supplier_id) {   

        $('#show-items').modal('show'); 
        if ($.fn.DataTable.isDataTable("#show-items-list")) {
            $('#show-items-list').DataTable().clear().destroy();
        }
        new DataTable("#show-items-list",{
            order: [[3, 'asc']],
            paging: true,
            //ajax: BASEURL+"settings/products/"+supplier_id+'/get',
            ajax: BASEURL+"settings/products",
            columns: [
                { data: 'product_id',  visible: false },
                { data: 'product_code' },
                { data: 'product_sku' },
                { data: 'product_name' }
            ],
        });
    // } else {
    //     alert("Supplier Name required");
    // }
});

$(document).on('click', '.remove-product', function() {
    var id = $(this).data('id');
    // $('#product_'+id).remove();
    $(this).closest("tr").remove();
});

$(document).on('click', '#add-product', function() {
    var table = $('#show-items-list').DataTable();
    var data = ( table.rows('.selected').data()[0] );

    if (table.rows('.selected').data().length > 0) {
        var uom = getUom();
        var rowCount = $('#product-list tr').length;
        var idx = rowCount - 1;

        let uniqueId = Date.now().toString(36) + Math.random().toString(36).substring(2);

        var btn = '<div class="text-center">';
        btn += '<a href="javascript:void(0)" class="text-danger remove-product" data-id="'+rowCount+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';
        btn += '</div>'

        $('#product-list tbody').append('<tr id="product_'+uniqueId+'"> \
        <td class="text-start d-none"> \
            <input type="hidden" name="product_id[]" readonly id="product_id_'+data.product_id+'" value="'+data.product_id+'" /> \
        '+(rowCount-1)+' </td> \
        <td class="text-start  fs-12"> \
            '+data.product_name+'<br/><small>'+data.product_code+'</small> \
        </td> \
        <td class="text-start"> \
            <select name="item_type[]" id="item_type_'+uniqueId+'" class="uom uom_select form-select">  \
                <option value="good">Good</option>  \
                <option value="damage">Damage</option> \
                <option value="repair">Repair</option> \
            </select> \
            <span class="text-danger error-msg item_type'+uniqueId+'_error"></span> \
        </td>  \
        <td class="text-start ps-1"> \
            <input type="text" class="form-control numeric whse_qty uom_select" name="whse_qty[]" data-id="'+uniqueId+'" id="whse_qty_'+uniqueId+'" value="" placeholder="Whse Qty" /> \
            <span class="text-danger error-msg whse_qty'+(idx-1)+'_error"></span> \
        </td> \
            <td class="text-start ps-1"><select name="whse_uom[]" data-id="'+uniqueId+'" id="uom_'+uniqueId+'" class="uom  whse_uom uom_select form-select select2"> \
            '+uom+'</select> \
            <span class="text-danger error-msg whse_uom'+(idx-1)+'_error"></span> \
        </td> \
        <td class="text-start ps-1"> \
            <input type="text" class="form-control inv_qty numeric uom_select" name="inv_qty[]" data-id="'+uniqueId+'" id="inv_qty_'+uniqueId+'" value="" placeholder="Inv Qty" /> \
            <span class="text-danger error-msg inv_qty'+(idx-1)+'_error"></span> \
        <td class="text-start ps-1"> \
            <select name="inv_uom[]" data-id="'+uniqueId+'" id="inv_uom_'+uniqueId+'" class="uom uom_select form-select select2"> \
            '+uom+'</select> \
            <span class="text-danger error-msg inv_uom'+(idx-1)+'_error"></span> \
        </td> \
        <td class="ps-1"> \
            <input type="text" class="form-control" style="width: 150px;" name="lot_no[]" placeholder="Lot/Batch No" /> \
        </td> \
        <td class="ps-1"> \
            <input type="date" class="form-control " name="expiry_date[]"  placeholder="Expiry Date" /> \
        </td> \
        <td class="ps-1"> \
            <input type="text" class="form-control" style="width: 150px;" name="item_remarks[]"  placeholder="Remarks" /> \
        </td> \
        <td>'+btn+'</td> \
        </tr>');


    }

    $('#show-items-list tbody tr').removeClass('selected')
    toastr.success(data.product_name + ' successfully added');
    //$('#show-items').modal('hide');
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
                url: BASEURL + 'receive',
                data: {
                    rcv_no : $('#rcv_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "DELETE",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-receive').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message); 
                            setTimeout(function () {
                                window.location = BASEURL+'receive';
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
                url: BASEURL + 'receive/unpost',
                data: {
                    rcv_no : $('#rcv_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "post",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-receive').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message); 
                            setTimeout(function () {
                                window.location = BASEURL+'receive';
                            }, 300);
                            
                        } else {
                            // toastr.error(data.message,'Error on saving'); 
                            showError(data.message);
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


$(document).on('click', '.create-receiving', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    setTimeout(function () {
        window.location = BASEURL+'receive/create';
    }, 300);
});

$(document).on('click', '.receive-po', function (e) {
    e.preventDefault();
    $('#po_num_holder').val('');
    $('#show-po').modal('show');

    if ($.fn.DataTable.isDataTable("#unit-allocation")) {
        $('#po-table').DataTable().clear().destroy();
    }
    new DataTable("#po-table",{ 
        paging: true,
        ajax: BASEURL+"settings/getAllPostedPO",
        columns: [
            { data: 'po_num' },
            { data: 'po_date' },
            { data: 'supplier_name' },
            { data: 'customer_name'},
            { data: 'created_by' }
        ],
    });
});

$(document).on('click', '#receive-po-btn', function (e) {
    e.preventDefault();
    var table = $('#po-table').DataTable();
    var data = ( table.rows('.selected').data()[0] );

    if (table.rows('.selected').data().length > 0) {
        $('#preloading').modal('show');
        rec = _encode(data.id);
        setTimeout(function () {
            window.location = BASEURL+'receive/'+rec.responseText+'/create';
        }, 300);
    } else {
        alert("Please select a PO Number");
    }    
});

function _submitData(form_data) {
    $.ajax({
        url: BASEURL + 'receive',
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
                console.log(data.data);
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'receive/'+data.id+'/edit';
						}, 300);
                    } else {
                        toastr.success(data.message); 
                        setTimeout(function () {
							window.location = BASEURL+'receive';
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

$(document).on('blur keyup', '#item_code', function(e) {
    if (e.type === 'blur' || e.keyCode === 13)  {

        var item_code = $(this).val();
      
        $.ajax({
            url: BASEURL + 'settings/product',
            method: "get",
            data: {
                'item_code': item_code
            },
            beforeSend: function () {
                $('#preloading').modal('show');
                $('#form-po').find('span.error-msg').text('');
            },
            success: function (data) {
                if($.isEmptyObject(data.errors)) {
                    if(data.success == true) {
                        if(data.data) {
                            var uom = getUom();
                            var rowCount = $('#product-list tr').length;
                            var idx = rowCount - 1;
                            var btn = '<div class="text-center">';
                            btn += '<a href="javascript:void(0)" class="text-danger remove-product" data-id="'+rowCount+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';
                            btn += '</div>'

                            $('#product-list tbody').append('<tr id="product_'+rowCount+'"> \
                            <td class="text-start"> \
                                <input type="hidden" name="product_id[]" readonly id="product_id_'+data.data.product_id+'" value="'+data.data.product_id+'" /> \
                            '+rowCount+' </td> \
                            <td class="text-start  fs-12"> \
                                '+data.data.product_name+'<br/><small>'+data.data.product_code+'</small> \
                            </td> \
                            <td class="text-start"> \
                                <select name="item_type[]" id="item_type_'+rowCount+'" class="uom uom_select form-select">  \
                                    <option value="good">Good</option>  \
                                    <option value="damage">Damage</option> \
                                    <option value="repair">Repair</option> \
                                </select> \
                                <span class="text-danger error-msg item_type'+(rowCount-1)+'_error"></span> \
                            </td>  \
                            <td class="text-start ps-1"> \
                                <input type="text" class="form-control numeric whse_qty uom_select" name="whse_qty[]" data-id="'+idx+'" id="whse_qty_'+idx+'" value="" placeholder="Whse Qty" /> \
                                <span class="text-danger error-msg whse_qty'+(rowCount-1)+'_error"></span> \
                            </td> \
                                <td class="text-start ps-1"><select name="whse_uom[]" data-id="'+idx+'" id="uom_'+idx+'" class="uom  whse_uom uom_select form-select select2"> \
                                '+uom+'</select> \
                                <span class="text-danger error-msg whse_uom'+(rowCount-1)+'_error"></span> \
                            </td> \
                            <td class="text-start ps-1"> \
                                <input type="text" class="form-control inv_qty numeric uom_select" name="inv_qty[]" data-id="'+idx+'" id="inv_qty_'+idx+'" value="" placeholder="Inv Qty" /> \
                                <span class="text-danger error-msg inv_qty'+(rowCount-1)+'_error"></span> \
                            <td class="text-start ps-1"> \
                                <select name="inv_uom[]" data-id="'+idx+'" id="inv_uom_'+idx+'" class="uom uom_select form-select select2"> \
                                '+uom+'</select> \
                                <span class="text-danger error-msg inv_uom'+(rowCount-1)+'_error"></span> \
                            </td> \
                            <td class="ps-1"> \
                                <input type="text" class="form-control" style="width: 150px;" name="lot_no[]" placeholder="Lot/Batch No" /> \
                            </td> \
                            <td class="ps-1"> \
                                <input type="date" class="form-control " name="expiry_date[]"  placeholder="Expiry Date" /> \
                            </td> \
                            <td class="ps-1"> \
                                <input type="text" class="form-control" style="width: 150px;" name="item_remarks[]"  placeholder="Remarks" /> \
                            </td> \
                            <td>'+btn+'</td> \
                            </tr>');


                            toastr.success(data.data.product_name,'Added successfully');
                          

                        } else {
                            toastr.error(item_code,'No Record found!');
                        }
                    } else {
                        toastr.error(data.message,'Error on saving');
                    }
                } else {
                    $.each(data.errors, function(prefix, val) {
                        $('#errMsg').removeClass('d-none');
                        $('#form-po').find('span.'+prefix.replace('.','')+'_error').text(val);
                    });
                    toastr.error('Some fields are required');
                }
            },
            complete: function() {
                $('#preloading').modal('hide');
                $('#item_code').val('');
            }
        });
    }
});

$(document).on('keyup', '.whse_qty', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    var val = $(this).val();

    $('#inv_qty_'+id).val(val);

    computeAll();
});

$(document).on('keyup', '.inv_qty', function(e){
    e.preventDefault();
    computeAll();
});

$(document).on('change', '.whse_uom', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    var val = $(this).val();

    $('#inv_uom_'+id).val(val);
});

function computeAll() {

    var total_whse_qty = 0;
    var total_inv_qty = 0;

    $("table#product-list").find('input[name^="whse_qty"]').each(function () {
        total_whse_qty += stringToFloat($(this).val());
    });

    $("table#product-list").find('input[name^="inv_qty"]').each(function () {
        total_inv_qty += stringToFloat($(this).val());
    });
    $("#total_inv_qty").val(total_inv_qty);
    $("#total_whse_qty").val(total_whse_qty);
}