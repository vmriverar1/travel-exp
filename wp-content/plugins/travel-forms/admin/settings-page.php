<?php
/**
 * Travel Forms Settings Page
 *
 * @package Travel\Forms
 */

defined('ABSPATH') || exit;

// Test HubSpot connection if requested
$connection_status = null;
if (isset($_GET['test_connection']) && check_admin_referer('test_hubspot_connection')) {
    $hubspot = new \Travel\Forms\Integrations\HubSpotAPI();
    $connection_status = $hubspot->test_connection();
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php if ($connection_status !== null): ?>
        <div class="notice notice-<?php echo $connection_status ? 'success' : 'error'; ?> is-dismissible">
            <p>
                <?php if ($connection_status): ?>
                    <strong><?php _e('Success!', 'travel-forms'); ?></strong>
                    <?php _e('HubSpot connection test successful.', 'travel-forms'); ?>
                <?php else: ?>
                    <strong><?php _e('Error!', 'travel-forms'); ?></strong>
                    <?php _e('HubSpot connection test failed. Please check your API key.', 'travel-forms'); ?>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields('travel_forms');
        do_settings_sections('travel_forms');
        submit_button();
        ?>
    </form>

    <?php if (get_option('travel_forms_hubspot_enabled', false)): ?>
        <hr>
        <h2><?php _e('Test HubSpot Connection', 'travel-forms'); ?></h2>
        <p><?php _e('Click the button below to test your HubSpot API connection.', 'travel-forms'); ?></p>
        <a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=travel-forms-settings&test_connection=1'), 'test_hubspot_connection'); ?>" class="button button-secondary">
            <?php _e('Test Connection', 'travel-forms'); ?>
        </a>
    <?php endif; ?>

    <hr>
    <h2><?php _e('Form Submissions', 'travel-forms'); ?></h2>
    <p><?php _e('View recent form submissions:', 'travel-forms'); ?></p>

    <?php
    // Get recent submissions
    $submissions = \Travel\Forms\Core\Database::get_submissions('', 10);

    if (!empty($submissions)):
    ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'travel-forms'); ?></th>
                    <th><?php _e('Form Type', 'travel-forms'); ?></th>
                    <th><?php _e('Name', 'travel-forms'); ?></th>
                    <th><?php _e('Email', 'travel-forms'); ?></th>
                    <th><?php _e('Status', 'travel-forms'); ?></th>
                    <th><?php _e('HubSpot', 'travel-forms'); ?></th>
                    <th><?php _e('Date', 'travel-forms'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php echo esc_html($submission['id']); ?></td>
                        <td><?php echo esc_html($submission['form_type']); ?></td>
                        <td><?php echo esc_html($submission['form_data']['name'] ?? '-'); ?></td>
                        <td><?php echo esc_html($submission['form_data']['email'] ?? '-'); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr($submission['status']); ?>">
                                <?php echo esc_html(ucfirst($submission['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($submission['hubspot_sent']): ?>
                                <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                                <?php if (!empty($submission['hubspot_contact_id'])): ?>
                                    <small><?php echo esc_html($submission['hubspot_contact_id']); ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="dashicons dashicons-minus"></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $submission['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?php _e('No submissions yet.', 'travel-forms'); ?></p>
    <?php endif; ?>

    <hr>
    <h2><?php _e('Shortcodes', 'travel-forms'); ?></h2>
    <p><?php _e('Use these shortcodes to display forms on your pages:', 'travel-forms'); ?></p>
    <ul>
        <li><code>[contact-form]</code> - <?php _e('Contact Form', 'travel-forms'); ?></li>
        <li><code>[booking-form]</code> - <?php _e('Booking Form', 'travel-forms'); ?></li>
        <li><code>[brochure-form]</code> - <?php _e('Brochure Request Form', 'travel-forms'); ?></li>
    </ul>
    <p><?php _e('Optional attributes:', 'travel-forms'); ?></p>
    <ul>
        <li><code>title</code> - <?php _e('Custom form title', 'travel-forms'); ?></li>
        <li><code>show_title</code> - <?php _e('Show/hide title (true/false)', 'travel-forms'); ?></li>
    </ul>
    <p><strong><?php _e('Example:', 'travel-forms'); ?></strong> <code>[contact-form title="Get in Touch" show_title="true"]</code></p>
</div>

<style>
.status-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.status-pending {
    background-color: #fef8e7;
    color: #856404;
}
.status-completed {
    background-color: #d4edda;
    color: #155724;
}
.status-failed {
    background-color: #f8d7da;
    color: #721c24;
}
</style>
