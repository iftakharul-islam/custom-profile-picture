jQuery(document).ready(function($) {
    let cropper;
    let cropperImage = document.getElementById('custprofpic-crop-image');
    let fileInput = document.getElementById('custprofpic_profile_picture');
    let modal = document.getElementById('custprofpic-cropping-modal');

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
    $('.custprofpic-close-modal, #custprofpic-crop-cancel').on('click', function() {
        modal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
        }
        fileInput.value = '';
    });

    // Save cropped image
    $('#custprofpic-crop-save').on('click', function() {
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
                url: custprofpic_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'custprofpic_save_cropped_image',
                    image: imageData,
                    user_id: userId,
                    nonce: custprofpic_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update the profile picture display immediately
                        let profileDisplay = document.getElementById('custprofpic-profile-picture-display');
                        if (profileDisplay) {
                            // Create new image element
                            let newImage = document.createElement('img');
                            newImage.src = response.data.url;
                            newImage.style.cssText = 'max-width:100px;max-height:100px;border-radius:50%;';
                            newImage.alt = 'Profile Picture';
                            
                            // Clear existing content and add new image
                            profileDisplay.innerHTML = '';
                            profileDisplay.appendChild(newImage);
                        }
                        
                        // Show success message
                        alert('Profile picture updated successfully!');
                        
                        // Close modal
                        modal.style.display = 'none';
                        if (cropper) {
                            cropper.destroy();
                        }
                        fileInput.value = '';
                        
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