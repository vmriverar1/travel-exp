<?php
/**
 * Travel Integrations Settings Page
 *
 * @package Travel\Integrations
 */

defined('ABSPATH') || exit;

// Handle manual sync if requested
$sync_results = null;
if (isset($_GET['sync_reviews']) && check_admin_referer('sync_reviews_manual')) {
    $syncer = new \Travel\Integrations\Reviews\ReviewsSyncer();
    $sync_results = $syncer->sync_all_reviews();
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if ($sync_results !== null): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php _e('Reviews Sync Completed!', 'travel-integrations'); ?></strong><br>
                <?php foreach ($sync_results as $platform => $result): ?>
                    <?php if ($result['success']): ?>
                        <?php echo esc_html(ucfirst($platform)); ?>: <?php echo esc_html($result['synced']); ?>/<?php echo esc_html($result['total']); ?> synced<br>
                    <?php else: ?>
                        <?php echo esc_html(ucfirst($platform)); ?>: <?php echo esc_html($result['message']); ?><br>
                    <?php endif; ?>
                <?php endforeach; ?>
            </p>
        </div>
    <?php endif; ?>

    <?php settings_errors(); ?>

    <h2 class="nav-tab-wrapper">
        <a href="#reviews" class="nav-tab nav-tab-active"><?php _e('Reviews', 'travel-integrations'); ?></a>
        <a href="#payments" class="nav-tab"><?php _e('Payments', 'travel-integrations'); ?></a>
    </h2>

    <form method="post" action="options.php">
        <?php settings_fields('travel_integrations'); ?>

        <!-- Reviews Tab -->
        <div id="reviews-tab" class="tab-content">
            <h2><?php _e('Reviews Integrations', 'travel-integrations'); ?></h2>

            <!-- TripAdvisor -->
            <table class="form-table">
                <tr>
                    <th colspan="2"><h3><?php _e('TripAdvisor', 'travel-integrations'); ?></h3></th>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Enable TripAdvisor', 'travel-integrations'); ?></th>
                    <td>
                        <input type="checkbox" name="travel_tripadvisor_enabled" value="1" <?php checked(1, get_option('travel_tripadvisor_enabled', false)); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('API Key', 'travel-integrations'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="travel_tripadvisor_api_key" value="<?php echo esc_attr(get_option('travel_tripadvisor_api_key', '')); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Location ID', 'travel-integrations'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="travel_tripadvisor_location_id" value="<?php echo esc_attr(get_option('travel_tripadvisor_location_id', '')); ?>" />
                    </td>
                </tr>
            </table>

            <!-- Google Reviews -->
            <table class="form-table">
                <tr>
                    <th colspan="2"><h3><?php _e('Google Reviews', 'travel-integrations'); ?></h3></th>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Enable Google Reviews', 'travel-integrations'); ?></th>
                    <td>
                        <input type="checkbox" name="travel_google_enabled" value="1" <?php checked(1, get_option('travel_google_enabled', false)); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('API Key', 'travel-integrations'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="travel_google_api_key" value="<?php echo esc_attr(get_option('travel_google_api_key', '')); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Place ID', 'travel-integrations'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="travel_google_place_id" value="<?php echo esc_attr(get_option('travel_google_place_id', '')); ?>" />
                    </td>
                </tr>
            </table>

            <!-- Facebook -->
            <table class="form-table">
                <tr>
                    <th colspan="2"><h3><?php _e('Facebook', 'travel-integrations'); ?></h3></th>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Enable Facebook', 'travel-integrations'); ?></th>
                    <td>
                        <input type="checkbox" name="travel_facebook_enabled" value="1" <?php checked(1, get_option('travel_facebook_enabled', false)); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Access Token', 'travel-integrations'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="travel_facebook_access_token" value="<?php echo esc_attr(get_option('travel_facebook_access_token', '')); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Page ID', 'travel-integrations'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="travel_facebook_page_id" value="<?php echo esc_attr(get_option('travel_facebook_page_id', '')); ?>" />
                    </td>
                </tr>
            </table>

            <p>
                <a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=travel-integrations&sync_reviews=1'), 'sync_reviews_manual'); ?>" class="button button-secondary">
                    <?php _e('Sync Reviews Now', 'travel-integrations'); ?>
                </a>
                <span class="description"><?php _e('Reviews are automatically synced daily.', 'travel-integrations'); ?></span>
            </p>
        </div>

        <!-- Payments Tab -->
        <div id="payments-tab" class="tab-content" style="display:none;">
            <h2><?php _e('Stripe Payment Integration', 'travel-integrations'); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Enable Stripe', 'travel-integrations'); ?></th>
                    <td>
                        <input type="checkbox" name="travel_stripe_enabled" value="1" <?php checked(1, get_option('travel_stripe_enabled', false)); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Test Mode', 'travel-integrations'); ?></th>
                    <td>
                        <input type="checkbox" name="travel_stripe_test_mode" value="1" <?php checked(1, get_option('travel_stripe_test_mode', true)); ?> />
                        <p class="description"><?php _e('Enable test mode for development', 'travel-integrations'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Publishable Key', 'travel-integrations'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="travel_stripe_publishable_key" value="<?php echo esc_attr(get_option('travel_stripe_publishable_key', '')); ?>" />
                        <p class="description"><?php _e('Starts with pk_test_ or pk_live_', 'travel-integrations'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Secret Key', 'travel-integrations'); ?></th>
                    <td>
                        <input type="password" class="regular-text" name="travel_stripe_secret_key" value="<?php echo esc_attr(get_option('travel_stripe_secret_key', '')); ?>" />
                        <p class="description"><?php _e('Starts with sk_test_ or sk_live_', 'travel-integrations'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Webhook Secret', 'travel-integrations'); ?></th>
                    <td>
                        <input type="password" class="regular-text" name="travel_stripe_webhook_secret" value="<?php echo esc_attr(get_option('travel_stripe_webhook_secret', '')); ?>" />
                        <p class="description">
                            <?php _e('Webhook URL:', 'travel-integrations'); ?>
                            <code><?php echo esc_url(rest_url('travel/v1/stripe/webhook')); ?></code>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.tab-content').hide();
        $(target + '-tab').show();
    });
});
</script>

<style>
.tab-content {
    padding: 20px 0;
}
.form-table h3 {
    margin: 0;
    padding: 0;
}
</style>
