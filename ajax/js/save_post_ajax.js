(function ($) {
    'use strict';
    
    $(document).ready(function() {
        function updateBookmarkStatus($button, newStatus) {
            $button.data('bookmarked', newStatus);
            var iconClass = newStatus ? 'icon-bookmark' : 'icon-bookmark-empty';
            var iconHtml = '<svg class="icon ' + iconClass + '"><use xlink:href="#' + iconClass + '"></use></svg>';
            var label = newStatus ? 'Remove from Bookmarks' : 'Add to Bookmarks';
            var buttonHtml = iconHtml + '<span class="visually-hidden">' + label + '</span>';

            $button.attr('data-bookmarked', newStatus);
            $button.html(buttonHtml);
        }

        function updateBookmarksList() {
            $.ajax({
                type: 'POST',
                url: save_post_ajax_object.ajax_url,
                data: {
                    action: 'update_bookmark_list_handler'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.woocommerce-MyAccount-content').html(response.data.html);
                    } else {
                        $('#error-container').html('<div class="error-message alert alert-danger" role="alert">'+response.data.message+'</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed:", status, error);
                }
            });
        }

        $(document).on('click', '#btn-bookmark', function(event) {
            event.preventDefault();

            var $button = $(this);

            var post_id = $button.data('post-id');
            var currentStatus = $button.data('bookmarked');
            var newStatus = !currentStatus;

            // Add loading state
            var loadingHtml = '<svg class="icon icon-loading"><use xlink:href="#icon-loading"></use></svg><span class="visually-hidden">Processing...</span>';
            $button.html(loadingHtml);

            $.ajax({
                type: 'POST',
                url: save_post_ajax_object.ajax_url,
                data: {
                    action: 'save_post_handler',
                    post_id: post_id
                },
                dataType: 'json',
                success: function(response) {
                    if (!response.success) {
                        console.error("Error in response:", response);
                    } else {
                        updateBookmarkStatus($button, newStatus);
                        setTimeout(function() {
                            updateBookmarksList();
                        }, 0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed:", status, error);
                }
            });
        });
    });
})(jQuery);
