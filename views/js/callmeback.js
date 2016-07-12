/*
 * @author Sarafoudis Nikolaos for 01generator.com <modules@01generator.com>
 * @license MIT License
 */

$(document).ready(function () {
    $('#callmeback').on('click', function () {
        event.preventDefault();
        $('#callmeback-form-msg-ok').hide();
        $('#callmeback-form-msg-alert').hide();
        $('#callmeback-form').toggle('slow');
    });

    $('#callmeback-submit').on('click', function () {
        event.preventDefault();
        var product_id = $('#product_page_product_id').val();
        var callmeback_name = $('#callmeback_name').val();
        var callmeback_telephone = $('#callmeback_telephone').val();
        var form_errors = [];
        // Surname
        if ($("#callmeback_surname").length) {
            var callmeback_surname = $('#callmeback_surname').val();
        } else {
            var callmeback_surname = '';
        }
        // Email
        if ($("#callmeback_email").length) {
            var callmeback_email = $('#callmeback_email').val();
        } else {
            var callmeback_email = '';
        }
        // Tele 2
        if ($("#callmeback_telephone2").length) {
            var callmeback_telephone2 = $('#callmeback_telephone2').val();
        } else {
            var callmeback_telephone2 = '';
        }
        // Hours from
        if ($("#callmeback_hours_from").length) {
            var callmeback_hours_from = $('#callmeback_hours_from').val();
        } else {
            var callmeback_hours_from = '';
        }
        // Hours to
        if ($("#callmeback_hours_to").length) {
            var callmeback_hours_to = $('#callmeback_hours_to').val();
        } else {
            var callmeback_hours_to = '';
        }
        // Message
        if ($("#callmeback_msg").length) {
            var callmeback_msg = $('#callmeback_msg').val();
        } else {
            var callmeback_msg = '';
        }
        console.log('method=callmebackSubmit&ajax=true'
            + '&product_id=' + product_id
            + '&token=' + static_token
            + '&callmeback_name=' + callmeback_name
            + '&callmeback_telephone=' + callmeback_telephone
            + '&callmeback_surname=' + callmeback_surname
            + '&callmeback_email=' + callmeback_email
            + '&callmeback_telephone2=' + callmeback_telephone2
            + '&callmeback_hours_from=' + callmeback_hours_from
            + '&callmeback_hours_to=' + callmeback_hours_to
            + '&callmeback_msg=' + callmeback_msg);
        // (typeof callmeback_surname !== 'undefined')? : ;
        $.ajax({
            url: callmeback_ajax,
            type: 'POST',
            data: 'method=callmebackSubmit&ajax=true'
            + '&product_id=' + product_id
            + '&token=' + static_token
            + '&callmeback_name=' + callmeback_name
            + '&callmeback_telephone=' + callmeback_telephone
            + '&callmeback_surname=' + callmeback_surname
            + '&callmeback_email=' + callmeback_email
            + '&callmeback_telephone2=' + callmeback_telephone2
            + '&callmeback_hours_from=' + callmeback_hours_from
            + '&callmeback_hours_to=' + callmeback_hours_to
            + '&callmeback_msg=' + callmeback_msg,
            dataType: 'json',
            success: function (data) {
                if (data['callmeback_call']) {
                    // data['callmeback_call_html']
                    // $('#callmeback-form-msg').html(data['callmeback_call_html']);
                    $('#callmeback-form').toggle('slow');
                    $('#callmeback-form-msg-alert').hide();
                    $('#callmeback-form-msg-ok').toggle('fast');
                    $('#callmeback-form-msg').toggle('slow');
                } else {
                    var alert_message = '';
                    if (data['callmeback_call_html'] == 'sql_error') {
                        alert_message = data['sql_error_msg'];
                    } else if (data['callmeback_call_html'] == 'ajax_error') {
                        alert_message = data['ajax_error_msg'];
                    } else if (data['callmeback_call_html'] == 'form_error') {
                        alert_message = '<ul class="callmeback_error_message_list">';
                        for (index = 0; index < data['form_errors'].length; ++index) {
                            alert_message += '<li>' + data['form_errors'][index] + '</li>'
                        }
                        alert_message += '</ul>';
                        console.log(alert_message);
                    }
                    $('#callmeback-form').toggle('slow');
                    $('#callmeback-form-msg-alert').html(alert_message);
                    $('#callmeback-form-msg-ok').hide();
                    $('#callmeback-form-msg-alert').show();
                    $('#callmeback-form-msg').show('slow');
                }
                // OTHER SUCCESS COMMAND - CHECK THE RETURN VALUE
            }
        });
    });
});