<?php
namespace Travel\Components\Blocks\SubscribeForm;

use WP_REST_Request;
use WP_REST_Response;

if (!defined('ABSPATH')) exit;

class RestSubscribe {

  public function register() {
    register_rest_route('acf-blocks/v1', '/subscribe', [
      'methods'  => 'POST',
      'permission_callback' => '__return_true',
      'callback' => [$this, 'handle_subscribe']
    ]);
  }

  public function handle_subscribe(WP_REST_Request $req) {
    global $wpdb;

    $name  = sanitize_text_field($req->get_param('name'));
    $email = sanitize_email($req->get_param('email'));
    $recipients_csv = sanitize_text_field($req->get_param('recipients'));
    $recipients = array_filter(array_map('sanitize_email', array_map('trim', explode(',', (string)$recipients_csv))));

    if (!$email || !is_email($email)) {
      return new WP_REST_Response(['success' => false, 'message' => 'Invalid email'], 400);
    }

    // 🟩 Guardar en DB
    $wpdb->insert($wpdb->prefix . 'subscribers', [
      'name'  => $name,
      'email' => $email
    ]);

    try {
      // 📦 Cargar PHPMailer nativo de WordPress si no está cargado
      if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
        require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
        require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
      }

      // 🔹 Crear nueva instancia
      $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);

      // Configuración SMTP directa (sin hooks)
      $mailer->isSMTP();
      $mailer->Host       = 'smtp-mail.outlook.com';
      $mailer->Port       = 587;
      $mailer->SMTPAuth   = true;
      $mailer->SMTPSecure = 'tls';
      $mailer->Username   = 'info@valenciatravelcusco.com';
      $mailer->Password   = 'LXuyhXqKCv6L';
      $mailer->setFrom('info@valenciatravelcusco.com', 'Sistema de Leads');
      $mailer->CharSet    = 'UTF-8';
      $mailer->Encoding   = 'base64';
      $mailer->isHTML(false);

      // Destinatarios (si no hay, va a tu correo de prueba)
      $to = !empty($recipients) ? $recipients : ['palominocomv@gmail.com'];
      foreach ($to as $addr) {
        $mailer->addAddress($addr);
      }

      // Asunto y contenido
      $mailer->Subject = 'Nueva suscripción recibida';
      $mailer->Body  = "Se ha recibido una nueva suscripción.\n\n";
      $mailer->Body .= "Nombre: {$name}\n";
      $mailer->Body .= "Correo: {$email}\n";
      $mailer->Body .= "\nMensaje enviado automáticamente desde el formulario.";

      // 📨 Enviar correo
      $mailer->send();

      return new WP_REST_Response([
        'success' => true,
        'message' => 'Subscription successful and email sent.'
      ], 200);

    } catch (\Throwable $e) {
      error_log('❌ [Mail Exception] ' . $e->getMessage());
      return new WP_REST_Response([
        'success' => false,
        'message' => 'Error sending email: ' . $e->getMessage()
      ], 200);
    }
  }
}
