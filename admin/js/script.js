const c_location = location.href;
let c_link = c_location.split("/").pop();
const menu_links = $(".sidebar .nav .nav-link");
menu_links.parent().removeClass('active');
Array.from(menu_links).forEach((link) => {
    let href = $(link).attr("href").split("/").pop();
    if (href === c_link) {
        if ($(link).parents(".dropdown").length > 0) {
            $(link).parents(".dropdown").addClass('active');
            $(link).parent().parent().show();
        }
        $(link).parent().addClass("active");
    } else {
        $(link).parent().removeClass('active');
    }
});
controllers.admin = 'controllers/admin';
// Change User Profile Picture
$('.sidebar .user-info .user-img-file').on('change', function(){
    let file = this.files;
    if(file.length < 1) return false;
    file = file[0];
    if(!isImageFile(file)){
        sAlert('Invalid File type. Please upload an image file', 'error');
        return false;
    }
    let image = URL.createObjectURL(file),
        parent = $(this).parents('.user-image-container');
    parent.find('.user-img').attr('src', image);
    parent.addClass('active');
});
$('.sidebar .user-info .save-img').on('click', function(){
    let imagecontainer  = $(this).parent().find('.user-image-container'),
        file = imagecontainer.find('.user-img-file').get(0).files,
        btn = $(this),
        btnText = $(this).html();
    if(file.length < 1){
        imagecontainer.removeClass('active');
        return false;
    }
    file = file[0];
    if(!isImageFile){
        sAlert('Invalid File type. Please upload an image file', 'error');
        return false;
    }
    let formData = new FormData();
    formData.append('changeUserImage', file);
    $.ajax({
        url: controllers.admin,
        type: 'POST',
        data: formData,
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function(){
            disableBtn(btn);
        },
        success: function(response){
            enableBtn(btn, btnText);
            imagecontainer.removeClass('active');
            sAlert('Image Changed Successfully!', 'success');
        },
        error: function(){
            enableBtn(btn, btnText);
            makeError();
        }
    })
});
// Delete Data
$(document).on('click', '.deleteData', function(e){
    e.preventDefault();
    let action = $(this).attr('data-action'),
        target = $(this).attr('data-target'),
        parent = $(this).parents('tr').first();
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.value) {
        $.post('controllers/admin.php', {
            deleteData: true,
            action: action,
            target: target
        }).done(function(response){
            if(!isJson(response)){
                makeError();
            } else {
                response = JSON.parse(response);
                sAlert(response.data, response.status);
                parent.remove();
            }
        }).fail(makeError());
      }
    });
});