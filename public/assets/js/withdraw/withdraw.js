var filterParams = {
    comparator: (filterLocalDateAtMidnight, cellValue) => {
        var dateAsString = cellValue;
        if (dateAsString == null) return -1;
        
        var dateAsString = moment(cellValue).format('DD/MM/YYYY');

        var dateParts = dateAsString.split("/");
        var cellDate = new Date(Number(dateParts[2]), Number(dateParts[1]) - 1, Number(dateParts[0]));

        if (filterLocalDateAtMidnight.getTime() === cellDate.getTime()) {
        return 0;
        }

        if (cellDate < filterLocalDateAtMidnight) {
        return -1;
        }

        if (cellDate > filterLocalDateAtMidnight) {
        return 1;
        }
        return 0;
    },
};
const columnDefs = [
    {headerName: '', filter: false, width:20, suppressMenu: true,  cellClass: 'text-end',

        headerCheckboxSelection: true, // Checkbox in header
        checkboxSelection: true, // Checkbox in each row
        filter: false,
        sort: false,
    },  
    { field: 'master_id', headerName: "Master Id", hide:true},
    { field: 'brand_name', headerName: "Brand", filter: 'agSetColumnFilter',suppressMenu: true },
    { field: 'product_code', headerName: "Product Code", filter: 'agTextColumnFilter'},
    { field: 'product_name', headerName: "Product Name", filter: 'agTextColumnFilter'},
    
    { field: 'item_type', headerName: "Item Type", hide:true, filter: 'agSetColumnFilter',},
    { field: 'inv_qty', headerName: "Stocks", filter: 'agTextColumnFilter', cellClass: 'text-end',valueFormatter: function (params) {
        if(params.value != null) {
            return number_format(params.value);
        } else {
            return '';
        }
        
    }, },
    { field: 'ui_code', headerName: "Unit", filter: 'agSetColumnFilter',},
    { field: 'location', headerName: "Location"},
    { field: 'warehouse_name', headerName: "Warehouse"},
    // { field: 'email_address', headerName: "Layer"},
    
    { field: 'received_date', headerName: "Date Recieved", filter: 'agDateColumnFilter', filterParams: filterParams,  suppressMenu: false, minWidth: 200,
        valueFormatter: function (params) {
            if(params.value != null) {
                return moment(params.value).format('D MMM YYYY');
            } else {
                return '-';
            }
            
        },
    },
    
    { field: 'lot_no', headerName: "Lot No", filter: 'agTextColumnFilter', },
    { field: 'expiry_date', headerName: "Expiry date", hide:true, filter: 'agDateColumnFilter', cellClass: 'text-center', filterParams: filterParams,  suppressMenu: false, minWidth: 200,
        valueFormatter: function (params) {
            if(params.value != null) {
                return moment(params.value).format('D MMM YYYY');
            } else {
                return '-';
            }
            
        },
    },
    { field: 'manufacture_date', headerName: "Mfg Date",  hide:true, cellClass: 'text-center', filter: 'agDateColumnFilter', filterParams: filterParams,  suppressMenu: false, minWidth: 200,
        valueFormatter: function (params) {
            if(params.value != null) {
                return moment(params.value).format('D MMM YYYY');
            } else {
                return '-';
            }
            
        },
    }, 
];

let gridApi;

const gridOptions = {
    columnDefs: columnDefs,
    autoSizeStrategy: {
        type: 'fitCellContents'
    },
    defaultColDef: {
    
        filter: true,
        floatingFilter: true,
        enableCellChangeFlash: true,
    },
    pagination: true,
    rowSelection: 'multiple',
    rowData: [],
    // onRowDoubleClicked: viewData
};
if (typeof page !== 'undefined') {
    if (page == 'create' || page == 'edit') {
        var gridDiv = document.querySelector('#myGrid');
        gridApi = agGrid.createGrid(gridDiv, gridOptions);
    }
}

$(document).ready(function () {
    $(".select").select2();

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
       // $('#show-items-list tbody tr').removeClass('selected')
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected')
        }
        else{
            $(this).addClass('selected');
        }
    });

    $('#do-table tbody').on('click', 'tr', function (e) {
        $('#do-table tbody tr').removeClass('selected')
        $(this).addClass('selected');
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
    var client_id = $('#client').val();
    var store_id = $('#store').val();

    if(client_id && store_id) {
        $('#show-items').modal('show');
        loadData();
    } else {
        if(client_id == ''){
            alert("Client Name required");
        }
        else{
            alert("Store Name required");
        }
    }
});

$(document).on('click', '.search-item', function() {
    $('#show-items').modal('show');
    if ($.fn.DataTable.isDataTable("#show-items-list")) {
        $('#show-items-list').DataTable().clear().destroy();
    }
    masterfile();
});

//search enter
$(document).on('keypress', '#product', function() {
    $('#show-items').modal('show');
    if ($.fn.DataTable.isDataTable("#show-items-list")) {
        $('#show-items-list').DataTable().clear().destroy();
    }
    masterfile();
});

function loadData() {
    var url = BASEURL + 'settings/aggrid/getAvailableStocks'

    var client_id = $('#client').val();
    var store_id = $('#store').val();
    var master_id = document.getElementsByName("master_id");
    var customer_id = $('#client').val();

    var master_id = $("input[name='master_id[]']")
              .map(function(){return $(this).val();}).get();
   
    $.ajax({
        type: 'POST',
        url: url,
        data:  {
            'client_id': client_id,
            'store_id': store_id,
            customer_id: customer_id,
            master_id: master_id,
            _token: $('input[name=_token]').val(),
        },
        dataType: 'json',
        beforeSend: function() {
           // $('#loadingModal').modal('show');
        //    scrambleAndRefreshAll(gridApi)
           gridApi.setGridOption('rowData', null)
        // gridApi.setRowData([]);
           gridApi.showLoadingOverlay();
        },
        success:function(data){
            // gridApi.setRowData(data);
            gridApi.setGridOption('rowData', data)
            // gridApi.setPinnedBottomRowData(getPinnedBottom(data));
            gridApi.hideOverlay();
           
        },
        statusCode: {
            200: function() {
                gridApi.hideOverlay();
            }
          }
    });
}

function scrambleAndRefreshAll(gridApi) {
    const params = {
      force: true,
      suppressFlash: true,
    };
    gridApi.refreshCells(params);
  }

function masterfile(){
    var company_id = $('#company').val();
    var client_id = $('#client').val();
    var store_id = $('#store').val();
    var warehouse_id = $('#warehouse').val();
    var product = $('#product').val();
    var item_type = $('#item_type').val();
    master = document.querySelectorAll('input[name="master_id[]"]');
    var master_id = [];
    master.forEach(input => {
        master_id.push(input.value);
    });

    new DataTable("#show-items-list",{
        order: [[1, 'asc'],[5,'asc']],
        paging: true,
        columnDefs : [
            { targets: [5], className: 'dt-body-right' },
        ],
        ajax: {
            url : BASEURL+"settings/getAvailableStocks",
            data : {
                company_id : company_id,
                customer_id : client_id,
                store_id : store_id,
                warehouse_id : warehouse_id,
                product : product,
                item_type : item_type,
                master_id : JSON.stringify(master_id)
            },
            dataSrc:""
        },
        columns: [
            { data: 'master_id',  visible: false },
            { data: 'product_code' },
            { data: 'product_name' },
            { data: 'received_date' },
            { data: 'item_type' },
            { data: 'inv_qty' , render: $.fn.dataTable.render.number( ',', '.', 2)},
            { data: 'ui_code' },
            { data: 'lot_no' },
            { data: 'expiry_date' },
            { data: 'manufacture_date' },
            { data: 'warehouse_name' },
            { data: 'location' },
        ],
    });
}

function removeProduct() {
    $(this).closest("tr").remove();
    toastr.error('Product removed');
  }

  $(document).on("click", ".remove-product", function () {
    removeProduct.call(this);
  });

$(document).on('click', '#add-product', function() {
    // var table = $('#show-items-list').DataTable();
    // var data = ( table.rows('.selected').data());

    const selectedNodes = gridApi.getSelectedNodes()
    if (selectedNodes.length > 0) {

        for(x=0; x< selectedNodes.length; x++) {
            var data = selectedNodes[x].data;

            // var uom = getUom();
            var rowCount = $('#product-list tr').length;

            if(page == 'edit') {
                rowCount = rowCount -1;
            }
            var idx = rowCount - 3;
            var itemType = (data.item_type == 'good') ? "bg-success" : "bg-danger";
            var btn = '<div class="text-center text-align-justify">';
            btn += '<button type="button" class="btn btn-danger mx-2 btn-icon waves-effect waves-light remove-product" data-id="'+(rowCount-1)+'"><i class="ri-delete-bin-5-fill"></i></button>';
            if(data.is_serialize == 1){
                btn += '<button type="button" class="add-serial btn btn-success btn-icon waves-effect waves-light" id="row_'+(rowCount-1)+'" data-rowid="'+(rowCount-1)+'" data-productid="'+ data.product_id +'" data-masterfileid="'+ data.masterfile_id +'" data-productcode="'+ data.product_code +'" data-productname="'+ data.product_name +'"><i class="ri-barcode-line"></i></button>';
            }
            btn += '</div>'

            $('#product-list tbody').prepend('<tr id="rows_'+(rowCount-1)+'"> \
            <td class="text-start"> \
                <input type="hidden" name="product_id[]" readonly id="product_id_'+data.product_id+'" value="'+data.product_id+'" /> \
                <input type="hidden" name="master_id[]" readonly id="master_id_'+data.master_id+'" value="'+data.master_id+'" /> \
                <input type="hidden" name="rcv_dtl_id[]" readonly id="rcv_dtl_id_'+data.rcv_dtl_id+'" value="'+data.rcv_dtl_id+'" /> \
                <input type="hidden" name="available_qty[]" readonly id="available_qty_'+data.inv_qty+'" value="'+data.inv_qty+'" /> \
                <input type="hidden" name="is_serialize[]" readonly value="'+data.is_serialize+'" />\
            '+(rowCount)+' </td> \
            <td class="text-start  fs-14"> \
                <small><b>'+((data.sap_code) ? data.sap_code : data.product_code)+'</b></small><br/>\
                '+data.product_name+'\
            </td> \
            <td class="text-center ps-1 fs-13"> \
                <span class="badge '+ itemType +' text-capitalize">'+data.item_type+'</span> \
            </td> \
            <td class="text-center  fs-14"> \
                '+((data.received_date) ? moment(data.received_date).format('D MMM YYYY') : '' )+'\
            </td> \
            <td class="text-end  fs-14"> \
                '+data.inv_qty.toFixed(0)+'\
            </td> \
            <td class="text-start fs-14"> \
                <input type="text" class="form-control text-end inv_qty numeric" name="inv_qty[]" data-qty="'+data.inv_qty+'" data-id="'+ idx +'" id="inv_qty'+(rowCount-1)+'" value="1" placeholder="Enter Qty" /> \
                <span class="text-danger error-msg inv_qty'+(rowCount-1)+'_error"></span> \
            </td> \
            <td class="text-start  fs-14"> \
                '+((data.ui_code) ? data.ui_code : '')+'\
                <input type="hidden" readonly class="form-control" name="inv_uom[]" data-id="'+data.inv_uom+'" id="inv_uom_'+(rowCount-1)+'" value="'+data.inv_uom+'"> \
            </td> \
            <td class="text-start  fs-14"> \
                '+((data.lot_no) ? data.lot_no : '')+'\
            </td> \
            <td class="text-start  fs-14 d-none"> \
                '+((data.expiry_date) ? moment(data.expiry_date).format('D MMM YYYY') : '') +'\
            </td> \
            <td class="text-start  fs-14  d-none"> \
                '+((data.manufacture_date) ? moment(data.manufacture_date).format('D MMM YYYY') : '') +'\
            </td> \
            <td class="text-start  fs-14"> \
                '+data.warehouse_name+'\
            </td> \
            <td class="text-start  fs-14"> \
                '+((data.location) ? data.location : '')+'\
            </td> \
            <td>'+btn+'</td> \
            </tr>');
            toastr.success(data.product_name + ' successfully added');

        }
        totalPackage();
    }

    $('#show-items-list tbody tr').removeClass('selected')
    $('#show-items').modal('hide');
});

function totalPackage(){
    var total = 0;
    $("#product-list tbody tr").each(function () {
        total += parseFloat($(this).find("input[name='inv_qty[]']").val());
    });
    $("#total").text(total.toFixed(2));
    $("#qty").val(total.toFixed(2));
}

$(document).on('blur', '.inv_qty', function() {
    var id = $(this).data('id');
    var stocks = $(this).data('qty');
    var new_inv = $(this).val();
    if(stocks < new_inv) {
        prefix = 'inv_qty'+id;
        $(this).val(stocks);
        $('#submit-withdrawal').find('span.'+prefix.replace('.','')+'_error').text('Insufficient Qty');
        $('.'+prefix+'_error').text('');
    }
    totalPackage();
});

$(document).on('click', '.submit-open', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-withdrawal"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'open');

    var serial_list = [];
    if(localStorage.length > 0){
        for (var i = 0; i < localStorage.length; i++) {
            var key = localStorage.key(i);
            var k = key.slice(0, 6);
            console.log(k);
            if(k !== undefined && k == 'serial'){
                var serial = localStorage.getItem(key);
                serial_list.push(JSON.parse(serial));
            }
        }
   }
    form_data.append("serial_list", JSON.stringify(serial_list));
    _submitData(form_data);
});

$(document).on('click', '.submit-posted', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-withdrawal"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'posted');
    var serial_list = [];
    if(localStorage.length > 0){
        for (var i = 0; i < localStorage.length; i++) {
            var key = localStorage.key(i);
            var k = key.slice(0, 6);
            if(k !== undefined && k == 'serial'){
                var serial = localStorage.getItem(key);
                serial_list.push(JSON.parse(serial));
            }
        }
    }
    form_data.append("serial_list", JSON.stringify(serial_list));
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
                url: BASEURL + 'withdraw/unpost',
                data: {
                    wd_no : $('#wd_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "post",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-withdrawal').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location = BASEURL+'withdraw';
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
                url: BASEURL + 'withdraw',
                data: {
                    wd_no : $('#wd_no').val(),
                    _token: $('input[name=_token]').val()
                },
                method: "DELETE",
                dataType: 'json',
                beforeSend: function () {
                    $('#preloading').modal('show');
                    $('#submit-withdrawal').find('span.error-msg').text('');
                },
                success: function (data) {
                    if($.isEmptyObject(data.errors)) {
                        if(data.success == true) {
                            toastr.success(data.message);
                            setTimeout(function () {
                                window.location = BASEURL+'withdraw';
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


$(document).on('click', '.create-withdrawal', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    setTimeout(function () {
        window.location = BASEURL+'withdraw/create';
    }, 300);
});

$(document).on('click', '.generate-picklist', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    var id = $('#wd_id').val();
    setTimeout(function () {
        window.location = BASEURL+'picklist/'+ id;
        $('#preloading').modal('hide');
    }, 300);
});

$(document).on('click', '.generate-withdrawal-slip', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    var id = $('#wd_id').val();
    setTimeout(function () {
        window.location = BASEURL+'withdrawalSlip/'+ id;
        $('#preloading').modal('hide');
    }, 300);
});

$(document).on('click', '.submit-withdrawal', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-withdrawal"));
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
        url: BASEURL + 'withdraw',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-withdrawal').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'withdraw/'+data.id+'/edit';
						}, 300);
                    } else {
                        localStorage.clear();
                        toastr.success(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'withdraw';
						}, 300);
                    }
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-withdrawal').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
}

$(document).on('click', '.add-serial', function (e) {
    $("#serial-list").empty();
    $("#counter").text(0);
    $("#show-serial").modal('show');

    var productid = $(this).data('productid');
    var masterfileid = $(this).data('masterfileid');
    var rowid = $(this).data('rowid');
    var itemize = $(this).data('itemize');
    var item = itemize;


    var productname = $(this).data('productname');
    var productcode = $(this).data('productcode');
    var qty = $('#inv_qty_'+rowid).val();

    $("#product_code").val(productcode + " - "+ productname);
    $("#product_qty").val(qty);
    $("#productid").val(productid);
    $("#masterfileid").val(masterfileid);

    var type = $(this).data('type');

    $("#serial_no").focus();
    var store = "serial_" + masterfileid + "_" + productid;
    var serial_list = localStorage.getItem(store);
    var storedSerialList = JSON.parse(serial_list);
    if(storedSerialList != null){
        storedSerialList.forEach(function(val){
            addRow(val.serial_no,val.warranty_no,type);
        });
    }
    else{
         if(item != null){
            item.forEach(function(val){
                addRow(val.serial_no,val.warranty_no,type);
            });
        }
    }
});

$(document).on('change','#toggle',function(e){
    $("#serial_no").focus();
});
$(document).on('change','#serial_no',function(e){
    var serial_no = $(this).val().trim();
    var checkbox = document.getElementById("toggle");
    var toggle = (checkbox.checked) ? 1 : 0;

    if(toggle == 1){
        $("#warranty_no").focus();
    }
    else{
        if (serial_no !== "") {
            const existingSerials = $("#serial-list td:nth-child(1)").map(function() {
                return $(this).find("input[name=serial_no]").val().trim();
            }).get();

            if (existingSerials.includes(serial_no)) {
                toastr.error('Serial No. already exists');
                return;
            }
            addRow(serial_no,"");
            $(this).focus();
            $(this).val('');
        }
    }
});

$(document).on('change','#warranty_no',function(e){
    var warranty_no = $(this).val().trim();
    var serial_no = $("#serial_no").val().trim();
    if($("#serial_no").length)
    if (serial_no !== "" && warranty_no != "") {
        const existingSerials = $("#serial-list td:nth-child(1)").map(function() {
            return $(this).find("input[name=serial_no]").val().trim();
        }).get();
        if (existingSerials.includes(serial_no)) {
            toastr.error('Serial No. already exists');
            return;
        }

        addRow(serial_no,warranty_no);
        $("#serial_no").focus();
        $("#serial_no").val('');
        $(this).val('');
    }
});

function addRow(s,w, type="edit") {
    const tbody = document.querySelector('#serial-list');
    const rows = tbody.querySelectorAll('tr');;
    const rowCount = rows.length;
    $("#counter").text(rowCount + 1);
    var newRow = `<tr>
        <td>
            <input type="text" class="form-control" placeholder="Serial No" name="serial_no" value="`+ s +`">
        </td>
        <td>
            <input type="text" class="form-control" placeholder="Warranty No" name="warranty_no" value="`+ w +`">
        </td>
        <td class="`+ ((type == 'view') ? 'd-none' : '')+`">
            <button type="submit" class="btn btn-icon btn-danger remove-row mx-2 waves-effect waves-light"><i class="ri-delete-bin-5-fill label-icon align-middle"></i>
                </button>
        </td>
    </tr>`;
    $("#show-serial-list tbody").prepend(newRow);
  }

  function removeRow() {
    $(this).closest("tr").remove();
    const tbody = document.querySelector('#serial-list');
    const rows = tbody.querySelectorAll('tr');;
    const rowCount = rows.length;
    $("#counter").text(rowCount);
    toastr.error('serial deleted');
  }

  $(document).on("click", ".remove-row", function () {
    removeRow.call(this);
  });

$(document).on("click","#save-serial",function(){
    var serial_list = [];
    var master_id = $("#masterfileid").val();
    var prod_id = $("#productid").val();
    var store = "serial_" + master_id + "_" + prod_id;
    localStorage.removeItem(store);
    $("#show-serial-list tbody tr").each(function () {
        serial_list.push({
          serial_no: $(this).find("input[name=serial_no]").val(),
          warranty_no: $(this).find("input[name=warranty_no]").val(),
          masterfile_id: master_id,
          product_id: prod_id
        });
    });

    const uniqueCombinations = {};
    const filteredArray = serial_list.filter(function(obj){
        const combination = `${obj.masterfile_id}-${obj.product_id}-${obj.serial_no}`;
        if (!uniqueCombinations[combination]) {
            uniqueCombinations[combination] = true;
            return true;
        }
        return false;
    });
    localStorage.setItem(store, JSON.stringify(filteredArray));
    $("#serial-list").empty();
    $("#serial_no").val('');
    $("#warranty_no").val('');
    toastr.success('Serial added');
    $("#show-serial").modal('hide');
});

$(document).on("click","#upload-serial",function(){
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    if (file) {
        // Check if the selected file type is supported (CSV)
        if (file.type === 'text/csv') {
            uploadFile(file);
        } else {
            alert('Please select a valid CSV file.');
        }
    } else {
        alert('Please select a file to upload.');
    }
});

function uploadFile(file) {
    if (file) {
        Papa.parse(file, {
            complete: function(results) {
                const serial = results.data.filter(row => row.length > 0 && row.every(cell => cell !== ""));
                serial.forEach(function(val,index){
                    if(index > 0 ){
                        addRow(val[0],val[1]);
                    }
                });
            }
        });
    }
}

$(document).on("click","#download-template",function(){
    const csvData = [
        ['SERIAL', 'WARRANTY']
    ];
    const csvContent = csvData.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'serial-template.csv';
    a.click();
    URL.revokeObjectURL(url);
});

$(document).on('click', '.withdraw-do', function (e) {
    e.preventDefault();
    $('#do_num_holder').val('');
    $('#show-do').modal('show');

    if ($.fn.DataTable.isDataTable("#unit-allocation")) {
        $('#do-table').DataTable().clear().destroy();
    }
    new DataTable("#do-table",{
        paging: true,
        ajax: BASEURL+"settings/getAllPostedDO",
        columns: [
            { data: 'do_no' },
            { data: 'order_date' },
            { data: 'order_no' },
            { data: 'customer_name'},
            { data: 'deliver_to' },
            { data: 'created_by' }
        ],
    });
});

$(document).on('click', '#withdraw-do-btn', function (e) {
    e.preventDefault();
    var table = $('#do-table').DataTable();
    var data = ( table.rows('.selected').data()[0]);
    if (table.rows('.selected').data().length > 0) {
        $('#preloading').modal('show');
        rec = _encode(data.id);
        setTimeout(function () {
            window.location = BASEURL+'withdraw/'+rec.responseText+'/create';
        }, 300);
    } else {
        alert("Please select a DO Number");
    }
});


