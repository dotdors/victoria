/**
 * Header Settings Admin JS
 * Media uploader for the light/white logo upload field.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const uploadBtn  = document.getElementById('dsp-light-logo-upload');
        const removeBtn  = document.getElementById('dsp-light-logo-remove');
        const input      = document.getElementById('dsp_header_logo_light');
        const preview    = document.getElementById('dsp-light-logo-preview');

        if (!uploadBtn || !input || !preview) return;

        let frame;

        uploadBtn.addEventListener('click', function (e) {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title:    'Select or Upload Light/White Logo',
                button:   { text: 'Use this logo' },
                multiple: false,
                library:  { type: ['image/svg+xml', 'image/png', 'image/jpeg', 'image/gif'] },
            });

            frame.on('select', function () {
                const attachment = frame.state().get('selection').first().toJSON();
                input.value      = attachment.id;
                uploadBtn.textContent = 'Change Light Logo';

                // Update preview
                const src = attachment.sizes && attachment.sizes.medium
                    ? attachment.sizes.medium.url
                    : attachment.url;
                preview.innerHTML = '<img src="' + src + '" alt="" style="max-height:60px;max-width:200px;display:block;">';

                // Show remove button if not present
                if (!removeBtn) {
                    const btn = document.createElement('button');
                    btn.type      = 'button';
                    btn.className = 'button';
                    btn.id        = 'dsp-light-logo-remove';
                    btn.style.marginLeft = '4px';
                    btn.textContent = 'Remove';
                    uploadBtn.after(btn);
                    btn.addEventListener('click', removeLogo);
                }
            });

            frame.open();
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', removeLogo);
        }

        function removeLogo(e) {
            e.preventDefault();
            input.value      = '0';
            preview.innerHTML = '';
            uploadBtn.textContent = 'Upload Light Logo';
            const rBtn = document.getElementById('dsp-light-logo-remove');
            if (rBtn) rBtn.remove();
        }
    });

})();
