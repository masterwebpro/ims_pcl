$(document).ready(function () {
    $(".select2").select2();

    if ( $( "#store_id" ).length ) {
        client_id = $("#client" ).val();
        store_id = $("#store_id" ).val();
        populateStore(client_id, store_id,'store');
        populateWarehouse(store_id, '', 'warehouse');
    }

    if ( $( "#warehouse_id" ).length ) {
        warehouse_id = $("#warehouse_id" ).val();
        store_id = $("#store_id" ).val();
        populateWarehouse(store_id, warehouse_id, 'warehouse');
    }

});

$(document).on('change', '#client', function() {
    var client_id = $(this).val();
    populateStore(client_id, '', 'store');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '', 'warehouse');
});

//receiveing detailed

$(document).on('click', '.submit-receive-search', function(e) {
    e.preventDefault();
    var rcv_no = $('#rcv_no').val();
    var client = $('#client').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var date_received = $('#date_received').val();
    var item_type = $('#item_type').val();
    var product_code = $('#product_code').val();
    var product_name = $('#product_name').val();

    $.ajax({
        url: BASEURL + 'reports/get-receiving-detailed',
        method: 'get',
        data: {
            rcv_no:rcv_no,
            client:client,
            store:store,
            warehouse:warehouse,
            date_received:date_received,
            item_type:item_type,
            product_code:product_code,
            product_name:product_name,
        },
        dataType: 'json',
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-receive').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    var results = data.data;
                    var table = '';
                    $('#load-search').removeClass('d-none');
                    $('#load-data').addClass('d-none');
                    $('#item_list tbody').html('');
                    results.forEach(function(item) {
                        //var date = moment();
                        table += '<tr>';
                            table += "<td width='120px;'>"+moment(new Date(item.date_received)).format("DD MMM YYYY") +"</td>";
                            table += "<td width='120px;'>"+item.rcv_no+"</td>";
                            table += "<td width='120px;'>"+item.po_num+"</td>";
                            table += "<td class='text-center' width='120px;'>"+item.product_code+"</td>";
                            table += "<td class='text-left'>"+item.product_name+"</td>";
                            table += "<td class='text-center'>"+item.item_type+"</td>";
                            table += "<td class='text-center'>"+item.whse_qty+" / "+item.uw_code+"</td>";
                            table += "<td class='text-center'>"+item.inv_qty+" / "+item.ui_code+"</td>";
                        table += '</tr>';
                    });

                    $('#item_list tbody').append(table);

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


});

$(document).on('click', '.submit-receive-xls', function(e) {
    e.preventDefault();
    var rcv_no = $('#rcv_no').val();
    var client = $('#client').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var date_received = $('#date_received').val();
    var item_type = $('#item_type').val();
    var product_code = $('#product_code').val();
    var product_name = $('#product_name').val();

    window.location.href= BASEURL + 'reports/export-receiving-detailed?rcv_no='+rcv_no+'&client='+client+'&store='+store+'&warehouse='+warehouse+'&date_received='+date_received+'&item_type='+item_type+'&product_code='+product_code+'&product_name='+product_name;
});

$(document).on('click', '.submit-receive-print', function(e) {
    $('#preloading').modal('show');
    $('#load-data').html('');
    $('#load-data').removeClass('d-none');
    $('#load-data').attr('style','min-height:500px;');
    $('#load-search').addClass('d-none');

    e.preventDefault();
    var rcv_no = $('#rcv_no').val();
    var client = $('#client').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var date_received = $('#date_received').val();
    var item_type = $('#item_type').val();
    var product_code = $('#product_code').val();
    var product_name = $('#product_name').val();

    pdf_url= BASEURL + 'reports/print-receiving-detailed?rcv_no='+rcv_no+'&client='+client+'&store='+store+'&warehouse='+warehouse+'&date_received='+date_received+'&item_type='+item_type+'&product_code='+product_code+'&product_name='+product_name;
    var iframe = $('<iframe>');
    iframe.attr('src',pdf_url);
    setTimeout(function () {
        $('#preloading').modal('hide');
        $('#load-data').append(iframe);
    }, 100);

});

$(document).on('click', '.submit-withdrawal-search', function(e) {
    e.preventDefault();
    var wd_no = $('#wd_no').val();
    var client = $('#client').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var withdraw_date = $('#withdraw_date').val();
    var order_type = $('#order_type').val();
    var product_code = $('#product_code').val();
    var product_name = $('#product_name').val();

    $.ajax({
        url: BASEURL + 'reports/get-withdrawal-detailed',
        method: 'get',
        data: {
            wd_no:wd_no,
            client:client,
            store:store,
            warehouse:warehouse,
            withdraw_date:withdraw_date,
            order_type:order_type,
            product_code:product_code,
            product_name:product_name,
        },
        dataType: 'json',
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-withdrawal').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    var results = data.data;
                    var table = '';
                    $('#load-search').removeClass('d-none');
                    $('#load-data').addClass('d-none');
                    $('#item_list tbody').html('');
                    results.forEach(function(item) {
                        //var date = moment();
                        table += '<tr>';
                            table += "<td width='120px;'>"+moment(new Date(item.withdraw_date)).format("DD MMM YYYY") +"</td>";
                            table += "<td width='120px;'>"+item.wd_no+"</td>";
                            table += "<td width='120px;'>"+item.order_no+"</td>";
                            table += "<td width='120px;'>"+item.order_type+"</td>";
                            table += "<td width='120px;'>"+item.dr_no+"</td>";
                            table += "<td width='120px;'>"+item.sales_invoice+"</td>";
                            table += "<td width='120px;'>"+item.po_num+"</td>";
                            table += "<td class='text-center' width='120px;'>"+item.product_code+"</td>";
                            table += "<td class='text-left'>"+item.product_name+"</td>";
                            table += "<td class='text-center'>"+item.inv_qty+" / "+item.ui_code+"</td>";
                        table += '</tr>';
                    });

                    $('#item_list tbody').append(table);

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
});

$(document).on('click', '.submit-withdrawal-xls', function(e) {
    e.preventDefault();
    var wd_no = $('#wd_no').val();
    var client = $('#client').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var withdraw_date = $('#withdraw_date').val();
    var product_code = $('#product_code').val();
    var product_name = $('#product_name').val();
    var order_type = $('#order_type').val();

    window.location.href= BASEURL + 'reports/export-withdrawal-detailed?wd_no='+wd_no+'&client='+client+'&store='+store+'&warehouse='+warehouse+'&withdraw_date='+withdraw_date+'&order_type='+order_type+'&product_code='+product_code+'&product_name='+product_name;
});


$(document).on('click', '.submit-withdrawal-print', function(e) {
    $('#preloading').modal('show');
    $('#load-data').html('');
    $('#load-data').removeClass('d-none');
    $('#load-data').attr('style','min-height:500px;');
    $('#load-search').addClass('d-none');

    e.preventDefault();
    var wd_no = $('#wd_no').val();
    var client = $('#client').val();
    var store = $('#store').val();
    var warehouse = $('#warehouse').val();
    var withdraw_date = $('#withdraw_date').val();
    var order_type = $('#order_type').val();
    var product_code = $('#product_code').val();
    var product_name = $('#product_name').val();

    pdf_url= BASEURL + 'reports/print-withdrawal-detailed?wd_no='+wd_no+'&client='+client+'&store='+store+'&warehouse='+warehouse+'&withdraw_date='+withdraw_date+'&order_type='+order_type+'&product_code='+product_code+'&product_name='+product_name;
    var iframe = $('<iframe>');
    iframe.attr('src',pdf_url);
    setTimeout(function () {
        $('#preloading').modal('hide');
        $('#load-data').append(iframe);
    }, 100);

});

$(document).on('click', '.submit-outbound-monitoring-xls', function(e) {
    e.preventDefault();
    var client = $('#client').val();
    var dispatch_date = $('#dispatch_date').val();
    window.location.href= BASEURL + 'reports/export-outbound-monitoring?client='+client+'&dispatch_date='+dispatch_date;
});
