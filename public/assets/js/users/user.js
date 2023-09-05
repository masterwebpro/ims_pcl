
$(document).on('click', '#user-save', function (e) {
    e.preventDefault();

    // let role_id             = $('#role_id').val()
    let user_id             = $('#user_id').val();
    let first_name          = $('#first_name').val();
    let middle_name         = $('#middle_name').val();
    let last_name           = $('#last_name').val();
    let email_address       = $('#email_address').val();
    let mobile_no           = $('#mobile_no').val();
    let company             = $('#company').val();
    let is_active           = $("#is_active").is(":checked") ? 1 : 0;
    let department          = $('#department').val();
    let position            = $('#position').val();
    let reported_to         = $('#reported_to').val();
    let image_path          = $('#image_path').val();
    let is_edit_password    = $('#is_edit_password').is(':checked') ? 1 : 0 ;

    let password        = $('#password').val();
    let password_confirmation    = $('#password_confirmation').val();

    //permission
    var arr = [];
    $.each($(".perm:checked"), function(){
        arr.push($(this).val());
    });
    
    $.ajax({
        url: BASEURL + 'users',
        method: "POST",
        data: {
            user_id : user_id,
            first_name : first_name,
            middle_name : middle_name,
            last_name : last_name,
            email_address : email_address,
            mobile_no : mobile_no,
            company : company,
            is_active : is_active,
            department : department,
            position : position,
            reported_to : reported_to,
            image_path: image_path,
            password: password,
            is_edit_password: is_edit_password,
            menu_access: arr,
            password_confirmation: password_confirmation,
            _token: $('input[name=_token]').val()
        },
      
        dataType: 'json',
        beforeSend: function () {
            $('#preloading').modal('show');
            $('#form-user').find('span.error-msg').text('');
        },
        success: function (data) {
            if($.isEmptyObject(data.errors)) {
                console.log(data.data);
                if(data.success == true) {
                   showSuccess(data.message);
                   toastr.success(data.message);
                    
                } else {
                    toastr.error(data.message,'Error on saving'); 
                }
            } else {
                $.each(data.errors, function(prefix, val) {
                    $('#errMsg').removeClass('d-none');
                    $('#form-user').find('span.'+prefix.replace('.','')+'_error').text(val);
                });
                toastr.error('Some fields are required');
            }
        },
        complete: function() {
           $('#preloading').modal('hide');
		}
    });

});

// $('#is_edit_password').trigger('click');
$('#is_edit_password').on('change', function() {
    if( $(this).is(':checked') ){
        $('#password_row').removeClass('d-none');
        $('input[type=checkbox]').attr('checked',true);
    }
    else{
        $('#password_row').addClass('d-none');
    }
});

$('.perm').on('change', function() {
    id = $(this).data('id');

    if( $(this).is(':checked') ){
        $('.submenu_'+id).attr('checked','checked');
    }
    else{
        $('.submenu_'+id).removeAttr('checked');
    }
});



// $(document).on('click', '#submit', function (e) {
//     e.preventDefault();
//     saveUser();
// });

// $.ajaxSetup({
//     headers: {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//     }
// });

// // $('#reported_to').select2();

// function saveUser() {

//     let is_passed           = true
//     let _token              = $('#_csrf_token').val()

//     let role_id             = $('#role_id').val()
//     let user_id             = $('#user_id').val()
//     let first_name          = $('#first_name').val()
//     let middle_name         = $('#middle_name').val()
//     let last_name           = $('#last_name').val()
//     let email               = $('#email').val()
//     let mobile_no           = $('#mobile_no').val()
//     let company             = $('#company').val()
//     let is_active           = $("#is_active").is(":checked");
//     let department          = $('#department').val()
//     let position            = $('#position').val()
//     let reported_to         = $('#reported_to').val()
//     let image_path         = $('#image_path').val()

//     let username            = $('#name').val()
//     let new_password        = $('#new_password').val()
//     let confirm_password    = $('#confirm_password').val()

//     let perm_access         = $('.perm_checkbox:checkbox:checked')

//     let role_name           = $('#role_id').find(":selected").text();
//     let perm_list           = []
//     $.each(perm_access, function(key, value) {
//         perm_list.push($(value).attr('data-id'));
//         perm_list.push($(value).attr('data-parent-name'));
//     });

//     if (new_password !== confirm_password) {
//         toastr.error('<b>Password</b> and <b>Confirm Password</b> does not match!');
//         is_passed = false
//     }
//     perm_list = [...new Set(perm_list)];

//     if (is_passed) {
//         let data = {
//             _token,
//             user_id,
//             role_id,
//             first_name,
//             middle_name,
//             last_name,
//             email,
//             mobile_no,
//             is_active,
//             company,
//             department,
//             position,
//             reported_to,
//             username,
//             new_password,
//             perm_list,
//             role_name,
//             image_path
//         }
//         console.log(data)
//         $.ajax({
//             url: BASEURL + "users/save",
//             method: "post",
//             data: data,
//             dataType: 'json',
//             beforeSend: function () {
//                 $('span.error-msg').text('');
//             },
//             success: function (data) {
//                 if ($.isEmptyObject(data.error)) {
//                     if (data.success == true) {
//                         showSuccess(data.msg)
//                         setTimeout(function () {
//                             window.location = BASEURL + 'users';
//                         }, 200);
//                     } else {
//                         toastr.error(data.msg, 'Error on saving');
//                     }
//                 } else {
//                     $.each(data.error, function (prefix, val) {
//                         console.log(prefix)
//                         $(`#${prefix}`).addClass("is-invalid")
//                         $(`.${prefix}_error`).text(val);

//                         toastr.error(`${titleCase(prefix)} is required`, 'Error on saving');
//                     });
//                 }

//             }
//         });
//     }
// }

const titleCase = (s) =>
  s.replace(/^_*(.)|_+(.)/g, (s, c, d) => c ? c.toUpperCase() : ' ' + d.toUpperCase())


// password_row


if($('#old_role_name').val()){
    let name = $('#old_role_name').val();
    get_selected_perm_per_role(name);
}

$('#role_id').on('change', function() {
    let role_name = $(this).find(":selected").text();
    get_selected_perm_per_role(role_name);
});

function get_selected_perm_per_role(name) {
    $(`.perm_checkbox`).prop('checked', false);
    $.ajax({
        url: BASEURL + 'settings/role/perm_role/'+name,
        method: 'get',
        data: {},
        dataType: 'json',
        success: function(response){
            // Remove options
            console.log(response)
            $.each(response,function(index,data){
                $(`[data-id="${data}"]`).prop('checked', true);
            })
        }
    });
  }


let readURL = function(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('.profilepic-image').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}


$(".file_upload").on('change', function(){
    readURL(this);
    uploadImageS3(this);
});

$(".upload-button").on('click', function() {
    $(".file_upload").click();
});
$(".profilepic-content").on('click', function() {
    $(".file_upload").click();
});


let uploadImageS3 = (d)=>{
    let fd = new FormData(document.getElementById("avatar_user_upload"));
    console.log($(d))
    $.ajax({
        url: BASEURL + 'users/image-upload',
        type: 'post',
        data: fd,
        contentType: false,
        processData: false,
        success: function(response){
            if(response.success == true){
                $("#img").attr("src",response);
                $(".preview img").show(); // Display image element
                $('#image_path').val(response.data)
                toastr.info('Image Uploaded');
            }else{
                console.log(response)
                toastr.error("Oops, something went wrong.", 'We have encountered unexpected problems.', {
                    closeButton: true,
                    progressBar: true,
                    newestOnTop: false,
                    showDuration: '200',
                    hideDuration: '200',
                    timeOut: '5000',
                  });
            }
        },
        error: function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
            console.log(status)
            console.log(error)
            if(status == "error"){
                toastr.error("Try waiting a few minutes, then upload again.", 'We have encountered unexpected problems.', {
                    closeButton: true,
                    progressBar: true,
                    newestOnTop: false,
                    showDuration: '200',
                    hideDuration: '200',
                    timeOut: '5000',
                  });
            }
        }
    });
};