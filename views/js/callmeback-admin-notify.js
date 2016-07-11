/*
* @author Sarafoudis Nikolaos for 01generator.com <modules@01generator.com>
* @license MIT License
*/

var baseUrl = document.location.origin;

$( document ).ready(function() {
    var callmeback_admin_link = $('#subtab-AdminCallMeBack a').attr('href');
    $('#header_notifs_icon_wrapper').append('<li id="callmeback-notify"><a href="'+callmeback_admin_link+'"><i class="icon-phone"></i></li></a>');
    $.ajax({
        url: baseUrl + '/modules/callmeback/callmeback-ajax.php',
        type: 'POST',
        data: 'method=notifyCallmeback',
        dataType: 'json',
        success: function(data) {
            if (data['callmeback_notify'] > 0){
                $('#callmeback-notify').append('<span id="callmeback_notif_number_wrapper" class="notifs_badge"><span id="callmeback_notif_value">'+data['callmeback_notify']+'</span></span>');
            }
            // OTHER SUCCESS COMMAND - CHECK THE RETURN VALUE
        }
    });
});

// <span id="orders_notif_number_wrapper" class="notifs_badge">
//     <span id="orders_notif_value">3</span>
// </span>