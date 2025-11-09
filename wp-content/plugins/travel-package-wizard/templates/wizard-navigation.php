<?php
/**
 * Wizard Navigation Template
 * Renders Next/Back buttons at the bottom of the page
 */

if (!defined('ABSPATH')) {
    exit;
}

$controller = Aurora_Wizard_Controller::get_instance();
$steps = $controller->get_steps();
$current_step = isset($_GET['wizard_step']) ? sanitize_text_field($_GET['wizard_step']) : 'basic';
$current_index = $controller->get_step_index($current_step);
$total_steps = count($steps);
$step_keys = array_keys($steps);

$show_back = $current_index > 0;
$show_next = $current_index < ($total_steps - 1);
$is_last_step = $current_index === ($total_steps - 1);

$prev_step = $show_back ? $step_keys[$current_index - 1] : '';
$next_step = $show_next ? $step_keys[$current_index + 1] : '';
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Move wizard navigation into post body
    var $navigation = $('#wizard-navigation-container');
    var $postBody = $('#post-body-content');

    if ($navigation.length && $postBody.length) {
        // Insert after all ACF field groups
        $('.acf-postbox:visible:last').after($navigation);
        $navigation.show();
    }

    // Make sure navigation is always visible
    $navigation.css({
        'position': 'sticky',
        'bottom': '0',
        'z-index': '999',
        'background': 'white',
        'padding': '20px',
        'box-shadow': '0 -2px 10px rgba(0,0,0,0.1)',
        'margin-top': '30px'
    });
});
</script>

<div id="wizard-navigation-container" class="wizard-navigation" style="display: none;">

    <div class="wizard-navigation-inner">

        <!-- Back Button -->
        <?php if ($show_back): ?>
        <button type="button"
                class="button button-large wizard-nav-back wizard-nav-btn"
                data-prev-step="<?php echo esc_attr($prev_step); ?>">
            ← <?php _e('Back', 'travel-package-wizard'); ?>
        </button>
        <?php else: ?>
        <span></span>
        <?php endif; ?>

        <!-- Middle: Save Draft Button -->
        <button type="button"
                class="button button-large wizard-nav-save wizard-nav-btn"
                id="wizard-save-draft">
            <?php _e('Save Draft', 'travel-package-wizard'); ?>
        </button>

        <!-- Next/Publish Button -->
        <?php if ($is_last_step): ?>
        <button type="submit"
                name="publish"
                class="button button-primary button-large wizard-nav-next wizard-nav-btn"
                id="wizard-publish">
            <?php _e('Publish Package', 'travel-package-wizard'); ?> ✓
        </button>
        <?php else: ?>
        <button type="button"
                class="button button-primary button-large wizard-nav-next wizard-nav-btn"
                data-next-step="<?php echo esc_attr($next_step); ?>">
            <?php printf(__('Next: %s', 'travel-package-wizard'), esc_html($steps[$next_step]['label'])); ?> →
        </button>
        <?php endif; ?>

    </div>

    <!-- Validation Messages -->
    <div id="wizard-validation-messages" class="wizard-validation-messages" style="display: none;"></div>

</div>
