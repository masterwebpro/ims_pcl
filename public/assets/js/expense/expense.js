$(document).ready(function () {
    $(".select").select2();
    var plate_no = $("#plate_no").val();
    if(plate_no.length){
        var trucker = populatePlateNo(plate_no);
        $("#trucker_name").val(trucker[0].trucker_name);
        $("#truck_type").val(trucker[0].vehicle_desc);
    }
    $('#show-dispatch-list tbody').on('click', 'tr', function (e) {
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected')
        }
        else{
            $(this).addClass('selected');
        }
    });

    $('#show-particular-list tbody').on('click', 'tr', function (e) {
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

$(document).on('click', '.create-expense', function (e) {
    e.preventDefault();
    $('#preloading').modal('show');
    setTimeout(function () {
        window.location = BASEURL+'expense/create';
    }, 300);
});

$(document).on('click', '#find-dispatch', function() {
    var plate_no = $("#plate_no").val();
    if(plate_no){
        $('#show-dispatch').modal('show');
        if ($.fn.DataTable.isDataTable("#show-dispatch-list")) {
            $('#show-dispatch-list').DataTable().clear().destroy();
        }

        var dispatch_list = document.querySelectorAll('input[name="dispatch_no[]"]');
        var dispatch_no = [];
        dispatch_list.forEach(input => {
            dispatch_no.push(input.value);
        });
        new DataTable("#show-dispatch-list",{
            order: [[1, 'asc'],[1,'asc']],
            paging: true,
            ajax: {
                url : BASEURL+"settings/getAllPostedDispatch",
                data : {
                    dispatch_no : JSON.stringify(dispatch_no),
                    plate_no : plate_no
                },
                dataSrc:""
            },
            columns: [
                { data: 'id',  visible: false },
                { data: 'dispatch_no' },
                { data: 'dispatch_date' }
            ],
        });
    }
    else{
        alert("Plate no required");
    }
});

$(document).on('click', '#search-dispatch', function() {
    if ($.fn.DataTable.isDataTable("#show-dispatch-list")) {
        $('#show-dispatch-list').DataTable().clear().destroy();
    }
});


$(document).on("click", ".remove-dispatch", function () {
    removeDispatch.call(this);
});

function removeDispatch() {
    $(this).closest("tr").remove();
    toastr.error('Dispatch removed');
}

$(document).on('click', '#add-dispatch', function() {
    var table = $('#show-dispatch-list').DataTable();
    var data = ( table.rows('.selected').data());

    if(data.length > 0) {
        for(x=0; x<data.length; x++) {
            var rowCount = $('#dispatch-list tr').length;
            var idx = rowCount - 3;
            var btn = '<div class="text-center text-align-justify">';
            btn += '<button type="button" class="btn btn-danger mx-2 waves-effect waves-light remove-dispatch" data-id="'+(rowCount-1)+'"><i class="ri-delete-bin-5-fill"></i> Remove</button>';
            btn += '</div>'

            $('#dispatch-list tbody').append('<tr id="rows_'+(rowCount-1)+'"> \
            <td class="text-start  fs-14"> \
                <input type="hidden" name="dispatch_no[]" value="'+data[x].dispatch_no+'" /> \
                '+data[x].dispatch_no+'\
            </td> \
            <td class="text-start fs-14"> \
                <input type="hidden" name="dispatch_date[]" value="'+data[x].dispatch_date+'" /> \
                '+ data[x].dispatch_date +' \
            </td> \
            <td>'+btn+'</td> \
            </tr>');
            toastr.success(data[x].dispatch_no + ' successfully added');
        }
    }

    $('#show-dispatch-list tbody tr').removeClass('selected')
    $('#show-dispatch').modal('hide');
});

$(document).on('click', '.submit-open', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-expense"));
    form_data.append("_token", $('input[name=_token]').val());
    form_data.append("status", 'open');
     _submitData(form_data);
});


$(document).on('click', '.submit-posted', function (e) {
    e.preventDefault();

    var form_data = new FormData(document.getElementById("submit-expense"));
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
        url: BASEURL + 'expense',
        method: "POST",
        data: form_data,
        processData: false,
        dataType: 'json',
        contentType: false,
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#submit-expense').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                if(data.success == true) {
                    if(data.data.status == 'open') {
                        showSuccess(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'expense/'+data.id+'/edit';
						}, 300);
                    } else {
                        localStorage.clear();
                        toastr.success(data.message);
                        setTimeout(function () {
							window.location = BASEURL+'expense';
						}, 300);
                    }
                } else {
                    toastr.error(data.message,'Error on saving');
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#submit-expense').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });
}

function addRow(val = "") {
    var rows = $("#particular-list").find("tr").length;
    rowCount = (rows == 0) ? 0 : rows - 1;
    var newRow = `<tr>
        <td>
            <input type="text" class="form-control" id="particular"
                name="particular[]" placeholder="Enter particular">
            <span class="text-danger error-msg particular`+(rowCount)+`_error"></span>

        </td>
        <td>
            <input type="text" class="form-control numeric" id="amount"
                name="amount[]" placeholder="Enter amount">
            <span class="text-danger error-msg amount`+(rowCount)+`_error"></span>

        </td>
        <td>
            <div class="text-center">
                <button type="button" class="remove-row btn btn-danger remove-truck mx-2 waves-effect waves-light">
                    <i class="ri-delete-bin-5-fill"></i> Remove
                </button>
            </div>
        </td>
    </tr>`;
    $("#particular-list tbody").append(newRow);
  }

  function removeRow() {
    $(this).closest("tr").remove();
  }

  $("#add-row").on("click", function () {
    addRow();
  });

  $(document).on("click", ".remove-row", function () {
    removeRow.call(this);
  });

  $(document).on('click', '#select-particular', function() {
    $('#show-particular').modal('show');
    if ($.fn.DataTable.isDataTable("#show-particular-list")) {
        $('#show-particular-list').DataTable().clear().destroy();
    }

    new DataTable("#show-particular-list",{
        order: [[1, 'asc'],[3,'asc']],
        paging: true,
        ajax: {
            url : BASEURL+"settings/getParticulars",
            dataSrc:""
        },
        columns: [
            { data: 'particular_id',  visible: false },
            { data: 'code' },
            { data: 'name' },
            { data: 'amount' },
        ],
    });
});

$(document).on('click', '#add-selected-particular', function() {
    var table = $('#show-particular-list').DataTable();
    var data = ( table.rows('.selected').data());

    if(data.length > 0) {
        for(x=0; x<data.length; x++) {
            var rowCount = $('#particular-list tr').length;
            var idx = rowCount - 3;
            var btn = '<div class="text-center text-align-justify">';
            btn += '<button type="button" class="btn btn-danger mx-2 waves-effect waves-light remove-row" data-id="'+(rowCount-1)+'"><i class="ri-delete-bin-5-fill"></i> Remove</button>';
            btn += '</div>'

            $('#particular-list tbody').append('<tr id="rows_'+(rowCount-1)+'">\
            <td class="text-start  fs-14"> \
                <input type="text" class="form-control" id="particular'+(rowCount-1)+'" name="particular[]" value="'+ data[x].name +'" placeholder="Enter particular"/>\
                <span class="text-danger error-msg amount'+(rowCount)+'_error"></span>\
            </td> \
            <td class="text-start fs-14"> \
                <input type="text" class="form-control numeric" id="amount'+(rowCount-1)+'" name="amount[]" value="'+ data[x].amount +'" placeholder="Enter amount"/>\
                <span class="text-danger error-msg amount'+(rowCount)+'_error"></span>\
            </td> \
            <td>'+btn+'</td> \
            </tr>');
            toastr.success(data[x].name + ' successfully added');
        }
    }

    $('#show-particular-list tbody tr').removeClass('selected')
    $('#show-particular').modal('hide');
});
