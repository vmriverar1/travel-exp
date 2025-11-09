<?php
/**
 * Atom: Payment Icon
 *
 * @param string $method - visa|mastercard|amex|discover|paypal|stripe|flywire
 */

$method = $args['method'] ?? 'visa';
$image_path = get_stylesheet_directory_uri() . "/assets/images/payment-methods/{$method}.svg";

$labels = [
    'visa' => 'Visa',
    'mastercard' => 'Mastercard',
    'amex' => 'American Express',
    'discover' => 'Discover',
    'paypal' => 'PayPal',
    'stripe' => 'Stripe',
    'flywire' => 'Flywire',
];

$label = $labels[$method] ?? ucfirst($method);
?>

<span class="payment-icon payment-icon--<?php echo esc_attr($method); ?>">
    <img
        src="<?php echo esc_url($image_path); ?>"
        alt="<?php echo esc_attr($label); ?>"
        class="payment-icon__img"
        loading="lazy"
        width="40"
        height="25"
    >
</span>
