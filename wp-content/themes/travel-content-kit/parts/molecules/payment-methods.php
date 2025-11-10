<?php
/**
 * Molecule: Payment Methods
 * Bloque de métodos de pago aceptados
 */

// Get ACF data with fallback to hardcoded values
$payment_methods = function_exists('get_field') ? (get_field('payment_methods', 'option') ?: []) : [];
$gateways = function_exists('get_field') ? (get_field('payment_gateways', 'option') ?: []) : [];
?>

<div class="payment-methods">
    <h3 class="payment-methods__title">Payment methods</h3>

    <?php if (!empty($payment_methods)): ?>
    <div class="payment-methods__icons">
        <?php foreach ($payment_methods as $method): ?>
            <?php if (!empty($method['image'])): ?>
                <div class="payment-methods__icon">
                    <img
                        src="<?php echo esc_url($method['image']); ?>"
                        alt="<?php echo esc_attr($method['name']); ?>"
                        loading="lazy"
                    />
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($gateways)): ?>
    <div class="payment-methods__gateways">
        <span class="payment-methods__label">Pay by:</span>
        <?php foreach ($gateways as $index => $gateway): ?>
            <?php if (!empty($gateway['url'])): ?>
                <a
                    href="<?php echo esc_url($gateway['url']); ?>"
                    class="payment-methods__gateway-link"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                   <img
                        src="<?php echo esc_url($gateway['image']); ?>"
                        loading="lazy"
                    />
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
