<?php

namespace ACF\FECP\Admin;

use ACF\FECP\DB\LeadsRepository;

if (!defined('ABSPATH')) exit;

class LeadsPage
{

  public function render()
  {
    if (!current_user_can('manage_options')) wp_die('No permitido');

    $type   = isset($_GET['type_lead']) ? (int) $_GET['type_lead'] : 0; // 0 = B2C, 1 = B2B
    $paged  = max(1, (int)($_GET['paged'] ?? 1));
    $per    = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 20;
    $action = sanitize_text_field($_GET['action'] ?? '');

    $repo = new LeadsRepository($type);

    /** ==========================
     *  ACCIONES (update/delete)
     * ========================== */
    if (!empty($_POST['fecp_action'])) {
      check_admin_referer('fecp_action_nonce');
      $id = (int)($_POST['id'] ?? 0);

      if ($_POST['fecp_action'] === 'delete') {
        $repo->delete($id);
        echo '<div class="updated"><p>🗑 Lead eliminado correctamente.</p></div>';
      }

      if ($_POST['fecp_action'] === 'update' && !empty($_POST['fields'])) {
        $clean = array_map('sanitize_text_field', (array) $_POST['fields']);
        $repo->update($id, $clean);
        echo '<div class="updated"><p>💾 Cambios guardados correctamente.</p></div>';
      }
    }


    /** ==========================
     *  EDITAR DETALLE
     * ========================== */
    if ($action === 'edit' && !empty($_GET['id'])) {
      $this->render_edit_screen($repo, (int)$_GET['id'], $type);
      return;
    }

    /** ==========================
     *  LISTA PRINCIPAL
     * ========================== */
    $res = $repo->list($paged, $per);
    $this->render_list($res, $type, $per);
  }

  /** ==========================
   *  Selector B2C / B2B
   * ========================== */
  private function render_form_selector(int $type): void
  {
    $options = [
      0 => 'Leads B2C (Clientes)',
      1 => 'Leads B2B (Agencias)',
    ];
    echo '<form method="get" style="margin:12px 0; display:flex; gap:8px; align-items:center;">';
    echo '<input type="hidden" name="page" value="fecp-leads"/>';
    echo '<select name="type_lead">';
    foreach ($options as $key => $label) {
      $selected = $key === $type ? 'selected' : '';
      echo "<option value='{$key}' {$selected}>{$label}</option>";
    }
    echo '</select>';
    echo '<button class="button">Ver</button>';
    echo '</form>';
  }

  /** ==========================
   *  Edición completa
   * ========================== */
  private function render_edit_screen(LeadsRepository $repo, int $id, int $type): void
  {
    global $wpdb;
    $table = $wpdb->prefix . ($type === 1 ? 'acf_fecp_leads_b2b' : 'acf_fecp_leads_b2c');
    $lead = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id=%d", $id), ARRAY_A);
    if (!$lead) {
      echo '<div class="wrap"><h1>Lead no encontrado</h1>';
      echo '<a href="' . admin_url('admin.php?page=fecp-leads&type_lead=' . $type) . '" class="button-secondary">← Volver</a>';
      echo '</div>';
      return;
    }

    echo '<div class="wrap">';
    echo '<h1>Editar Lead #' . (int)$lead['id'] . ' - ' . ($type === 1 ? 'B2B' : 'B2C') . '</h1>';
    echo '<div style="display:flex;align-items:flex-start;gap:20px;">';
    echo '<div style="flex:2;">';
    echo '<a href="' . admin_url('admin.php?page=fecp-leads&type_lead=' . $type) . '" class="button-secondary">← Volver</a>';

    echo '<form method="post" style="margin-top:20px;">';
    wp_nonce_field('fecp_action_nonce');
    echo '<input type="hidden" name="id" value="' . (int)$lead['id'] . '">';

    // Campos editables
    $readonly = ['id', 'endpoint', 'api_code', 'api_response', 'sent_to_api', 'created_at', 'updated_at'];
    foreach ($lead as $col => $val) {
      echo '<p>';
      echo '<label style="display:block;margin-bottom:6px;font-weight:600;">' . esc_html(ucfirst($col)) . '</label>';
      if (in_array($col, $readonly, true)) {
        echo '<input type="text" readonly value="' . esc_attr($val) . '" style="width:100%;background:#f5f5f5;">';
      } elseif (strlen($val) > 100) {
        echo '<textarea name="fields[' . esc_attr($col) . ']" rows="3" style="width:100%;">' . esc_textarea($val) . '</textarea>';
      } else {
        echo '<input type="text" name="fields[' . esc_attr($col) . ']" value="' . esc_attr($val) . '" style="width:100%;">';
      }
      echo '</p>';
    }

    // Botones de acción
    echo '<div style="display:flex;gap:10px;margin-top:20px;">';

    // Guardar cambios
    echo '<button type="submit" name="fecp_action" value="update" class="button button-primary" style="background:#28a745;border-color:#28a745;">💾 Save</button>';

    // Eliminar lead
    echo '<button type="submit" name="fecp_action" value="delete" class="button button-secondary" style="background:#dc3545;border-color:#dc3545;color:#fff;" onclick="return confirm(\'¿Eliminar este lead?\');">🗑 Delete</button>';

    echo '</div>';
    echo '</form>';


    // 🔹 Panel lateral derecho con botón de envío de correo
    echo '<div style="flex:1;background:#f6f7f7;padding:16px;border:1px solid #ddd;border-radius:6px;">';
    echo '<h2>Acciones</h2>';

    $send_url = wp_nonce_url(
      admin_url('admin-post.php?action=fecp_send_email&id=' . (int)$lead['id'] . '&type_lead=' . $type),
      'fecp_send_email_nonce'
    );

    echo '<p><a href="' . esc_url($send_url) . '" class="button button-primary" style="width:100%;text-align:center;">📧 Enviar Email</a></p>';
    echo '<p><em>Envía este lead por correo a los responsables configurados.</em></p>';
    echo '</div>'; // end side panel

    echo '</div>'; // end flex container
    echo '</div>'; // end wrap
  }

  /** ==========================
   *  Lista con columnas principales
   * ========================== */
  private function render_list($res, int $type, int $per): void
  {
    $rows  = $res['rows'];
    $total = $res['total'];
    $paged = $res['paged'];
    $pages = max(1, ceil($total / $per));

    echo '<div class="wrap">';
    echo '<h1>Leads ' . ($type === 1 ? 'B2B' : 'B2C') . '</h1>';
    $this->render_form_selector($type);

    echo '<div style="overflow:auto; max-width:100%;margin-top:10px;">';
    echo '<table class="wp-list-table widefat fixed striped table-view-list" style="min-width:1100px">';
    echo '<thead><tr>';
    echo '<th>ID</th><th>Fecha</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Paquete</th><th>Estado</th><th>Código</th><th>Acciones</th>';
    echo '</tr></thead><tbody>';

    foreach ($rows as $r) {
      echo '<tr>';
      echo '<td>' . (int)$r['id'] . '</td>';
      echo '<td>' . esc_html($r['created_at']) . '</td>';
      echo '<td>' . esc_html(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) . '</td>';
      echo '<td>' . esc_html($r['email'] ?? '') . '</td>';
      echo '<td>' . esc_html($r['phone'] ?? '') . '</td>';
      echo '<td>' . esc_html($r['package'] ?? '') . '</td>';

      // Estado
      if ((int)($r['sent_to_api'] ?? 0) === 1) {
        echo '<td style="color:green;">✅ Enviado</td>';
      } elseif (!empty($r['api_code'])) {
        echo '<td style="color:#d63638;">❌ Error</td>';
      } else {
        echo '<td style="color:#777;">✅ Realizado</td>';
      }

      echo '<td>' . esc_html($r['api_code'] ?? '') . '</td>';

      $edit_url = admin_url('admin.php?page=fecp-leads&type_lead=' . $type . '&action=edit&id=' . (int)$r['id']);
      echo '<td>';
      echo '<a href="' . esc_url($edit_url) . '" class="button button-small">✏️ Editar</a>';
      echo '<form method="post" onsubmit="return confirm(\'¿Eliminar este lead?\');" style="display:inline-block;margin-left:6px">';
      wp_nonce_field('fecp_action_nonce');
      echo '<input type="hidden" name="fecp_action" value="delete">';
      echo '<input type="hidden" name="id" value="' . (int)$r['id'] . '">';
      echo '<button class="button button-small button-link-delete">🗑 Eliminar</button>';
      echo '</form>';
      echo '</td>';

      echo '</tr>';
    }

    echo '</tbody></table></div>';

    // paginación
    echo '<div class="tablenav"><div class="tablenav-pages">';
    if ($pages > 1) {
      for ($i = 1; $i <= $pages; $i++) {
        $cls = $i == $paged ? 'class="page-numbers current"' : 'class="page-numbers"';
        echo '<a ' . $cls . ' href="' . esc_url(add_query_arg(['paged' => $i])) . '">' . $i . '</a> ';
      }
    }
    echo '</div></div></div>';
  }
}
