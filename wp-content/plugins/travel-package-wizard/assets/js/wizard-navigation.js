/**
 * Aurora Package Wizard - Navigation Controller
 * Handles step navigation, validation, and auto-save
 */

(function($) {
    'use strict';

    window.AuroraWizard = {
        currentStep: 0,
        totalSteps: 6,
        steps: [],
        postId: 0,
        saving: false,
        autoSaveTimer: null,

        /**
         * Initialize wizard
         */
        init: function() {
            if (typeof auroraWizard === 'undefined') {
                console.error('Aurora Wizard: Configuration not found');
                return;
            }

            this.steps = Object.keys(auroraWizard.steps);
            this.totalSteps = this.steps.length;
            this.currentStep = this.steps.indexOf(auroraWizard.currentStep);
            this.postId = auroraWizard.postId;

            // Disable WordPress/ACF beforeunload warnings for wizard
            this.preventBeforeUnloadWarnings();

            this.bindEvents();
            this.updateUI();

            console.log('Aurora Wizard initialized:', {
                currentStep: this.currentStep,
                totalSteps: this.totalSteps,
                steps: this.steps
            });
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            var self = this;

            // Navigation buttons
            $(document).on('click', '.wizard-nav-next', function(e) {
                e.preventDefault();
                self.nextStep();
            });

            $(document).on('click', '.wizard-nav-back', function(e) {
                e.preventDefault();
                self.prevStep();
            });

            // Save draft button
            $(document).on('click', '.wizard-nav-save, #wizard-save-draft', function(e) {
                e.preventDefault();
                self.saveDraft();
            });

            // Step indicator click
            $(document).on('click', '.wizard-step-indicator', function(e) {
                var targetIndex = parseInt($(this).data('step-index'));
                if (!isNaN(targetIndex) && self.canJumpToStep(targetIndex)) {
                    self.goToStep(targetIndex);
                }
            });

            // Auto-save on field change (disabled for wizard to prevent beforeunload)
            // We handle saving explicitly on step navigation
            // $(document).on('change', '.acf-field input, .acf-field textarea, .acf-field select', function() {
            //     self.triggerAutoSave();
            // });

            // Prevent form submission on Enter key in text fields
            $(document).on('keypress', '.acf-field input[type="text"]', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }
            });
        },

        /**
         * Navigate to next step
         */
        nextStep: function() {
            var self = this;

            if (this.saving) {
                return;
            }

            // Validate current step
            this.validateCurrentStep().then(function(isValid) {
                if (isValid) {
                    // Save and move to next step
                    self.saveStep().then(function() {
                        if (self.currentStep < self.totalSteps - 1) {
                            var nextStep = self.steps[self.currentStep + 1];
                            // Disable WordPress beforeunload warning before redirecting
                            self.disableBeforeUnload();
                            window.location.href = self.getStepUrl(nextStep);
                        }
                    });
                } else {
                    self.showValidationError();
                }
            });
        },

        /**
         * Navigate to previous step
         */
        prevStep: function() {
            if (this.currentStep > 0) {
                var prevStep = this.steps[this.currentStep - 1];
                // Disable WordPress beforeunload warning before redirecting
                this.disableBeforeUnload();
                window.location.href = this.getStepUrl(prevStep);
            }
        },

        /**
         * Go to specific step
         */
        goToStep: function(stepIndex) {
            if (stepIndex >= 0 && stepIndex < this.totalSteps) {
                var targetStep = this.steps[stepIndex];
                // Disable WordPress beforeunload warning before redirecting
                this.disableBeforeUnload();
                window.location.href = this.getStepUrl(targetStep);
            }
        },

        /**
         * Check if can jump to step
         */
        canJumpToStep: function(targetIndex) {
            // Can go back to any completed step
            // Can go forward only to next step
            return targetIndex <= this.currentStep + 1;
        },

        /**
         * Get step URL
         */
        getStepUrl: function(stepName) {
            var baseUrl = window.location.href.split('?')[0];
            var params = new URLSearchParams(window.location.search);
            params.set('wizard_step', stepName);
            return baseUrl + '?' + params.toString();
        },

        /**
         * Validate current step
         */
        validateCurrentStep: function() {
            var self = this;
            var deferred = $.Deferred();

            // Get required fields in current step
            var $requiredFields = $('.wizard-step.active .acf-field.is-required');
            var hasErrors = false;
            var errorMessages = [];

            $requiredFields.each(function() {
                var $field = $(this);
                var value = null;
                var fieldLabel = $field.find('.acf-label label').first().clone().children().remove().end().text().trim();

                // Handle different ACF field types
                if ($field.hasClass('acf-field-select')) {
                    // For ACF select fields (including Select2)
                    var $select = $field.find('select').first();
                    value = $select.val();
                } else if ($field.hasClass('acf-field-wysiwyg')) {
                    // For WYSIWYG fields
                    var editorId = $field.find('textarea').attr('id');
                    if (editorId && typeof tinymce !== 'undefined') {
                        var editor = tinymce.get(editorId);
                        value = editor ? editor.getContent() : $field.find('textarea').val();
                    } else {
                        value = $field.find('textarea').val();
                    }
                } else if ($field.hasClass('acf-field-image')) {
                    // For image fields
                    value = $field.find('input[type="hidden"]').val();
                } else if ($field.hasClass('acf-field-gallery')) {
                    // For gallery fields
                    value = $field.find('.acf-gallery-attachment').length > 0 ? 'has-images' : null;
                } else {
                    // For regular input, textarea, select
                    var $input = $field.find('input, textarea, select').not('[type="hidden"]').first();
                    value = $input.val();
                }

                // Check if value is empty
                var isEmpty = !value ||
                             ($.isArray(value) && value.length === 0) ||
                             (typeof value === 'string' && value.trim() === '');

                if (isEmpty) {
                    $field.addClass('wizard-field-error');
                    hasErrors = true;
                    errorMessages.push(fieldLabel || 'Unknown field');
                } else {
                    $field.removeClass('wizard-field-error');
                }
            });

            // Validate repeater fields specially
            $('.wizard-step.active .acf-repeater').each(function() {
                var $repeater = $(this);
                var $rows = $repeater.find('.acf-row:not(.acf-clone)');

                $rows.each(function(index) {
                    var $row = $(this);
                    var $requiredInRow = $row.find('.acf-field.is-required');

                    $requiredInRow.each(function() {
                        var $field = $(this);
                        var value = null;
                        var fieldLabel = $field.find('.acf-label label').first().text().trim();
                        var repeaterLabel = $repeater.closest('.acf-field').find('> .acf-label label').first().text().trim();

                        if ($field.hasClass('acf-field-image')) {
                            value = $field.find('input[type="hidden"]').val();
                        } else {
                            value = $field.find('input, textarea, select').not('[type="hidden"]').first().val();
                        }

                        var isEmpty = !value || (typeof value === 'string' && value.trim() === '');

                        if (isEmpty) {
                            $field.addClass('wizard-field-error');
                            hasErrors = true;
                            errorMessages.push(repeaterLabel + ' (Row ' + (index + 1) + ') - ' + fieldLabel);
                        } else {
                            $field.removeClass('wizard-field-error');
                        }
                    });
                });
            });

            // Custom validations
            var stepName = this.steps[this.currentStep];

            switch(stepName) {
                case 'basic':
                    var summary = $('#acf-field_package_summary').val();
                    if (summary && summary.length > 200) {
                        hasErrors = true;
                        errorMessages.push('Summary must be 200 characters or less');
                    }
                    break;

                case 'pricing':
                    var priceFrom = parseFloat($('#acf-field_package_price_from').val());
                    var priceNormal = parseFloat($('#acf-field_package_price_normal').val());

                    if (priceFrom && priceNormal && priceFrom > priceNormal) {
                        hasErrors = true;
                        errorMessages.push('"Price From" cannot be higher than "Normal Price"');
                    }
                    break;

                case 'media':
                    // Validate Featured Image (WordPress native field in sidebar)
                    var $featuredImageDiv = $('#postimagediv');
                    var hasFeaturedImage = $featuredImageDiv.find('#set-post-thumbnail img').length > 0;

                    if (!hasFeaturedImage) {
                        hasErrors = true;
                        errorMessages.push('Featured Image (see right sidebar) is required');
                        // Highlight the featured image metabox
                        $featuredImageDiv.addClass('wizard-field-error');
                    } else {
                        $featuredImageDiv.removeClass('wizard-field-error');
                    }
                    break;
            }

            // Debug: Log all errors to console
            if (hasErrors) {
                console.log('Aurora Wizard - Validation Errors:', errorMessages);
                console.log('Aurora Wizard - Total errors found:', errorMessages.length);

                // Show specific error messages
                self.showValidationErrors(errorMessages);
                deferred.resolve(false);
            } else {
                console.log('Aurora Wizard - Validation passed! No errors found.');
                deferred.resolve(true);
            }

            return deferred.promise();
        },

        /**
         * Save current step
         */
        saveStep: function() {
            var self = this;
            var deferred = $.Deferred();

            this.saving = true;
            this.showNotice(auroraWizard.labels.saving, 'info');

            // First, collect all ACF field data and save it manually
            var acfData = this.collectACFFieldData();

            // Save via AJAX with ACF data
            $.ajax({
                url: auroraWizard.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'aurora_wizard_save_step',
                    nonce: auroraWizard.nonce,
                    post_id: this.postId,
                    step: this.steps[this.currentStep],
                    acf: acfData
                },
                success: function(response) {
                    self.saving = false;
                    console.log('Aurora Wizard - Save response:', response);

                    // Display debug log if available
                    if (response.data && response.data.debug) {
                        console.log('%c========== SERVER DEBUG LOG ==========', 'color: #4CAF50; font-weight: bold');
                        response.data.debug.forEach(function(log) {
                            console.log('%c' + log, 'color: #666');
                        });
                        console.log('%c======================================', 'color: #4CAF50; font-weight: bold');
                    }

                    if (response.success) {
                        self.showNotice(auroraWizard.labels.saved, 'success', 2000);

                        // Mark all ACF fields as unmodified to prevent beforeunload
                        self.markFieldsAsUnmodified();

                        // Log saved fields info
                        if (response.data && response.data.saved_fields) {
                            console.log('Aurora Wizard - Saved ' + response.data.saved_fields + ' fields');
                        }

                        deferred.resolve();
                    } else {
                        var errorMsg = 'Save failed: ' + (response.data && response.data.message ? response.data.message : 'Unknown error');
                        self.showNotice(errorMsg, 'error', 5000);
                        console.error('Aurora Wizard - Save error:', response);
                        deferred.reject();
                    }
                },
                error: function(xhr, status, error) {
                    self.saving = false;
                    console.error('%c========== SERVER ERROR ==========', 'color: #f44336; font-weight: bold');
                    console.error('Status:', xhr.status);
                    console.error('Response:', xhr.responseText);
                    console.error('Error:', error);
                    console.error('%c==================================', 'color: #f44336; font-weight: bold');
                    self.showNotice('Save failed: Server error', 'error');
                    deferred.reject();
                }
            });

            return deferred.promise();
        },

        /**
         * Collect ACF field data from current step
         */
        collectACFFieldData: function() {
            var data = {};
            var self = this;

            // Get all ACF fields in the active step
            var $fields = $('.wizard-step.active .acf-field[data-name]');

            $fields.each(function() {
                var $field = $(this);
                var fieldName = $field.attr('data-name');
                var fieldType = $field.attr('data-type');
                var fieldKey = $field.attr('data-key');
                var value = null;

                // Skip fields that are:
                // 1. Inside a repeater row (not the repeater itself)
                // 2. Clone fields (ACF templates)
                var $parentRow = $field.closest('.acf-row');
                if ($parentRow.length > 0 && !$parentRow.hasClass('acf-clone')) {
                    // This is a field INSIDE a repeater row, skip it
                    // The repeater handler will collect these
                    return;
                }

                // Skip clone fields
                if ($field.closest('.acf-clone').length > 0) {
                    return;
                }

                switch(fieldType) {
                    case 'select':
                        value = $field.find('select').val();
                        break;

                    case 'wysiwyg':
                        var editorId = $field.find('textarea').attr('id');
                        if (editorId && typeof tinymce !== 'undefined') {
                            var editor = tinymce.get(editorId);
                            value = editor ? editor.getContent() : $field.find('textarea').val();
                        } else {
                            value = $field.find('textarea').val();
                        }
                        break;

                    case 'true_false':
                        value = $field.find('input[type="checkbox"]').is(':checked') ? 1 : 0;
                        break;

                    case 'number':
                    case 'range':
                        value = $field.find('input[type="number"], input[type="range"]').val();
                        break;

                    case 'image':
                        // For image fields, get the attachment ID from hidden input
                        value = $field.find('input[type="hidden"]').val();
                        break;

                    case 'gallery':
                        // For gallery fields, collect all attachment IDs
                        var galleryIds = [];

                        // Debug: Log the field structure
                        console.log('Aurora Wizard - Gallery field:', fieldName);
                        console.log('  - Field HTML:', $field[0]);

                        // Try to find gallery attachments
                        var $attachments = $field.find('.acf-gallery-attachment');
                        console.log('  - Found attachments:', $attachments.length);

                        $attachments.each(function() {
                            var $attachment = $(this);
                            var id = $attachment.data('id');
                            console.log('  - Attachment ID:', id);
                            if (id) {
                                galleryIds.push(id);
                            }
                        });

                        console.log('  - Final gallery IDs:', galleryIds);
                        value = galleryIds.length > 0 ? galleryIds.join(',') : '';
                        break;

                    case 'repeater':
                        // For repeater fields, collect all rows
                        var repeaterData = [];

                        // Try multiple selectors to support different ACF repeater layouts
                        var $rows = $field.find('> .acf-input > .acf-repeater > .acf-table > tbody > .acf-row:not(.acf-clone)');

                        if ($rows.length === 0) {
                            // Alternative for non-table layouts
                            $rows = $field.find('.acf-repeater > .acf-table > tbody > .acf-row:not(.acf-clone)');
                        }

                        if ($rows.length === 0) {
                            // Fallback: any row in repeater (excluding clones)
                            $rows = $field.find('.acf-row:not(.acf-clone)').not(function() {
                                return $(this).closest('.acf-clone').length > 0;
                            });
                        }

                        console.log('Aurora Wizard - Repeater field:', fieldName, '| Rows found:', $rows.length);

                        $rows.each(function(index) {
                            var $row = $(this);
                            var rowData = {};

                            // Try both standard and alternative selectors for sub-fields
                            var $subFields = $row.find('> .acf-fields > .acf-field[data-name]');
                            if ($subFields.length === 0) {
                                // Alternative: find any direct child field
                                $subFields = $row.find('.acf-field[data-name]').filter(function() {
                                    return $(this).closest('.acf-row')[0] === $row[0];
                                });
                            }

                            $subFields.each(function() {
                                var $subField = $(this);
                                var subFieldName = $subField.attr('data-name');
                                var subFieldKey = $subField.attr('data-key');
                                var subFieldType = $subField.attr('data-type');
                                var subValue = null;

                                if (subFieldType === 'image') {
                                    subValue = $subField.find('input[type="hidden"]').val();
                                } else if (subFieldType === 'gallery') {
                                    // For gallery fields within repeaters, collect all attachment IDs
                                    var galleryIds = [];
                                    $subField.find('.acf-gallery-attachment').each(function() {
                                        var id = $(this).data('id');
                                        if (id) {
                                            galleryIds.push(id);
                                        }
                                    });
                                    subValue = galleryIds.length > 0 ? galleryIds.join(',') : '';
                                    console.log('Aurora Wizard - Gallery in repeater:', subFieldName, 'IDs:', galleryIds);
                                } else if (subFieldType === 'wysiwyg') {
                                    // For WYSIWYG fields within repeaters, check TinyMCE
                                    var editorId = $subField.find('textarea').attr('id');
                                    if (editorId && typeof tinymce !== 'undefined') {
                                        var editor = tinymce.get(editorId);
                                        subValue = editor ? editor.getContent() : $subField.find('textarea').val();
                                    } else {
                                        subValue = $subField.find('textarea').val();
                                    }
                                    console.log('Aurora Wizard - WYSIWYG in repeater:', subFieldName, 'Length:', (subValue || '').length);
                                } else if (subFieldType === 'number') {
                                    subValue = $subField.find('input[type="number"]').val();
                                } else if (subFieldType === 'select') {
                                    subValue = $subField.find('select').val();
                                } else if (subFieldType === 'date_picker') {
                                    subValue = $subField.find('input[type="hidden"]').val();
                                } else {
                                    subValue = $subField.find('input, textarea, select').not('[type="hidden"]').first().val();
                                }

                                // Use field key for sub-fields for more reliable saving
                                rowData[subFieldKey || subFieldName] = subValue;
                                console.log('  Row', index, '-', subFieldName, ':', subValue, '(type:', subFieldType + ')');
                            });

                            // Only add row if it has at least one non-empty value
                            var hasData = Object.values(rowData).some(function(val) {
                                return val !== '' && val !== null && val !== undefined;
                            });

                            if (hasData) {
                                repeaterData.push(rowData);
                            }
                        });

                        value = repeaterData;
                        break;

                    default:
                        value = $field.find('input, textarea, select').not('[type="hidden"]').first().val();
                }

                if (fieldName) {
                    // Use field key for more reliable saving
                    var saveKey = fieldKey || fieldName;

                    // Only save if we don't already have this field
                    // (prevents duplicates from repeater sub-fields)
                    if (!data.hasOwnProperty(saveKey)) {
                        data[saveKey] = value;
                    }
                }
            });

            // Collect taxonomy data (for hierarchical taxonomies like FAQ, destinations, etc.)
            $('.categorychecklist input[type="checkbox"]:checked').each(function() {
                var $checkbox = $(this);
                var taxonomy = $checkbox.closest('.postbox').attr('id').replace('div', '');
                var termId = $checkbox.val();

                if (!data['tax_input']) {
                    data['tax_input'] = {};
                }
                if (!data['tax_input'][taxonomy]) {
                    data['tax_input'][taxonomy] = [];
                }
                data['tax_input'][taxonomy].push(termId);
            });

            // Collect non-hierarchical taxonomy data (tags)
            $('div[id^="tagsdiv-"] input.newtag').each(function() {
                var $input = $(this);
                var taxonomy = $input.closest('div[id^="tagsdiv-"]').attr('id').replace('tagsdiv-', '');
                var tags = $input.val();

                if (tags) {
                    if (!data['tax_input']) {
                        data['tax_input'] = {};
                    }
                    data['tax_input'][taxonomy] = tags;
                }
            });

            console.log('Aurora Wizard - Collected field data:', data);
            console.log('Aurora Wizard - Total fields collected:', Object.keys(data).length);
            console.log('Aurora Wizard - Taxonomy data:', data['tax_input']);
            return data;
        },

        /**
         * Save draft (without navigation)
         */
        saveDraft: function() {
            var self = this;

            if (this.saving) {
                console.log('Aurora Wizard - Already saving, please wait...');
                return;
            }

            console.log('Aurora Wizard - Saving draft...');
            this.saveStep().then(function() {
                self.showNotice('✓ Draft saved successfully!', 'success', 3000);
                console.log('Aurora Wizard - Draft saved successfully');
            }).fail(function() {
                self.showNotice('✗ Failed to save draft. Please try again.', 'error', 5000);
                console.log('Aurora Wizard - Failed to save draft');
            });
        },

        /**
         * Trigger auto-save
         */
        triggerAutoSave: function() {
            var self = this;
            clearTimeout(this.autoSaveTimer);

            this.autoSaveTimer = setTimeout(function() {
                if (!self.saving && typeof wp !== 'undefined' && wp.autosave) {
                    wp.autosave.server.triggerSave();
                    self.showNotice(auroraWizard.labels.autoSaved, 'info', 2000);
                }
            }, 3000);
        },

        /**
         * Update UI elements
         */
        updateUI: function() {
            this.updateProgressBar();
            this.updateStepIndicators();
            this.updateNavigationButtons();
        },

        /**
         * Update progress bar
         */
        updateProgressBar: function() {
            var percentage = Math.round(((this.currentStep + 1) / this.totalSteps) * 100);
            $('.wizard-progress-bar-fill').css('width', percentage + '%');
            $('.wizard-progress-percentage').text(percentage + '%');
        },

        /**
         * Update step indicators
         */
        updateStepIndicators: function() {
            var self = this;

            $('.wizard-step-indicator').each(function(index) {
                var $indicator = $(this);

                $indicator.removeClass('active completed');

                if (index < self.currentStep) {
                    $indicator.addClass('completed');
                } else if (index === self.currentStep) {
                    $indicator.addClass('active');
                }
            });
        },

        /**
         * Update navigation buttons
         */
        updateNavigationButtons: function() {
            var $backBtn = $('.wizard-nav-back');
            var $nextBtn = $('.wizard-nav-next');

            // Show/hide back button
            if (this.currentStep === 0) {
                $backBtn.hide();
            } else {
                $backBtn.show();
            }

            // Update next button text
            if (this.currentStep === this.totalSteps - 1) {
                $nextBtn.text(auroraWizard.labels.publish + ' ✓');
            } else {
                var nextStepName = this.steps[this.currentStep + 1];
                var nextStepLabel = auroraWizard.steps[nextStepName].label;
                $nextBtn.text('Next: ' + nextStepLabel + ' →');
            }
        },

        /**
         * Show notice message
         */
        showNotice: function(message, type, duration) {
            type = type || 'info';
            duration = duration || 3000;

            var $notice = $('<div class="wizard-notice wizard-notice-' + type + '">' + message + '</div>');

            $('#wizard-notice-area').append($notice);

            if (duration > 0) {
                setTimeout(function() {
                    $notice.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, duration);
            }
        },

        /**
         * Show validation error (deprecated - use showValidationErrors)
         */
        showValidationError: function() {
            this.showNotice(auroraWizard.labels.validationError, 'error');
            this.scrollToFirstError();
        },

        /**
         * Show validation errors with specific field names
         */
        showValidationErrors: function(errorMessages) {
            if (errorMessages && errorMessages.length > 0) {
                var message = '<strong>Please complete the following required fields:</strong><ul style="margin: 10px 0 0 0; padding-left: 20px;">';
                errorMessages.forEach(function(msg) {
                    message += '<li>' + msg + '</li>';
                });
                message += '</ul>';

                // Show in a more prominent error box
                var $errorBox = $('<div class="wizard-validation-error-box"></div>').html(message);
                $('#wizard-notice-area').append($errorBox);

                // Auto-remove after 10 seconds
                setTimeout(function() {
                    $errorBox.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 10000);
            } else {
                this.showValidationError();
            }

            this.scrollToFirstError();
        },

        /**
         * Scroll to first error field
         */
        scrollToFirstError: function() {
            var $firstError = $('.wizard-field-error').first();
            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 100
                }, 300);
            }
        },

        /**
         * Add character counter to summary field
         */
        addCharacterCounter: function() {
            var $summaryField = $('#acf-field_package_summary');

            if ($summaryField.length) {
                var $counter = $('<div class="wizard-char-counter"><span class="current">0</span>/200 characters</div>');
                $summaryField.after($counter);

                $summaryField.on('input', function() {
                    var length = $(this).val().length;
                    $counter.find('.current').text(length);

                    if (length > 200) {
                        $counter.addClass('over-limit');
                    } else {
                        $counter.removeClass('over-limit');
                    }
                });

                // Trigger initial count
                $summaryField.trigger('input');
            }
        },

        /**
         * Mark all ACF fields as unmodified
         * Resets the "changed" state to prevent beforeunload warnings
         */
        markFieldsAsUnmodified: function() {
            // Reset ACF's internal change tracking
            if (typeof acf !== 'undefined') {
                // ACF uses a validation object to track changes
                if (acf.validation) {
                    acf.validation.errors = [];
                }

                // Remove the changed class from all fields
                $('.acf-field').removeClass('acf-changed');
            }

            // Clear WordPress's tracking
            if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
                try {
                    var dispatch = wp.data.dispatch('core/editor');
                    if (dispatch && dispatch.resetEditorBlocks) {
                        // Mark blocks as saved
                        dispatch.resetEditorBlocks();
                    }
                } catch (e) {
                    // Silently fail if Gutenberg isn't available
                }
            }
        },

        /**
         * Prevent beforeunload warnings globally for wizard
         * Called once during initialization
         */
        preventBeforeUnloadWarnings: function() {
            var self = this;

            // Override WordPress's beforeunload check
            if (typeof wp !== 'undefined' && wp.data && wp.data.select) {
                // For Gutenberg-based editors
                var originalSelect = wp.data.select;
                wp.data.select = function(storeName) {
                    var store = originalSelect(storeName);
                    if (storeName === 'core/editor' && store && store.isEditedPostDirty) {
                        // Override to always return false (post is not dirty)
                        var originalStore = Object.assign({}, store);
                        originalStore.isEditedPostDirty = function() { return false; };
                        return originalStore;
                    }
                    return store;
                };
            }

            // Intercept any beforeunload event additions
            var originalAddEventListener = window.addEventListener;
            window.addEventListener = function(type, listener, options) {
                if (type === 'beforeunload' && $('body').hasClass('post-type-package')) {
                    // Don't add beforeunload listeners on Package edit screen
                    console.log('Aurora Wizard: Blocked beforeunload listener');
                    return;
                }
                return originalAddEventListener.call(this, type, listener, options);
            };

            // Remove any existing beforeunload handlers
            this.disableBeforeUnload();
        },

        /**
         * Disable WordPress beforeunload warning
         * This prevents the "Do you want to leave this site?" popup
         * when navigating between wizard steps
         */
        disableBeforeUnload: function() {
            // Remove all beforeunload event listeners
            $(window).off('beforeunload.edit-post');
            $(window).off('beforeunload');

            // Remove the unload check
            window.onbeforeunload = null;

            // Disable WordPress autosave check
            if (typeof wp !== 'undefined' && wp.autosave) {
                // Store original function
                if (!this.originalTriggerSave) {
                    this.originalTriggerSave = wp.autosave.server.triggerSave;
                }
                // Override to do nothing
                wp.autosave.server.triggerSave = function() {
                    console.log('Aurora Wizard: Autosave triggered (suppressed)');
                };
            }
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        if ($('body').hasClass('post-type-package') && $('#aurora-wizard-container').length) {
            window.AuroraWizard.init();
            window.AuroraWizard.addCharacterCounter();

            // Global init function for template
            window.auroraWizardInit = function() {
                window.AuroraWizard.init();
            };
        }
    });

})(jQuery);
