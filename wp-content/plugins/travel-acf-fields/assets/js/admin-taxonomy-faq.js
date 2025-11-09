/**
 * Hide native Description and Slug fields for FAQ taxonomy
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Execute multiple times to ensure fields are hidden
        function hideNativeFields() {
            // Target the EXACT selectors from the HTML structure

            // Hide Slug field - line 12-16 in the form
            $('.form-field.term-slug-wrap').css('display', 'none !important').hide();
            $('div.term-slug-wrap').css('display', 'none !important').hide();
            $('#tag-slug').closest('.form-field').css('display', 'none !important').hide();
            $('input[name="slug"]').closest('.form-field').css('display', 'none !important').hide();
            $('label[for="tag-slug"]').closest('.form-field').css('display', 'none !important').hide();

            // Hide Description field - line 17-21 in the form
            $('.form-field.term-description-wrap').css('display', 'none !important').hide();
            $('div.term-description-wrap').css('display', 'none !important').hide();
            $('#tag-description').closest('.form-field').css('display', 'none !important').hide();
            $('textarea[name="description"]').closest('.form-field').css('display', 'none !important').hide();
            $('label[for="tag-description"]').closest('.form-field').css('display', 'none !important').hide();
        }

        // Run immediately
        hideNativeFields();

        // Run after 100ms (for ACF load)
        setTimeout(hideNativeFields, 100);

        // Run after 500ms (backup)
        setTimeout(hideNativeFields, 500);

        // Run after 1000ms (final backup)
        setTimeout(hideNativeFields, 1000);
    });
})(jQuery);
