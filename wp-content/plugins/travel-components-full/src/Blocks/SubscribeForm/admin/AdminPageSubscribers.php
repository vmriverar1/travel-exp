<?php
namespace Travel\Components\Blocks\SubscribeForm\Admin;

if (!defined('ABSPATH')) exit;

class AdminPageSubscribers {

  public function register() {
    add_action('admin_menu', [$this, 'add_menu']);
    add_action('admin_post_tc_add_subscriber', [$this, 'handle_add']);
    add_action('admin_post_tc_delete_subscriber', [$this, 'handle_delete']);
    add_action('admin_post_tc_update_subscriber', [$this, 'handle_update']);
  }

  public function add_menu() {
    add_menu_page(
      'Subscribers',
      'Subscribers',
      'manage_options',
      'tc-subscribers',
      [$this, 'render_page'],
      'dashicons-email-alt2',
      25
    );
  }

  public function render_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'subscribers';
    $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $editing = $edit_id ? $wpdb->get_row("SELECT * FROM $table WHERE id = $edit_id") : null;
    $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    echo '<div class="wrap"><h1>Subscribers</h1>';

    echo '<h2>' . ($editing ? 'Edit Subscriber' : 'Add Subscriber') . '</h2>';
    echo '<form method="POST" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="' . ($editing ? 'tc_update_subscriber' : 'tc_add_subscriber') . '">';
    if ($editing) echo '<input type="hidden" name="id" value="' . esc_attr($editing->id) . '">';
    wp_nonce_field('tc_subscriber_action', 'tc_nonce');

    echo '<table class="form-table">
      <tr><th><label for="name">Name</label></th>
          <td><input name="name" id="name" type="text" value="' . esc_attr($editing->name ?? '') . '" class="regular-text"></td></tr>
      <tr><th><label for="email">Email</label></th>
          <td><input name="email" id="email" type="email" value="' . esc_attr($editing->email ?? '') . '" class="regular-text"></td></tr>
    </table>';

    submit_button($editing ? 'Update Subscriber' : 'Add Subscriber');
    echo '</form><hr>';

    echo '<h2>All Subscribers</h2>';
    echo '<table class="widefat striped"><thead><tr>
      <th>ID</th><th>Name</th><th>Email</th><th>Created</th><th>Actions</th></tr></thead><tbody>';
    foreach ($rows as $r) {
      $edit_url = admin_url('admin.php?page=tc-subscribers&edit=' . $r->id);
      $delete_url = wp_nonce_url(admin_url('admin-post.php?action=tc_delete_subscriber&id=' . $r->id), 'tc_subscriber_action');
      echo '<tr>
        <td>' . esc_html($r->id) . '</td>
        <td>' . esc_html($r->name) . '</td>
        <td>' . esc_html($r->email) . '</td>
        <td>' . esc_html($r->created_at) . '</td>
        <td>
          <a href="' . esc_url($edit_url) . '">Edit</a> |
          <a href="' . esc_url($delete_url) . '" onclick="return confirm(\'Are you sure?\');">Delete</a>
        </td>
      </tr>';
    }
    echo '</tbody></table></div>';
  }

  public function handle_add() {
    if (!current_user_can('manage_options') || !check_admin_referer('tc_subscriber_action', 'tc_nonce')) wp_die('Not allowed');
    global $wpdb;
    $wpdb->insert($wpdb->prefix . 'subscribers', [
      'name' => sanitize_text_field($_POST['name']),
      'email' => sanitize_email($_POST['email']),
    ]);
    wp_redirect(admin_url('admin.php?page=tc-subscribers&added=1'));
    exit;
  }

  public function handle_delete() {
    if (!current_user_can('manage_options') || !check_admin_referer('tc_subscriber_action')) wp_die('Not allowed');
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'subscribers', ['id' => intval($_GET['id'])]);
    wp_redirect(admin_url('admin.php?page=tc-subscribers&deleted=1'));
    exit;
  }

  public function handle_update() {
    if (!current_user_can('manage_options') || !check_admin_referer('tc_subscriber_action', 'tc_nonce')) wp_die('Not allowed');
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'subscribers', [
      'name' => sanitize_text_field($_POST['name']),
      'email' => sanitize_email($_POST['email']),
    ], ['id' => intval($_POST['id'])]);
    wp_redirect(admin_url('admin.php?page=tc-subscribers&updated=1'));
    exit;
  }
}
