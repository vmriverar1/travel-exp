<?php

namespace ACF\FECP\Admin;

if (!defined('ABSPATH')) exit;

/**
 * Clase encargada de manejar el envío de correos desde el panel (botón "Send Email")
 */
class LeadEmailAction
{

    public function register()
    {
        add_action('admin_post_fecp_send_email', [$this, 'handle']);
    }

    public function handle()
    {
        if (!current_user_can('manage_options')) wp_die('No permitido');
        check_admin_referer('fecp_send_email_nonce');

        global $wpdb;
        $id   = (int)($_GET['id'] ?? 0);
        $type = (int)($_GET['type_lead'] ?? 0);
        $table = $wpdb->prefix . ($type === 1 ? 'acf_fecp_leads_b2b' : 'acf_fecp_leads_b2c');
        $lead  = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id=%d", $id), ARRAY_A);

        if (!$lead) {
            wp_redirect(add_query_arg(['msg' => 'not_found'], wp_get_referer()));
            exit;
        }

        // 🧭 Remitente: correo del lead
        $from_email = !empty($lead['email']) ? $lead['email'] : 'no-reply@tudominio.com';
        $from_name  = trim(($lead['first_name'] ?? '') . ' ' . ($lead['last_name'] ?? '')) ?: 'Formulario Web';

        // Destinatarios fijos
        $to = [
            //   'ventas@tudominio.com',
            //   'marketing@tudominio.com',
            //   'info@tudominio.com',
            //   'gerencia@tudominio.com',
            'palominocomv@gmail.com',
        ];

        // Cuerpo del mensaje (solo datos del cliente)
        $subject = 'Nuevo Lead #' . $id . ' (' . ($type === 1 ? 'B2B' : 'B2C') . ')';
        $body  = "<h2 style='font-family:sans-serif;color:#17565C;'>Nuevo lead recibido</h2>";
        $body .= "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;width:100%;font-family:sans-serif;'>";

        $exclude = ['id', 'endpoint', 'api_code', 'api_response', 'sent_to_api', 'created_at', 'updated_at', 'base_uri'];
        foreach ($lead as $key => $val) {
            if (in_array($key, $exclude, true)) continue;
            $body .= "<tr><td style='font-weight:bold;background:#f2f2f2;width:25%;'>" . esc_html(ucfirst($key)) . "</td><td>" . esc_html($val) . "</td></tr>";
        }
        $body .= "</table>";

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . sanitize_text_field($from_name) . ' <' . sanitize_email($from_email) . '>'
        ];

        error_log("[FECP][EMAIL] Intentando enviar correo del lead ID {$id}");
        error_log("[FECP][EMAIL] From: {$from_email} ({$from_name})");
        error_log("[FECP][EMAIL] To: " . implode(', ', $to));
        // Envío
        $sent = wp_mail($to, $subject, $body, $headers);

        error_log("[FECP][EMAIL] Resultado: " . ($sent ? 'OK ✅' : 'FAIL ❌'));

        $msg = $sent ? 'email_sent' : 'email_fail';
        wp_redirect(add_query_arg(['msg' => $msg], wp_get_referer()));
        exit;
    }
}
