<?php
/**
 * Wizard Layout Template
 * Injected into Package edit screen header
 */

if (!defined('ABSPATH')) {
    exit;
}

$controller = Aurora_Wizard_Controller::get_instance();
$steps = $controller->get_steps();
$current_step = isset($_GET['wizard_step']) ? sanitize_text_field($_GET['wizard_step']) : 'basic';
$current_index = $controller->get_step_index($current_step);
$total_steps = count($steps);
$progress_percentage = round((($current_index + 1) / $total_steps) * 100);
?>

<style>
    /* Hide default WordPress elements */
    body.post-type-package #postdivrich,
    body.post-type-package #postexcerpt {
        display: none !important;
    }
</style>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Initialize wizard immediately
        if (typeof window.auroraWizardInit === 'function') {
            window.auroraWizardInit();
        }

        // Hide ACF metaboxes initially (wizard will show/hide them)
        $('.acf-postbox').hide();

        // Hide taxonomy metaboxes initially
        $('.postbox[id*="div"]').each(function() {
            var id = $(this).attr('id');
            // Hide taxonomy metaboxes (destinationsdiv, faqdiv, etc.)
            if (id && (id.endsWith('div') || id.startsWith('tagsdiv-'))) {
                $(this).hide();
            }
        });

        // Open all ACF field groups by default (no collapsed)
        $('.acf-postbox .handlediv').remove(); // Remove collapse handle
        $('.acf-postbox').removeClass('closed'); // Ensure not collapsed

        // Show only current step metaboxes
        var currentStep = '<?php echo esc_js($current_step); ?>';
        var stepMetaboxes = <?php echo json_encode(array_values($steps)); ?>;

        // Find current step metaboxes
        var currentStepData = stepMetaboxes.find(function(step) {
            var stepKeys = Object.keys(<?php echo json_encode($steps); ?>);
            return stepKeys[stepMetaboxes.indexOf(step)] === currentStep;
        });

        if (currentStepData) {
            // Show ACF metaboxes
            if (currentStepData.metaboxes) {
                currentStepData.metaboxes.forEach(function(metaboxId) {
                    $('#acf-' + metaboxId).show().wrap('<div class="wizard-step active" data-step="' + currentStep + '"></div>');
                });
            }

            // Show taxonomy metaboxes
            if (currentStepData.taxonomies) {
                currentStepData.taxonomies.forEach(function(taxonomy) {
                    // Hierarchical taxonomies use: {taxonomy}div
                    $('#' + taxonomy + 'div').show().wrap('<div class="wizard-step active" data-step="' + currentStep + '"></div>');
                    // Non-hierarchical taxonomies use: tagsdiv-{taxonomy}
                    $('#tagsdiv-' + taxonomy).show().wrap('<div class="wizard-step active" data-step="' + currentStep + '"></div>');
                });
            }
        }
    });
</script>

<div class="wizard-container" id="aurora-wizard-container">

    <!-- Progress Bar -->
    <div class="wizard-progress-wrapper">
        <div class="wizard-progress-bar">
            <div class="wizard-progress-bar-fill" style="width: <?php echo esc_attr($progress_percentage); ?>%"></div>
            <span class="wizard-progress-percentage"><?php echo esc_html($progress_percentage); ?>%</span>
        </div>
    </div>

    <!-- Step Indicators -->
    <div class="wizard-step-indicators">
        <?php
        $step_index = 0;
        foreach ($steps as $step_key => $step_data):
            $is_active = ($step_key === $current_step);
            $is_completed = ($step_index < $current_index);
            $classes = ['wizard-step-indicator'];

            if ($is_active) {
                $classes[] = 'active';
            }
            if ($is_completed) {
                $classes[] = 'completed';
            }
        ?>
        <div class="<?php echo esc_attr(implode(' ', $classes)); ?>"
             data-step="<?php echo esc_attr($step_key); ?>"
             data-step-index="<?php echo esc_attr($step_index); ?>">
            <div class="wizard-step-indicator-circle">
                <?php if (!$is_completed): ?>
                    <span class="wizard-step-icon"><?php echo esc_html($step_data['icon']); ?></span>
                <?php endif; ?>
            </div>
            <div class="wizard-step-indicator-label">
                <?php echo esc_html($step_data['label']); ?>
            </div>
        </div>
        <?php
            $step_index++;
        endforeach;
        ?>
    </div>

    <!-- Wizard Notice Area -->
    <div id="wizard-notice-area"></div>

</div>
