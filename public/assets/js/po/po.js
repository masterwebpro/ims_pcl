$(document).ready(function () {
    $(".select2").select2();

    if ( $( "#store_id" ).length ) {
        company_id = $("#company" ).val();
        store_id = $("#store_id" ).val();
        populateStore(company_id, store_id, 'store');
    }

    $('#show-items-list tbody').on('click', 'tr', function (e) {
        $('#show-items-list tbody tr').removeClass('selected')
        $(this).addClass('selected');
    });

});

$(document).on('change', '#company', function() {
    var company_id = $(this).val();
    populateStore(company_id, '', 'store');
});

$(document).on('click', '#find-items', function() {
//    var supplier_id = $('#supplier').val();

//    if(supplier_id) {   

        $('#show-items').modal('show'); 
        if ($.fn.DataTable.isDataTable("#show-items-list")) {
            $('#show-items-list').DataTable().clear().destroy();
        }
        new DataTable("#show-items-list",{
            order: [[1, 'desc']],
            paging: true,
            ajax: {
                url :BASEURL+"settings/products",
                data : {
                    supplier_id: ''
                }
            },

        //    ajax: BASEURL+"settings/products/"+supplier_id+'/get',
            // ajax: BASEURL+"settings/products",
            columns: [
                { data: 'product_id',  visible: false },
                { data: 'sap_code' },
                { data: 'product_code' },
                { data: 'product_sku' },
                { data: 'product_name' }
            ],
            "pageLength": 25,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All']
            ]
        });
//    } else {
//        showError("Please select supplier name.");
//    }
});

$(document).on('click', '#add-product', function() {
    var table = $('#show-items-list').DataTable();
    var data = ( table.rows('.selected').data()[0] );

    if (table.rows('.selected').data().length > 0) {
        //get UOM list

        var uom = getUom();
        var rowCount = $('#product-list tr').length - 1;
        
        var btn = '<div class="text-center">';
        btn += '<a href="javascript:void(0)" class="text-danger remove-product" data-id="'+data.product_id+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';
        btn += '</div>'

        $('#product-list tbody').append('<tr id="product_'+data.product_id+'"> \
        <td class="text-start"> \
        '+rowCount+' \
        </td> \
        <td class="text-start  fs-14"> \
            <input type="hidden" name="product_id[]" readonly id="product_id_'+data.product_id+'" value="'+data.product_id+'" /> \
            '+data.product_name+'<br/><small>'+data.sap_code+'</small> \
            <input type="hidden" readonly class="form-control" name="product_code[]" data-id="'+data.product_id+'" id="product_code_'+data.product_id+'" value="'+data.product_code+'" placeholder="Code" /> \
            <input type="hidden" readonly class="form-control" name="sap_code[]" data-id="'+data.product_id+'" id="sap_code_'+data.product_id+'" value="'+data.sap_code+'" placeholder="Code" /> \
            <input type="hidden" readonly class="form-control" name="product_name[]" data-id="'+data.product_id+'" id="product_name_'+data.product_id+'" value="'+data.product_name+'" placeholder="Sku" /> \
        </td> \
        <td class="text-start ps-1">\
          <select name="uom[]" class="uom uom_select form-select select2">  '+uom+' </select> \
          <span class="text-danger error-msg uom'+(rowCount-1)+'_error"></span> \
        </td> \
        <td class="text-right"> \
            <input type="text" style="text-align: right !important" class="form-control qty text-right" name="qty[]" data-id="'+data.product_id+'" id="qty_'+data.product_id+'" value="" placeholder="Qty" /> \
            <span class="text-danger error-msg qty'+(rowCount-1)+'_error"></span> \
        </td> \
        <td class="text-start d-none"> \
             <input type="text" name="unit_price[]" value="0" data-id="'+data.product_id+'" id="unit_price_'+data.product_id+'" class="form-control unit_price text-end" placeholder="0.00" /> \
             <span class="text-danger error-msg unit_price'+(rowCount-1)+'_error"></span> \
        </td> \
        <td class="text-start d-none"> \
            <input type="text" name="discount[]" data-id="'+data.product_id+'" id="discount_'+data.product_id+'" value="0" class="form-control discount text-end" placeholder="0.00" /> \
        </td> \
        <td class="d-none"><input type="text" name="amount[]" readonly data-id="'+data.product_id+'" id="total_amount_'+data.product_id+'" data-id="'+data.id+'"  value="0" class="form-control total_amount text-end" placeholder="0.00"/><span class="text-danger error-msg amount'+(rowCount-1)+'_error"></span> \</td> \
        <td>'+btn+'</td> \
        </tr>');

        $('#show-items-list tbody tr').removeClass('selected')
    }

});

$(document).on('click', '.submit-po', function (e) {
    e.preventDefault();
    var id = $('#po_id').val();
    var status =$(this).data('status');

    var form_data = new FormData(document.getElementById("form-po"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", status);

    $.ajax({
        url: BASEURL + 'po',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#form-po').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    if(status == 'posted') {
                        setTimeout(function(){
                            window.location= BASEURL + 'po';
                        }, 300);
                    } else {
                        setTimeout(function(){
                            window.location= BASEURL + 'po/'+data.id+'/edit';
                        }, 300);
                    }
                    toastr.success(data.message);

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
		}
    });
});

$(document).on('blur', '.qty', function () {
    var id = $(this).data('id');
    var val = $(this).val();

    unit_price = $('#unit_price_'+id).val();

    $('#total_amount_'+id).val(number_format(parseFloat(val * unit_price).toFixed(2)));

    computeAll();

});

$(document).on('blur', '.unit_price', function () {
    var id = $(this).data('id');
    var val = $(this).val();
    console.log(val);

    qty = $('#qty_'+id).val();

    $('#total_amount_'+id).val(number_format(parseFloat(val * qty).toFixed(2)));

    computeAll();

});

$(document).on('blur', '.qty', function () {
    computeAll();
});

$(document).on('blur', '.discount', function () {
    computeAll();
});


function computeAll() {

    var subtotal = 0;
    var discount = 0;
    var total_amount = 0;
    var qty = 0;


    $("table#product-list").find('input[name^="amount"]').each(function () {
        subtotal += stringToFloat($(this).val());
    });

    $("table#product-list").find('input[name^="discount"]').each(function () {
        discount += stringToFloat($(this).val());
    });

    $("table#product-list").find('input[name^="qty"]').each(function () {
        qty += stringToFloat($(this).val());
    });

    total_amount = subtotal - discount;

    $("#cart-subtotal").val(number_format(parseFloat(subtotal).toFixed(2)));
    $("#cart-discount").val(number_format(parseFloat(discount).toFixed(2)));
    $("#cart-total").val(number_format(parseFloat(total_amount).toFixed(2)));
    $("#total_qty").val(qty);
}

$(document).on('click', '.remove-product', function() {
    var id = $(this).data('id');
    $('#product_'+id).remove();
});

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
                            
                            var btn = '<div class="text-center">';
                            btn += '<a href="javascript:void(0)" class="text-danger remove-product" data-id="'+rowCount+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';
                            btn += '</div>'

                            $('#product-list tbody').append('<tr id="product_'+rowCount+'"> \
                            <td class="text-start"> \
                            '+rowCount+' \
                            </td> \
                            <td class="text-start  fs-14"> \
                                <input type="hidden" name="product_id[]" readonly id="product_id_'+data.data.product_id+'" value="'+data.data.product_id+'" /> \
                                '+data.data.product_name+'<br/><small>'+data.data.product_code+'</small> \
                                <input type="hidden" readonly class="form-control" name="product_code[]" data-id="'+data.data.product_id+'" id="product_code_'+data.data.product_id+'" value="'+data.data.product_code+'" placeholder="Code" /> \
                                <input type="hidden" readonly class="form-control" name="product_name[]" data-id="'+data.data.product_id+'" id="product_name_'+data.data.product_id+'" value="'+data.data.product_name+'" placeholder="Sku" /> \
                            </td> \
                            <td class="text-start ps-1">\
                              <select name="uom[]" class="uom uom_select form-select select2">  '+uom+' </select> \
                              <span class="text-danger error-msg uom'+(rowCount-1)+'_error"></span> \
                            </td> \
                            <td class="text-start"> \
                                <input type="text" style="text-align: right !important" class="form-control qty" name="qty[]" data-id="'+data.data.product_id+'" id="qty_'+data.data.product_id+'" value="0" placeholder="Qty" /> \
                                <span class="text-danger error-msg qty'+(rowCount-1)+'_error"></span> \
                            </td> \
                            <td class="text-start d-none"> \
                                 <input type="text" name="unit_price[]" data-id="'+data.data.product_id+'" id="unit_price_'+data.data.product_id+'" value="0" class="form-control unit_price text-end" placeholder="0.00" /> \
                                 <span class="text-danger error-msg unit_price'+(rowCount-1)+'_error"></span> \
                            </td> \
                            <td class="text-start d-none"> \
                                <input type="text" name="discount[]" data-id="'+data.data.product_id+'" id="discount_'+data.data.product_id+'" value="0" class="form-control discount text-end" placeholder="0.00" /> \
                            </td> \
                            <td class="d-none"><input type="text" name="amount[]" readonly data-id="'+data.data.product_id+'" id="total_amount_'+data.data.product_id+'"  value="" class="form-control total_amount text-end" placeholder="0.00"/><span class="text-danger error-msg amount'+(rowCount-1)+'_error"></span> \</td> \
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
