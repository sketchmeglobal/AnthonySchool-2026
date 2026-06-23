/**
 * Created by mosaddek on 2/2/15.
 */

//date picker start
if (top.location != location) {
    top.location.href = document.location.href ;
}
$(function(){
    window.prettyPrint && prettyPrint();
    $('.dp_dob').datepicker({
        format: "dd MM, yyyy",
        endDate : '0d',
        autoclose: true,
        pickerPosition: "bottom-left"
    });
});
//date picker end
