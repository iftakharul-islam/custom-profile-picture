jQuery(document).ready(function($) {
    let cropper;
    let cropperImage = document.getElementById('cpp-crop-image');
    let fileInput = document.getElementById('cpp_profile_picture');
    let modal = document.getElementById('cpp-cropping-modal');

    // Initialize cropper when file is selected
    $(fileInput).on('change', function(e) {
        let file = e.target.files[0];
        if (file) {
            if (!file.type.match('image.*')) {
                alert('Please select an image file');
                return;
            }

            let reader = new FileReader();
            reader.onload = function(e) {
                cropperImage.src = e.target.result;
                modal.style.display = 'block';
                
                // Initialize cropper
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // Close modal
    $('.cpp-close-modal, #cpp-crop-cancel').on('click', function() {
        modal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
        }
        fileInput.value = '';
    });

    // Save cropped image
    $('#cpp-crop-save').on('click', function() {
        if (!cropper) return;

        let canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300
        });

        if (canvas) {
            let imageData = canvas.toDataURL('image/png');
            let userId = $(fileInput).closest('form').find('input[name="user_id"]').val() || 
                        $(fileInput).closest('form').find('input#user_id').val();
            
            $.ajax({
                url: cpp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'cpp_save_cropped_image',
                    image: imageData,
                    user_id: userId,
                    nonce: cpp_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();

                    } else {
                        alert('Failed to save image. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
            modal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
            }
            fileInput.value = '';
        }
    });

    // Close modal if clicked outside
    $(window).on('click', function(e) {
        if (e.target == modal) {
            modal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
            }
            fileInput.value = '';
        }
    });
});