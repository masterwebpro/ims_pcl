// async
const data = {
    src: async (query) => {
      try {
        // Fetch Data from external Source
        const source = await fetch(BASEURL + 'settings/allProducts');
        const data = await source.json();
        return data;
      } catch (error) {
        return error;
      }
    },
    keys: ["product_id", "product"],
    cache: true
}

if($("#product_holder").length) {

    var autoCompletePoNum = new autoComplete({
        selector: "#product_holder",
        placeHolder: "Search for Product code or Name...",
        data: data,
        threshold: 4,
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
                    //console.log(event.detail.selection.value);
                    var selection = event.detail.selection.value;
                    $('#product_id').val(selection.product_id);
                    autoCompletePoNum.input.value = selection.product;
                }
            }
        }
    });
}


$(document).on('click', '.submit-inventory', function(e) {
    e.preventDefault();

    var company = $('#company').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var item_type = $('#item_type').val();
    var product_id = $('#product_id').val();
    var location = $('#location').val();
   
    $.ajax({
        url: BASEURL + 'reports/getInventoryReport',
        method: 'get',
        data: {
            company:company,
            store:store,
            warehouse:warehouse,
            item_type:item_type,
            product_id:product_id,
            location:location,
        },
        dataType: 'json',
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#inventory_list tbody').html('');
            $('#submit-ledger').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    var res = data.result;
                    var table = '';
                    res.forEach(function(item) {
                        //get the first 2 character
                        var location = (item.location != null) ? item.location : 'RA';
                       
                        table += '<tr>';
                            // table += "<td width='120px;'>"+item.client_name+"</td>";
                            // table += "<td width='120px;'>"+item.store_name+"</td>";
                            // table += "<td width='120px;'>"+item.warehouse_name+"</td>";
                        
                            table += "<td class='text-center'>"+item.product_code+"</td>";
                            table += "<td class='text-left'>"+item.product_name+"</td>";
                            table += "<td class='text-center' width='120px;'>"+item.item_type+"</td>";
                            table += "<td class='text-center' width='80px;'>"+location+"</td>";
                            table += "<td class='text-center'>"+item.whse_qty+"</td>";
                            table += "<td class='text-center'>"+item.uw_code+"</td>";

                            table += "<td class='text-center'>"+item.inv_qty+"</td>";
                            table += "<td class='text-center'>"+item.ui_code+"</td>";                                           
                        table += '</tr>';                        
                    });

                    $('#inventory_list tbody').html(table);
                } else {
                    toastr.error(data.message,'Error on saving'); 
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-ledger').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
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


$(document).on('change', '#warehouse', function() {
    var value = $(this).val();
    //populateWarehouse(store_id, '');
    populateLocation('location', value, '');

});


$(document).on('click', '.submit-xls-inventory', function(e) {
    e.preventDefault();

    var company = $('#company').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var item_type = $('#item_type').val();
    var product_id = $('#product_id').val();
    var location = $('#location').val();
    
    window.location.href= BASEURL + 'reports/export-inventory?company='+company+'&store='+store+'&warehouse='+warehouse+'&item_type='+item_type+'&product_id='+product_id+'&location='+location;
});
