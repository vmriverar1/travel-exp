<?php
/**
 * Molecule: Contact Info
 * Bloque de información de contacto (administrable vía ACF Options)
 */

// Obtener datos de ACF Options (los crearemos en Fase 4)
// Check if ACF is available
if (!function_exists('get_field')) {
    function get_field($field, $context = null) { return null; }
}

$toll_free = get_field('contact_toll_free', 'option') ?: '1-(888)-803-8004';
$peru_phone = get_field('contact_peru_phone', 'option') ?: '+51 84 255907';
$phone_24_7_1 = get_field('contact_phone_24_7_1', 'option') ?: '+51 992 236 677';
$phone_24_7_2 = get_field('contact_phone_24_7_2', 'option') ?: '+51 979706446';
$email = get_field('contact_email', 'option') ?: 'info@machupicchuperu.com';

// Office hours
$office_weekdays = get_field('office_weekdays', 'option') ?: 'Monday through Saturday';
$office_morning = get_field('office_morning', 'option') ?: '8AM – 1:30PM';
$office_afternoon = get_field('office_afternoon', 'option') ?: '3PM – 5:30PM';
$office_sunday = get_field('office_sunday', 'option') ?: 'Sunday 8AM – 1:30PM';
?>

<div class="contact-info">
    <h3 class="contact-info__title">Contact us</h3>

    <div class="contact-info__block">
        <p class="contact-info__item">
            <span class="contact-info__label">Toll Free (USA/Canada):</span>
            <a href="tel:<?php echo esc_attr(str_replace(['-', '(', ')', ' '], '', $toll_free)); ?>" class="contact-info__link">
                <?php echo esc_html($toll_free); ?>
            </a>
        </p>

        <p class="contact-info__item">
            <span class="contact-info__label">Peru office phone:</span>
            <a href="tel:<?php echo esc_attr(str_replace(['-', ' '], '', $peru_phone)); ?>" class="contact-info__link">
                <?php echo esc_html($peru_phone); ?>
            </a>
        </p>

        <p class="contact-info__item">
            <span class="contact-info__label">24/7 phone:</span>
            <a href="tel:<?php echo esc_attr(str_replace(['-', ' '], '', $phone_24_7_1)); ?>" class="contact-info__link">
                <?php echo esc_html($phone_24_7_1); ?>
            </a>
            or
            <a href="tel:<?php echo esc_attr(str_replace(['-', ' '], '', $phone_24_7_2)); ?>" class="contact-info__link">
                <?php echo esc_html($phone_24_7_2); ?>
            </a>
        </p>

        <p class="contact-info__item">
            <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-info__link contact-info__link--email">
                <?php echo esc_html($email); ?>
            </a>
        </p>
    </div>

    <div class="contact-info__block">
        <h4 class="contact-info__subtitle">Office Hours</h4>
        <p class="contact-info__schedule">
            <strong>Sales & Administrations Teams Hours</strong><br>
            <?php echo esc_html($office_weekdays); ?><br>
            <?php echo esc_html($office_morning); ?>, <?php echo esc_html($office_afternoon); ?><br>
            <?php echo esc_html($office_sunday); ?><br>
            <span class="contact-info__closed">Sunday closed</span>
        </p>
        <p class="contact-info__schedule">
            <strong>Operations support</strong><br>
            Monday through Sunday 24/7
        </p>
    </div>
</div>
