$(document).ready(function() {
    var sub = $("#category").data('sub-category');
    var sub_category = [];
    if(sub)
    {
        $(".d-show").removeClass("d-none");
       sub.forEach(function(val){
        sub_category.push({
            category_name: val.category_name,
            category_id: val.category_id
        })
       });
        viewCategory();
    }
    else{
        $(".d-show").addClass("d-none");
    }

    $('#add').on('click', function() {
        var category = $('#sub_category').val();
        if(category != ''){
            addCategory();
        }
    });

    function addCategory(){
        var category = $('#sub_category').val();
        populateStore(category, '', '');

        var dataObject = {
            category_name: category,
            category_id : null
        };
        sub_category.push(dataObject);

        $(".d-show").removeClass("d-none");
        $('#sub_category').val('');
        viewCategory()
    }

    function viewCategory(){
        var display = $('#category');
        display.empty();
        var dataHtml = '<ul class="list-group list-group-flush mb-0">';
            sub_category.forEach(function(data, index) {
            dataHtml += '<li class="list-group-item" data-id="1">' +
              '<div class="d-flex">' +
              '<div class="flex-grow-1">' +
              '<h5 class="fs-13 mb-1"><a href="#" class="link name text-dark">' + data.category_name + '</a></h5>' +
              '</div>' +
              '<a href="#" class="delete-icon text-danger" data-id="'+ index +'"><i class="ri-delete-bin-line"></i></a>' +
              '</div>' +
              '</li>';
          });

          dataHtml += '</ul>' ;
       display.append(dataHtml);
    }

    function removeItemFromArray(index) {
        sub_category.splice(index, 1);
        viewCategory();
      }

    $('#category').on('click', '.delete-icon', function() {
    var index = $(this).data('id');
    removeItemFromArray(index);
    });


    $('.submit-category').on('click', function (e) {
        e.preventDefault();
        var id = $('#id').val();
        var selectedBrands = $('#brand_id').val();

        var form_data = new FormData(document.getElementById("form-category"));
        form_data.append("_token", $('input[name=_token]').val());
        form_data.append("sub_categories", JSON.stringify(sub_category));
        form_data.append("brand_id", JSON.stringify(selectedBrands));
        form_data.append("id", id);

        $.ajax({
            url: BASEURL + 'maintenance/category',
            method: "POST",
            data: form_data,
            processData: false,
            dataType: 'json',
            contentType: false,
            beforeSend: function () {
                $('#error-handling ul').html('');
                $('#error-handling').addClass('d-none');
                $('#preloading').modal('show');
            },
            success: function (data) {
                if($.isEmptyObject(data.error)) {
                    if(data.success == true) {
                        setTimeout(function(){
                            window.location= BASEURL + 'maintenance/category/'+data.id+'/edit';
                        }, 300);
                        toastr.success(data.message);

                    } else {
                        toastr.error(data.message,'Error on saving');
                    }
                } else {
                    $('#error-handling').removeClass('d-none');

                    $.each(data.error, function(prefix, val) {
                        $('#error-handling ul').append('<li>'+val+'</li>');
                    });
                }
            },
            complete: function() {
                $('#preloading').modal('hide');
            }
        });
    });
});

$(document).on('change', '#parent_id', function() {
    var parent_id = $(this).val();
    populateStore(parent_id, '', '');
});


