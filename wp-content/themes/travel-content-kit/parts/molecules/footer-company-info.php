<?php
/**
 * Molecule: Footer Company Info
 * Displays company registration and physical address
 * Centered below reviews section
 *
 * @package Travel_Child_Theme
 * @since 1.0.0
 */

// Get ACF options with fallbacks
// Check if ACF is available
if (!function_exists('get_field')) {
    function get_field($field, $context = null) { return null; }
}

$company_ruc = get_field('company_ruc', 'option') ?: '20490568957';
$company_address = get_field('company_address', 'option') ?: 'Portal Panes #123 / Centro Comercial Ruiseñores Office #306–307 Cusco — Peru';
?>

<div class="footer-company-info">
    <p class="footer-company-info__ruc">
        <span class="footer-company-info__label">RUC #:</span>
        <span class="footer-company-info__value"><?php echo esc_html($company_ruc); ?></span>
    </p>
    <p class="footer-company-info__address">
        <span class="footer-company-info__label">Address:</span>
        <span class="footer-company-info__value"><?php echo esc_html($company_address); ?></span>
    </p>
</div>
