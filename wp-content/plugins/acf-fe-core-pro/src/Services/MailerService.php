<?php
namespace ACF\FECP\Services;

if (!defined('ABSPATH')) exit;

/**
 * MailerService
 *
 * Configura SMTP global para todos los correos enviados con wp_mail().
 * No envía correos directamente, solo establece la conexión.
 */
class MailerService {

  /**
   * Registra el hook global phpmailer_init
   */
  public function register() {
    add_action('phpmailer_init', [$this, 'setup_smtp']);
  }

  /**
   * Configura el transporte SMTP global
   *
   * @param \PHPMailer $phpmailer
   */
  public function setup_smtp($phpmailer) {
    // Forzamos a usar SMTP
    $phpmailer->isSMTP();

    // Configuración de tu servidor SMTP
    $phpmailer->Host       = 'smtp-mail.outlook.com'; // Cambia según tu hosting
    $phpmailer->Port       = 587;                  // 465 si usas SSL
    $phpmailer->SMTPAuth   = true;
    $phpmailer->SMTPSecure = 'tls';                // o 'ssl'
    $phpmailer->Username   = 'info@valenciatravelcusco.com'; // tu cuenta SMTP
    $phpmailer->Password   = 'LXuyhXqKCv6L';          // contraseña de esa cuenta

    // Remitente predeterminado (en caso de que el wp_mail no defina un From)
    $phpmailer->From       = 'info@valenciatravelcusco.com';
    $phpmailer->FromName   = 'Sistema de Leads';

    // Opcional: codificación y charset
    $phpmailer->CharSet    = 'UTF-8';
    $phpmailer->Encoding   = 'base64';

    // (Opcional) Debug local
    if (defined('WP_DEBUG') && WP_DEBUG) {
      $phpmailer->SMTPDebug = 0; // Cambia a 2 para ver el log SMTP en pantalla
    }

    error_log("[FECP][SMTP] Configurado host {$phpmailer->Host}:{$phpmailer->Port} / Auth=" . ($phpmailer->SMTPAuth ? 'true' : 'false'));

  }
}
