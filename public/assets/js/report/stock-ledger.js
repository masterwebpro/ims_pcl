$(document).ready(function () {
    $(".select2").select2();
});

$(document).on('change', '#client', function() {
    var client_id = $(this).val();
    populateStore(client_id, '', 'store');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '', 'warehouse');
});

$(document).on('change', '#warehouse', function() {
    var warehouse_id = $(this).val();
    $.ajax({
        url: BASEURL + 'settings/newLocation/'+warehouse_id,
        method: 'get',
        dataType: 'json',
        async: false,
        success: function(res){
            if(res.success == true) {
                result = res.data;
                $('#location').html('');
                $('#location').append(result);
            }
        }
    });
});


$(document).on('click', '.submit-stock-ledger', function(e) {
    e.preventDefault();

    var client = $('#client').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var item_type = $('#item_type').val();
    var product_id = $('#product_id').val();
    var location = $('#location').val();
    var date_range = $('#date_picker').val();
   
    $.ajax({
        url: BASEURL + 'reports/getStockLedger',
        method: 'get',
        data: {
            client:client,
            store:store,
            warehouse:warehouse,
            item_type:item_type,
            product_id:product_id,
            location:location,
            date_range: date_range
        },
        dataType: 'json',
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#masterfile_list tbody').html('');
            $('#submit-ledger').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    var res = data.result;
                    var table = '';
                   
                    beggining_balance= 0;
                    
                    if(data.beg_balance[0]) {
                        if(data.beg_balance[0].inv_qty)
                            beggining_balance = data.beg_balance[0].inv_qty;
                    }

                    table += '<tr>';
                        table += "<td colspan='9' class='fw-medium'>Begginning Qty</td>";
                    
                        table += "<td class='text-end fw-bold'>"+beggining_balance+"</td>";                      
                    table += '</tr>';

                    var remaining = beggining_balance;

                    res.forEach(function(item) {
                        //get the first 2 character

                
                        var type = item.ref_no.split("-");
                       
                        rcv_qty = 0;
                        if(item.trans_type == 'RV')
                            rcv_qty = item.inv_qty;

                        transfer_qty = 0;
                        if(item.trans_type == 'ST')
                            transfer_qty = item.inv_qty;

                        withdraw_qty = 0;
                        reserved_qty = 0;
                        if(item.trans_type == 'WD') {
                            if(item.inv_qty > 0) { //positive
                                if(type[1] == 'TWD') {
                                    withdraw_qty = item.inv_qty;
                                } else {
                                    reserved_qty = item.inv_qty;
                                }
                                // reserved_qty = item.inv_qty;
                            } else {
                                if(type[1] == 'TWD') {
                                    reserved_qty = item.inv_qty;
                                } else {
                                    if(type[0] == 'W') {
                                        reserved_qty = item.inv_qty;
                                    } else {
                                        withdraw_qty = item.inv_qty;
                                    }
                                    
                                }   
                                
                            }
                        }
                            

                        if(item.trans_type == 'SM') {
                            if(item.inv_qty < 0) {
                                withdraw_qty = item.inv_qty;
                            } else {
                                transfer_qty = item.inv_qty;
                            }
                            
                        }

                        balance = remaining + (parseInt(rcv_qty) + parseInt(transfer_qty) + parseInt(withdraw_qty) + parseInt(reserved_qty)) ;
                        remaining =+ balance;

                        if(rcv_qty == 0)
                            rcv_qty = '';
                        if(transfer_qty == 0)
                            transfer_qty = '';
                        if(withdraw_qty == 0)
                            withdraw_qty = '';

                        if(reserved_qty == 0) {
                            reserved_qty = ''
                        }
                        if(type[0] == 'R')
                            remarks = 'Received';
                        else if(type[0] == 'ST')
                            remarks = 'Stock Transfer';
                        else if(type[0] == 'SM')
                            remarks = 'Putaway';
                        else if(type[0] == 'W')
                            remarks = 'Dispatched';
                        else if(type[0] == 'TWD') {
                            if(item.inv_qty >= 0)
                                remarks = 'Reserved';
                            else
                                remarks = 'Withdrawn';
                        }   
                            
                        else
                            remarks = '';

                            
                        var location = (item.location != null) ? item.location : 'RA';
                          
                        table += '<tr>';
                            table += "<td width='120px;'>"+moment(new Date(item.created_at)).format("DD MMM YYYY")+"</td>";
                            table += "<td width='120px;'>"+item.ref_no+"</td>";
                            table += "<td width='120px;'>"+remarks+"</td>";
                            table += "<td class='text-center' width='80px;'>"+location+"</td>";
                            table += "<td class='text-center' width='120px;'>"+item.item_type+"</td>";

                            table += "<td class='text-end'>"+rcv_qty+"</td>";
                            table += "<td class='text-end'>"+transfer_qty+"</td>";
                            table += "<td class='text-end'>"+withdraw_qty+"</td>";
                            table += "<td class='text-end'>"+reserved_qty+"</td>";
                            table += "<td class='text-end'>"+parseInt(remaining)+"</td>";                      
                        table += '</tr>';                        
                    });

                    table += '<tr>';
                        table += "<td colspan='9' class='fw-medium'>Remaining Qty</td>";
                    
                        table += "<td class='text-end fw-bold'>"+number_format(parseInt(remaining))+"</td>";                      
                    table += '</tr>';

                    $('#masterfile_list tbody').html(table);
                  
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