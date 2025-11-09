<?php

namespace ACF\FECP\Services;

class SpamAndRatingService
{
    private string $recaptchaSecret;

    public function __construct()
    {
        // Carga la clave secreta desde wp-config.php
        $this->recaptchaSecret = defined('RECAPTCHA_SECRET_KEY')
            ? RECAPTCHA_SECRET_KEY
            : '';
    }

    /**
     * Analiza el envío y devuelve score numérico + rating textual
     */
    public function analyze(array $fields): array
    {
        $token = $fields['recaptcha_token'] ?? null;
        $score = $this->verify_recaptcha($token);

        // === Guardamos el puntaje real del reCAPTCHA ===
        $score_spam = $score !== null ? round(floatval($score), 2) : 0.0;

        // === Calcular rating basado en el score ===
        if ($score_spam >= 0.8) {
            $rating = 'Hot';
        } elseif ($score_spam >= 0.5) {
            $rating = 'Warm';
        } else {
            $rating = 'Cold';
        }

        return [
            'score_spam' => $score_spam, // Puntaje real (0.00 - 1.00)
            'rating'     => $rating,     // Clasificación simple
        ];
    }

    /**
     * Verifica token de reCAPTCHA v3 con Google
     */
    private function verify_recaptcha(?string $token): ?float
    {
        if (empty($token)) return null;

        $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret'   => $this->recaptchaSecret,
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ],
            'timeout' => 10,
        ]);

        if (is_wp_error($response)) return null;

        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($data['success'])) return null;

        return isset($data['score']) ? floatval($data['score']) : null;
    }
}
