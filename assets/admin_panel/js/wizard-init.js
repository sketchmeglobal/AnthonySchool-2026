/**
 * Created by mosaddek on 3/5/15.
 */

$(document).ready(function () {

    // var formB = $("#basic-form");
    // formB.children("div").steps({
    //     headerTag: "h3",
    //     bodyTag: "section",
    //     transitionEffect: "slideLeft"
    //
    // });


    var form = $("#change_pass_form");
    form.validate({
        errorPlacement: function errorPlacement(error, element) {
            element.after(error);
        }
    });
    form.children("div").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slideLeft",
        onStepChanging: function (event, currentIndex, newIndex) {
            // Allways allow previous action even if the current form is not valid!
            if (currentIndex > newIndex) {
                return true;
            }
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ":disabled";
            return form.valid();
        },
        onFinished: function (event, currentIndex) {
            form.submit();
            // alert("Submitted!");
        }
    });


});