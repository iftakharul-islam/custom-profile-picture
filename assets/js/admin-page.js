jQuery(document).ready(function($) {
    var mediaUploaders = {};

    // Handle change picture button click
    $('.custprofpic-change-picture').on('click', function(e) {
        e.preventDefault();

        var userId = $(this).data('user-id');
        var $card = $(this).closest('.custprofpic-user-card');
        var $avatar = $card.find('.custprofpic-avatar');
        var $attachmentIdField = $card.find('.custprofpic-attachment-id');

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploaders[userId]) {
            mediaUploaders[userId].open();
            return;
        }

        // Create the media uploader for this user
        mediaUploaders[userId] = wp.media({
            title: 'Choose Profile Picture for ' + $card.find('h3').text(),
            button: {
                text: 'Use this picture'
            },
            library: {
                type: 'image'
            },
            multiple: false
        });

        // When a file is selected, run a callback
        mediaUploaders[userId].on('select', function() {
            var attachment = mediaUploaders[userId].state().get('selection').first().toJSON();

            // Show loading state
            $card.addClass('loading');

            // Send AJAX request to update user profile picture
            $.ajax({
                url: custprofpicAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'custprofpic_update_user_picture',
                    nonce: custprofpicAdmin.nonce,
                    user_id: userId,
                    attachment_id: attachment.id
                },
                success: function(response) {
                    $card.removeClass('loading');

                    if (response.success) {
                        // Update the avatar image
                        $avatar.attr('src', response.data.image_url);

                        // Update attachment ID
                        $attachmentIdField.val(attachment.id);

                        // Add remove button if it doesn't exist
                        if (!$card.find('.custprofpic-remove-picture').length) {
                            var $removeBtn = $('<button type="button" class="button custprofpic-remove-picture" data-user-id="' + userId + '">' +
                                '<span class="dashicons dashicons-trash"></span>' +
                                custprofpicAdmin.strings.removed.replace(' successfully!', '') +
                                '</button>');
                            $card.find('.custprofpic-avatar-overlay').append($removeBtn);
                        }

                        // Show success message
                        showNotice(response.data.message, 'success');
                    } else {
                        showNotice(response.data.message || custprofpicAdmin.strings.error, 'error');
                    }
                },
                error: function() {
                    $card.removeClass('loading');
                    showNotice(custprofpicAdmin.strings.error, 'error');
                }
            });
        });

        // Open the uploader dialog
        mediaUploaders[userId].open();
    });

    // Handle remove picture button click
    $(document).on('click', '.custprofpic-remove-picture', function(e) {
        e.preventDefault();

        if (!confirm(custprofpicAdmin.strings.confirm_remove)) {
            return;
        }

        var userId = $(this).data('user-id');
        var $card = $(this).closest('.custprofpic-user-card');
        var $avatar = $card.find('.custprofpic-avatar');
        var $attachmentIdField = $card.find('.custprofpic-attachment-id');
        var $removeBtn = $(this);

        // Show loading state
        $card.addClass('loading');

        // Send AJAX request to remove user profile picture
        $.ajax({
            url: custprofpicAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'custprofpic_remove_user_picture',
                nonce: custprofpicAdmin.nonce,
                user_id: userId
            },
            success: function(response) {
                $card.removeClass('loading');

                if (response.success) {
                    // Update the avatar image to Gravatar
                    $avatar.attr('src', response.data.image_url);

                    // Clear attachment ID
                    $attachmentIdField.val('');

                    // Remove the remove button
                    $removeBtn.remove();

                    // Show success message
                    showNotice(response.data.message, 'success');
                } else {
                    showNotice(response.data.message || custprofpicAdmin.strings.error, 'error');
                }
            },
            error: function() {
                $card.removeClass('loading');
                showNotice(custprofpicAdmin.strings.error, 'error');
            }
        });
    });

    // Show notification
    function showNotice(message, type) {
        var $notice = $('<div class="custprofpic-notice ' + (type === 'error' ? 'error' : '') + '">' + message + '</div>');
        $('body').append($notice);

        // Auto-hide after 3 seconds
        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
});
