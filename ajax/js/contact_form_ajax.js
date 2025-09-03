(function($){
    'use strict';

    $(document).ready(function(){

        $('#contact_form').on('submit', function(e){
            e.preventDefault();

            // Check if privacy checkbox is checked
            if( !$('#privacy_policy').is(':checked') ){
                $('#response').html('<div class="alert alert-danger">'+event_registration_form_ajax_object.msg_privacy_required+'</div>');
                return; // stop submission
            }

            var data = {
                action: 'contact_form_handler',
                user_id: contact_form_ajax_object.user_id,
                form_data: $(this).serialize() // send all form fields including nonce
            };

            $.ajax({
                url: contact_form_ajax_object.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',

                // Before sending, show a loading indicator
                beforeSend: function() {
                    $('#response').html('<div class="alert alert-info">'+contact_form_ajax_object.msg_sending+'</div>');
                },

                success: function(response){
                    if(response && typeof response === 'object'){
                        if(response.success){
                            var message = response.data && response.data.message ? response.data.message : contact_form_ajax_object.msg_success;
                            $('#response').html('<div class="alert alert-success">'+message+'</div>');

                            if(response.data.redirect_url){
                                // Grab values from response
                                var message_id = response.data.message_id ? response.data.message_id : '';

                                // Build query string
                                var queryString = '?message_id=' + encodeURIComponent(message_id);

                                setTimeout(function(){
                                    window.location.href = response.data.redirect_url + queryString;
                                }, 500); // short delay so user sees the success message
                            }
                        } else {
                            var message = response.data && response.data.message ? response.data.message : contact_form_ajax_object.msg_error_sending;
                            $('#response').html('<div class="alert alert-danger">'+message+'</div>');
                        }
                    } else {
                        $('#response').html('<div class="alert alert-danger">'+contact_form_ajax_object.msg_unexpected+'</div>');
                    }
                },

                error: function(xhr, status, error){
                    var errMsg = contact_form_ajax_object.msg_network_error;
                    if(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message){
                        errMsg = xhr.responseJSON.data.message;
                    } else if(error){
                        errMsg += ' (' + error + ')';
                    }
                    $('#response').html('<div class="alert alert-danger">'+errMsg+'</div>');
                },

                complete: function() {
                    // Optional: remove loading spinner or do other cleanup
                    // Example: console.log('AJAX request completed');
                }
            });
        });

    });

})(jQuery);
