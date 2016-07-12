/*
* @author Sarafoudis Nikolaos for 01generator.com <modules@01generator.com>
* @license MIT License
*/

var baseUrl = document.location.origin;

$( document ).ready(function() {
    $('.calledStatus').each(function(){
        $(this).find('.checkbox-called').on('click', function (){
            // event.preventDefault();
            var callid = $(this).data('callid');
            var call_checked = $(this).prop('checked');
            if (call_checked == 0) {
                call_checked = 'false';
            } else {
                call_checked = 'true';
            }
            $.ajax({
                url: callmeback_ajax,
                type: 'POST',
                data: 'method=updateCalled&callid=' + callid + '&callchecked=' + call_checked,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    // if (data['callupdated']){

                    // }
                    // OTHER SUCCESS COMMAND - CHECK THE RETURN VALUE
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(XMLHttpRequest);
                }
            });
        });
    });
});