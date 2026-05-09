/* global custprofpicFrontend, Cropper */
(function ($) {
    'use strict';

    var cropper     = null;
    var fileInput   = document.getElementById('custprofpic-frontend-file');
    var cropImage   = document.getElementById('custprofpic-frontend-crop-image');
    var modal       = document.getElementById('custprofpic-frontend-modal');
    var wrap        = document.getElementById('custprofpic-frontend-wrap');

    if (!fileInput || !cropImage || !modal || !wrap) {
        return;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    function showMessage(text, type) {
        var msgEl = document.getElementById('custprofpic-message');
        if (!msgEl) { return; }
        msgEl.textContent  = text;
        msgEl.className    = 'custprofpic-message custprofpic-message--' + (type || 'info');
        msgEl.style.display = 'block';
    }

    function clearMessage() {
        var msgEl = document.getElementById('custprofpic-message');
        if (msgEl) {
            msgEl.textContent  = '';
            msgEl.style.display = 'none';
        }
    }

    function openModal() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        fileInput.value = '';
    }

    function updateAvatarSrc(url) {
        var avatar = document.getElementById('custprofpic-current-avatar');
        if (avatar) {
            avatar.src = url;
        }
    }

    function showRemoveButton() {
        var removeBtn = document.getElementById('custprofpic-frontend-remove');
        if (!removeBtn) {
            var actionsEl = wrap.querySelector('.custprofpic-actions');
            if (actionsEl) {
                removeBtn = document.createElement('button');
                removeBtn.type      = 'button';
                removeBtn.id        = 'custprofpic-frontend-remove';
                removeBtn.className = 'button custprofpic-remove-btn';
                removeBtn.textContent = custprofpicFrontend.strings.remove_label || 'Remove Picture';
                actionsEl.appendChild(removeBtn);
                bindRemoveButton(removeBtn);
            }
        }
        if (removeBtn) {
            removeBtn.style.display = '';
        }
    }

    function hideRemoveButton() {
        var removeBtn = document.getElementById('custprofpic-frontend-remove');
        if (removeBtn) {
            removeBtn.style.display = 'none';
        }
    }

    // -------------------------------------------------------------------------
    // File input change → open crop modal
    // -------------------------------------------------------------------------

    $(fileInput).on('change', function () {
        var file = this.files && this.files[0];
        if (!file) { return; }

        if (!file.type.match(/^image\//)) {
            showMessage(custprofpicFrontend.strings.select_image, 'error');
            fileInput.value = '';
            return;
        }

        clearMessage();

        var reader = new FileReader();
        reader.onload = function (e) {
            cropImage.src = e.target.result;
            openModal();

            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(cropImage, {
                aspectRatio:        1,
                viewMode:           1,
                dragMode:           'move',
                autoCropArea:       1,
                restore:            false,
                guides:             true,
                center:             true,
                highlight:          false,
                cropBoxMovable:     true,
                cropBoxResizable:   true,
                toggleDragModeOnDblclick: false,
            });
        };
        reader.readAsDataURL(file);
    });

    // -------------------------------------------------------------------------
    // Save crop → AJAX upload
    // -------------------------------------------------------------------------

    $(document).on('click', '#custprofpic-frontend-crop-save', function () {
        if (!cropper) { return; }

        var canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
        if (!canvas) { return; }

        var saveBtn = this;
        $(saveBtn).prop('disabled', true).text('Saving\u2026');

        var imageData = canvas.toDataURL('image/png');

        $.ajax({
            url:  custprofpicFrontend.ajax_url,
            type: 'POST',
            data: {
                action:  'custprofpic_save_cropped_image',
                image:   imageData,
                user_id: custprofpicFrontend.user_id,
                nonce:   custprofpicFrontend.crop_nonce,
            },
            success: function (response) {
                if (response.success && response.data && response.data.url) {
                    updateAvatarSrc(response.data.url);
                    showRemoveButton();
                    showMessage(
                        response.data.message || 'Profile picture updated successfully!',
                        'success'
                    );
                    closeModal();
                } else {
                    showMessage(custprofpicFrontend.strings.upload_error, 'error');
                }
            },
            error: function () {
                showMessage(custprofpicFrontend.strings.upload_error, 'error');
            },
            complete: function () {
                $(saveBtn).prop('disabled', false).text('Save Crop');
            },
        });
    });

    // -------------------------------------------------------------------------
    // Close modal (cancel / × button / backdrop)
    // -------------------------------------------------------------------------

    $(document).on('click', '.custprofpic-close-modal, #custprofpic-frontend-crop-cancel', function () {
        closeModal();
    });

    $(window).on('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // -------------------------------------------------------------------------
    // Remove picture → AJAX
    // -------------------------------------------------------------------------

    function bindRemoveButton(btn) {
        $(btn).on('click', function () {
            // eslint-disable-next-line no-alert
            if (!window.confirm(custprofpicFrontend.strings.confirm_remove)) {
                return;
            }

            var removeBtn = this;
            $(removeBtn).prop('disabled', true);

            $.ajax({
                url:  custprofpicFrontend.ajax_url,
                type: 'POST',
                data: {
                    action: 'custprofpic_frontend_remove_picture',
                    nonce:  custprofpicFrontend.remove_nonce,
                },
                success: function (response) {
                    if (response.success && response.data && response.data.avatar_url) {
                        updateAvatarSrc(response.data.avatar_url);
                        hideRemoveButton();
                        showMessage(response.data.message || 'Profile picture removed.', 'success');
                    } else {
                        showMessage(custprofpicFrontend.strings.remove_error, 'error');
                        $(removeBtn).prop('disabled', false);
                    }
                },
                error: function () {
                    showMessage(custprofpicFrontend.strings.remove_error, 'error');
                    $(removeBtn).prop('disabled', false);
                },
            });
        });
    }

    var existingRemoveBtn = document.getElementById('custprofpic-frontend-remove');
    if (existingRemoveBtn) {
        bindRemoveButton(existingRemoveBtn);
    }

}(jQuery));
