$(document).ready(function () {
    $(".select").select2();
    var plate_no = $("#plate_no").val();
    if(plate_no.length){
        var trucker = populatePlateNo(plate_no);
        $("#trucker_name").val(trucker[0].trucker_name);
        $("#truck_type").val(trucker[0].vehicle_desc);
    }
    $('#show-withdrawal-list tbody').on('click', 'tr', function (e) {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected')
        }
        else{
            $(this).addClass('selected');
        }
    });
});

$(document).on('change', '#plate_no', function() {
    var plate_no = $(this).val();
    var trucker = populatePlateNo(plate_no);
    $("#trucker_name").val(trucker[0].trucker_name);
    $("#truck_type").val(trucker[0].vehicle_desc);
});

$(document).on('click', '.create-dispatch', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    setTimeout(function () {
        window.location = BASEURL+'dispatch/create';
    }, 300);
});

$(document).on('click', '#find-withdrawal', function() {
    $('#show-withdrawal').modal('show');
    if ($.fn.DataTable.isDataTable("#show-withdrawal-list")) {
        $('#show-withdrawal-list').DataTable().clear().destroy();
    }
    withdrawal();
});

$(document).on('click', '#search-withdrawal', function() {
    if ($.fn.DataTable.isDataTable("#show-withdrawal-list")) {
        $('#show-withdrawal-list').DataTable().clear().destroy();
    }
    withdrawal();
});

function withdrawal(){
    var keyword = $("#keyword").val();
    var wd_list = document.querySelectorAll('input[name="wd_no[]"]');
    var wd_no = [];
    wd_list.forEach(input => {
        wd_no.push(input.value);
    });

    new DataTable("#show-withdrawal-list",{
        order: [[1, 'asc'],[4,'asc']],
        paging: true,
        columnDefs : [
            { targets: [4], className: 'dt-body-right' },
        ],
        ajax: {
            url : BASEURL+"settings/withdrawal_list",
            data : {
                keyword : keyword,
                status : "posted",
                wd_no : JSON.stringify(wd_no),
            },
            dataSrc:""
        },
        columns: [
            { data: 'id',  visible: false },
            { data: 'wd_no' },
            { data: 'client_name' },
            { data: 'deliver_to' },
            { data: 'no_of_package' , render: $.fn.dataTable.render.number( ',', '.', 2), class : 'text-center'},
            { data: 'order_no' },
            { data: 'order_date' },
            { data: 'dr_no' },
            { data: 'po_num' },
            { data: 'sales_invoice' },
        ],
    });
}

$(document).on("click", ".remove-withdrawal", function () {
    removeWithdrawal.call(this);
});

function removeWithdrawal() {
    $(this).closest("tr").remove();
    toastr.error('Withdrawal removed');
}

$(document).on('click', '#add-withdrawal', function() {
    var table = $('#show-withdrawal-list').DataTable();
    var data = ( table.rows('.selected').data());

    if(data.length > 0) {
        for(x=0; x<data.length; x++) {
            var rowCount = $('#withdrawal-list tr').length;
            var idx = rowCount - 3;
            var btn = '<div class="text-center text-align-justify">';
            btn += '<button type="button" class="btn btn-danger mx-2 btn-icon waves-effect waves-light remove-withdrawal" data-id="'+(rowCount-1)+'"><i class="ri-delete-bin-5-fill"></i></button>';
            btn += '</div>'

            $('#withdrawal-list tbody').append('<tr id="rows_'+(rowCount-1)+'"> \
            <td class="text-start"> \
                <input type="hidden" name="wd_no[]" value="'+data[x].wd_no+'" /> \
                <input type="hidden" name="wd_qty[]" value="'+data[x].no_of_package+'" /> \
            '+(rowCount-1)+' </td> \
            <td class="text-start  fs-14"> \
                '+data[x].wd_no+'\
            </td> \
            <td class="text-start fs-14"> \
                '+ data[x].deliver_to +' \
            </td> \
            <td class="text-center  fs-14"> \
                '+data[x].no_of_package+'\
            </td> \
            <td class="text-start  fs-14"> \
                '+data[x].order_no+'\
            </td> \
            <td class="text-start  fs-14"> \
                '+data[x].order_date+'\
            </td> \
            <td class="text-start  fs-14"> \
                '+data[x].dr_no+'\
            </td> \
            <td class="text-start  fs-14"> \
                '+data[x].po_num+'\
            </td> \
            <td class="text-start  fs-14"> \
                '+data[x].sales_invoice+'\
            </td> \
            <td>'+btn+'</td> \
            </tr>');
            toastr.success(data[x].wd_no + ' successfully added');
        }
        totalPackage();
    }

    $('#show-withdrawal-list tbody tr').removeClass('selected')
    $('#show-withdrawal').modal('hide');
});

function totalPackage(){
    var total = 0;
    $("#withdrawal-list tbody tr").each(function () {
        total += parseFloat($(this).find("input[name='wd_qty[]']").val());
    });
    $("#total").text(total.toFixed(2));
    $("#qty").val(total.toFixed(2));
}

$(document).on('click', '.submit-open', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-dispatch"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'open');
     _submitData(form_data);
});

$(document).on('click', '.generate-deliveryslip', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    var id = $('#dispatch_id').val();
    setTimeout(function () {
        window.location = BASEURL+'deliverySlip/'+ id;
        $('#preloading').modal('hide');
    }, 300);
});

$(document).on('click', '.submit-posted', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-dispatch"));
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
        url: BASEURL + 'dispatch',
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
							window.location = BASEURL+'dispatch/'+data.id+'/edit';
						}, 300);
                    } else {
                        localStorage.clear();
                        toastr.success(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'dispatch';
						}, 300);
                    }
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-dispatch').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
}
