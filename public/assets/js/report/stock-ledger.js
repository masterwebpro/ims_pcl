$(document).ready(function () {
    $(".select2").select2();

    // if ( $( "#store_id" ).length ) {
    //     client_id = $("#client_id" ).val();
    //     store_id = $("#store_id" ).val();
    //     populateStore(client_id, store_id);
    //     populateWarehouse(store_id, '');
    // }

    // if ( $( "#warehouse_id" ).length ) {
    //     warehouse_id = $("#warehouse_id" ).val();
    //     store_id = $("#store_id" ).val();
    //     populateWarehouse(store_id, warehouse_id);
    // }
});

$(document).on('change', '#client', function() {
    var client_id = $(this).val();
    populateStore(client_id, '');
});

$(document).on('change', '#store', function() {
    var store_id = $(this).val();
    populateWarehouse(store_id, '');
});