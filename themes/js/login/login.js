$(document).ready(function() {

    $("html").addClass("loginPage");

    $("#login").toggle('fast');

    var wrapper = $(".login-wrapper");
    var barBtn = $("#bar .btn");

    //change the tabs
    barBtn.click(function() {
        var btnId = $(this).attr('id');
        wrapper.attr("data-active", btnId);
        $("#bar").attr("data-active", btnId);
    });

});