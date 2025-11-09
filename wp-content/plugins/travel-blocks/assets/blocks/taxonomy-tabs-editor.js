/**
 * Taxonomy Tabs - Editor Filter Script
 * Filters the "Término" select in repeater to show only selected checkboxes
 * Only regenerates select when checkboxes actually change
 */

(function($) {
    'use strict';

    if (typeof acf === 'undefined') {
        return;
    }

    // Store last selected IDs to detect changes
    let lastSelectedIds = [];
    let allOptions = {}; // Store all available options

    /**
     * Get all selected IDs from checkboxes (individual terms and complete taxonomies)
     */
    function getSelectedIds() {
        const selectedIds = [];

        // Individual terms checkboxes
        const checkboxFields = [
            'tt_selected_terms_package_type',
            'tt_selected_terms_interest',
            'tt_selected_locations_cpt',
            'tt_selected_terms_category',
            'tt_selected_terms_post_tag'
        ];

        checkboxFields.forEach(function(fieldName) {
            const $field = $('[data-name="' + fieldName + '"]');
            if ($field.length) {
                $field.find('input[type="checkbox"]:checked').each(function() {
                    const idValue = $(this).val();
                    if (idValue && idValue !== '' && idValue !== '0') {
                        selectedIds.push(idValue);
                    }
                });
            }
        });

        // Complete taxonomies checkboxes (Package source)
        const $taxonomiesPackage = $('[data-name="tt_selected_taxonomies_package"]');
        if ($taxonomiesPackage.length) {
            $taxonomiesPackage.find('input[type="checkbox"]:checked').each(function() {
                const taxValue = $(this).val();
                if (taxValue && taxValue !== '' && taxValue !== '0') {
                    // These are taxonomy slugs: package_type, interest, locations_cpt
                    selectedIds.push(taxValue);
                }
            });
        }

        // Complete taxonomies checkboxes (Post source)
        const $taxonomiesPost = $('[data-name="tt_selected_taxonomies_post"]');
        if ($taxonomiesPost.length) {
            $taxonomiesPost.find('input[type="checkbox"]:checked').each(function() {
                const taxValue = $(this).val();
                if (taxValue && taxValue !== '' && taxValue !== '0') {
                    // These are taxonomy slugs: category, post_tag
                    selectedIds.push(taxValue);
                }
            });
        }

        return selectedIds;
    }

    /**
     * Save all original options from a select
     */
    function saveAllOptions($select) {
        const selectKey = $select.attr('name') || 'default';

        if (!allOptions[selectKey]) {
            allOptions[selectKey] = [];
            $select.find('option').each(function() {
                const $option = $(this);
                allOptions[selectKey].push({
                    value: $option.val(),
                    text: $option.text(),
                    html: $option.prop('outerHTML')
                });
            });
        }
    }

    /**
     * Rebuild select with only selected IDs
     */
    function rebuildSelect($select, selectedIds) {
        const selectKey = $select.attr('name') || 'default';
        const currentValue = $select.val();

        // Save options if not saved yet
        saveAllOptions($select);

        // Clear select
        $select.empty();

        // Add empty option
        $select.append('<option value="">Selecciona un término</option>');

        // Add only selected options
        allOptions[selectKey].forEach(function(optionData) {
            if (optionData.value === '' || optionData.value === '0') {
                return; // Skip, already added
            }

            if (selectedIds.includes(optionData.value)) {
                $select.append(optionData.html);
            }
        });

        // Restore value if still available
        if (selectedIds.includes(currentValue)) {
            $select.val(currentValue);
        }

        // Trigger Select2 update if present
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change.select2');
        }
    }

    /**
     * Filter select options based on selected checkboxes
     * Only rebuilds if selection actually changed
     */
    function filterTermSelect() {
        const selectedIds = getSelectedIds();

        // Check if selection changed
        const idsString = JSON.stringify(selectedIds.sort());
        const lastIdsString = JSON.stringify(lastSelectedIds.sort());
        const hasChanged = idsString !== lastIdsString;

        if (!hasChanged && lastSelectedIds.length > 0) {
            // No change detected, skip rebuild
            return;
        }

        if (hasChanged) {
            console.log('TaxonomyTabs: Checkboxes changed. Selected IDs:', selectedIds);
            lastSelectedIds = selectedIds.slice(); // Clone array
        }

        // Rebuild all term_id selects in the repeater
        $('[data-name="term_id"] select').each(function() {
            rebuildSelect($(this), selectedIds);
        });
    }

    /**
     * Check for changes periodically (without rebuilding unless needed)
     */
    function checkForChanges() {
        const currentIds = getSelectedIds();
        const idsString = JSON.stringify(currentIds.sort());
        const lastIdsString = JSON.stringify(lastSelectedIds.sort());

        if (idsString !== lastIdsString) {
            console.log('TaxonomyTabs: Change detected in interval check');
            filterTermSelect();
        }
    }

    /**
     * Initialize when ACF is ready
     */
    acf.addAction('ready', function() {
        console.log('TaxonomyTabs: ACF Ready - Initializing filter');
        filterTermSelect();

        // Check for changes every 2 seconds (but only rebuild if changed)
        setInterval(checkForChanges, 2000);
    });

    /**
     * Re-filter when checkboxes change (immediate response)
     */
    acf.addAction('change', function(e) {
        const $field = $(e.$el);
        const fieldName = $field.data('name');

        const checkboxFields = [
            'tt_selected_terms_package_type',
            'tt_selected_terms_interest',
            'tt_selected_locations_cpt',
            'tt_selected_terms_category',
            'tt_selected_terms_post_tag',
            'tt_selected_taxonomies_package',
            'tt_selected_taxonomies_post'
        ];

        if (checkboxFields.includes(fieldName)) {
            console.log('TaxonomyTabs: Checkbox changed:', fieldName);
            setTimeout(filterTermSelect, 100);
        }
    });

    /**
     * Re-filter when repeater adds new row
     */
    acf.addAction('append', function($el) {
        if ($el.closest('[data-name="tt_tab_overrides"]').length) {
            console.log('TaxonomyTabs: Repeater row added');
            setTimeout(filterTermSelect, 200);
        }
    });

    /**
     * Initialize new select when field loads
     */
    acf.addAction('load_field', function(e) {
        const $field = $(e.$el);
        const fieldName = $field.data('name');

        if (fieldName === 'term_id') {
            const $select = $field.find('select');
            if ($select.length) {
                saveAllOptions($select);
                setTimeout(filterTermSelect, 100);
            }
        }
    });

})(jQuery);
