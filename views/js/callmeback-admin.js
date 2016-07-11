/*
* @author Sarafoudis Nikolaos for 01generator.com <modules@01generator.com>
* @copyright 2015 Sarafoudis Nikolaos
* @license All Rights Reserved read license.txt file for more information
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
            console.log(baseUrl + '/modules/callmeback/callmeback-ajax.php?method=updateCalled&callid=' + callid + '&callchecked=' + call_checked);
            $.ajax({
                url: baseUrl + '/modules/callmeback/callmeback-ajax.php',
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