// wait for the DOM to be loaded
$(document).ready(function() {

    function notification(obj) {
        toastr[obj.type](obj.msg, obj.title, {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "15000",
            "extendedTimeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        })
    }

    //submit basic_info_form without refreshing page
    $('#basic_info_form').ajaxForm(function(data) {
        var obj = JSON.parse(data);
        $('#file').val('');
        $('.fullname').text(obj.fullname);
        $('.lastname').text(obj.lastname);
        if(obj.img) {
            $('.profile_img').attr('src', base_url+'assets/admin_panel/img/profile_img/'+obj.img);
        }
        notification(obj);
    });

    //submit change_pass_form without refreshing page
    $('#change_pass_form').ajaxForm(function(data) {
        var obj = JSON.parse(data);
        $('#current_pass').val('');
        $('#new_pass').val('');
        $('#confirm_pass').val('');
        notification(obj);
    });

    //submit change_email_form without refreshing page
    $('#change_email_form').ajaxForm(function(data) {
        var obj = JSON.parse(data);
        $('#new_email').val('');
        notification(obj);
    });

    //submit change_username_form without refreshing page
    $('#change_username_form').ajaxForm(function(data) {
        var obj = JSON.parse(data);
        $('.username').text(obj.new_username);
        notification(obj);
    });

});