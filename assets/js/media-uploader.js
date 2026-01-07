jQuery(document).ready(function($) {
    var mediaUploader;

    $('#custprofpic_media_button').on('click', function(e) {
        e.preventDefault();

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media uploader
        mediaUploader = wp.media({
            title: 'Choose Profile Picture',
            button: {
                text: 'Use this picture'
            },
            library: {
                type: 'image'
            },
            multiple: false
        });

        // When a file is selected, run a callback
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            // Set the attachment ID to the hidden field
            $('#custprofpic_attachment_id').val(attachment.id);

            // Display the selected image
            var imgUrl = attachment.sizes && attachment.sizes.thumbnail ?
                        attachment.sizes.thumbnail.url : attachment.url;

            var imgHtml = '<img src="' + imgUrl + '" style="max-width:100px;max-height:100px;border-radius:50%;" alt="Profile Picture">';
            $('#custprofpic-profile-picture-display').html(imgHtml);
        });

        // Open the uploader dialog
        mediaUploader.open();
    });

    // Clear the file input when media library is used
    $('#custprofpic_media_button').on('click', function() {
        $('#custprofpic_profile_picture').val('');
    });

    // Clear the attachment ID when file input is used
    $('#custprofpic_profile_picture').on('change', function() {
        if ($(this).val()) {
            $('#custprofpic_attachment_id').val('');
        }
    });
});
