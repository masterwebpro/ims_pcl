// create a program for odd even number

$(document).ready(function () {
    $(".select2").select2();

    if ( $( "#store_id" ).length ) {
        client_id = $("#client_id" ).val();
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
        $('#show-items-list tbody tr').removeClass('selected')
        $(this).addClass('selected');
    });

});

$(document).on('change', '#client', function() {
    var client_id = $(this).val();
    populateStore(client_id, '');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '');
});

$(document).on('click', '#find-items', function() {
   
    var sup_id = $('#supplier').val();

    var supplier_id = (sup_id != '') ? sup_id : 0;

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
    $('#product_'+id).remove();
});

$(document).on('click', '#add-product', function() {
    var table = $('#show-items-list').DataTable();
    var data = ( table.rows('.selected').data()[0] );

    if (table.rows('.selected').data().length > 0) {
        var uom = getUom();
        var rowCount = $('#product-list tr').length;
        var idx = rowCount - 1;
        var btn = '<div class="text-center">';
        btn += '<a href="javascript:void(0)" class="text-danger remove-product" data-id="'+data.product_id+'"><i class="ri-delete-bin-5-fill label-icon align-middle rounded-pill fs-16 me-2"></i></a>';
        btn += '</div>'

        $('#product-list tbody').append('<tr id="product_'+data.product_id+'"> \
        <td class="text-start"> \
            <input type="hidden" name="product_id[]" readonly id="product_id_'+data.product_id+'" value="'+data.product_id+'" /> \
        '+rowCount+' </td> \
        <td class="text-start  fs-14"> \
            '+data.product_name+'<br/><small>'+data.product_code+'</small> \
        </td> \
        <td class="text-start"> \
            <select name="item_type[]" id="item_type_{{$x}}" class="uom uom_select form-select">  \
                <option value="good">Good</option>  \
                <option value="damage">Damage</option> \
                <option value="repair">Repair</option> \
            </select> \
            <span class="text-danger error-msg item_type'+(rowCount-1)+'_error"></span> \
        </td>  \
        <td class="text-start ps-1"> \
            <input type="text" class="form-control numeric whse_qty uom_select" name="whse_qty[]" data-id="'+data.product_id+'" id="whse_qty_'+idx+'" value="" placeholder="Whse Qty" /> \
            <span class="text-danger error-msg whse_qty'+(rowCount-1)+'_error"></span> \
        </td> \
            <td class="text-start ps-1"><select name="whse_uom[]" id="uom_'+idx+'" class="uom uom_select form-select select2"> \
            '+uom+'</select> \
            <span class="text-danger error-msg whse_uom'+(rowCount-1)+'_error"></span> \
        </td> \
        <td class="text-start ps-1"> \
            <input type="text" class="form-control inv_qty numeric uom_select" name="inv_qty[]" data-id="'+data.product_id+'" id="inv_qty_'+idx+'" value="" placeholder="Inv Qty" /> \
            <span class="text-danger error-msg inv_qty'+(rowCount-1)+'_error"></span> \
        <td class="text-start ps-1"> \
            <select name="inv_uom[]" id="inv_uom_'+idx+'" class="uom uom_select form-select select2"> \
            '+uom+'</select> \
            <span class="text-danger error-msg inv_uom'+(rowCount-1)+'_error"></span> \
        </td> \
        <td class="text-start ps-1"> \
            <small>-</small> \
        </td> \
        <td class="text-start ps-1"> \
            <small>-</small> \
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
});

$(document).on('click', '#receive-po-btn', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    var po_num = $('#po_num_holder').val();
    setTimeout(function () {
        window.location = BASEURL+'receive/'+escapeHtml(po_num)+'/create';
    }, 300);
    
});



// async
const data = {
    src: async (query) => {
      try {
        // Fetch Data from external Source
        const source = await fetch(BASEURL + 'settings/getPostedPO');
        const data = await source.json();
        return data;
      } catch (error) {
        return error;
      }
    },
    keys: ["po_num"],
    cache: true
}

if($("#po_num_holder").length) {

    var autoCompletePoNum = new autoComplete({
        selector: "#po_num_holder",
        placeHolder: "Search for PO number...",
        data: data,
        threshold: 2,
        resultsList: {
            element: function element(list, data) {
                if (!data.results.length) {
                    // Create "No Results" message element
                    var message = document.createElement("div");
                    // Add class to the created element
                    message.setAttribute("class", "no_result");
                    // Add message text content
                    message.innerHTML = "<span>Found No Results for \"" + data.query + "\"</span>";
                    // Append message element to the results list
                    list.prepend(message);
                }
            },
            noResults: true
        },
        resultItem: {
            highlight: true
        },
        events: {
            input: {
                selection: function selection(event) {
                    var selection = event.detail.selection.value;
                    autoCompletePoNum.input.value = selection.po_num;
                }
            }
        }
    });
}

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

