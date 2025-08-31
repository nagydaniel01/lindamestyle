(function($){
    'use strict';

    $(document).ready(function(){

        $('#beauty_profile_form').on('submit', function(e){
            e.preventDefault();

            var data = {
                action: 'beauty_profile_form_handler',
                user_id: beauty_profile_form_ajax_object.user_id,
                form_data: $(this).serialize() // send all form fields including nonce
            };

            $.ajax({
                url: beauty_profile_form_ajax_object.ajax_url,
                type: 'POST',
                data: data,
                dataType: 'json',

                // Before sending, show a loading indicator
                beforeSend: function() {
                    $('#message').html('<div class="alert alert-info">'+beauty_profile_form_ajax_object.msg_saving+'</div>');
                },

                success: function(response){
                    if(response && typeof response === 'object'){
                        if(response.success){
                            var message = response.data && response.data.message ? response.data.message : beauty_profile_form_ajax_object.msg_success;
                            $('#message').html('<div class="alert alert-success">'+message+'</div>');
                        } else {
                            var message = response.data && response.data.message ? response.data.message : beauty_profile_form_ajax_object.msg_error_saving;
                            $('#message').html('<div class="alert alert-danger">'+message+'</div>');
                        }
                    } else {
                        $('#message').html('<div class="alert alert-danger">'+beauty_profile_form_ajax_object.msg_unexpected+'</div>');
                    }
                },

                error: function(xhr, status, error){
                    var errMsg = beauty_profile_form_ajax_object.msg_network_error;
                    if(xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message){
                        errMsg = xhr.responseJSON.data.message;
                    } else if(error){
                        errMsg += ' (' + error + ')';
                    }
                    $('#message').html('<div class="alert alert-danger">'+errMsg+'</div>');
                },

                // Runs regardless of success or error
                complete: function() {
                    // Optional: remove loading spinner or do other cleanup
                    // Example: console.log('AJAX request completed');
                }
            });
        });

    });

})(jQuery);
