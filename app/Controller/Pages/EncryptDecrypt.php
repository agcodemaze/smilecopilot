<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class EncryptDecrypt {

    // Base64 URL-safe
    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder) $data .= str_repeat('=', 4 - $remainder);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Cria um token seguro com AES-256-CBC + HMAC-SHA256.
     * @param string|int $id
     * @param string $key 32 bytes 
     * @return string token (base64url)
     */
    public static function encrypt_id_token($id, $key) {
        // chave deve ter 32 bytes para AES-256; derive se necessário
        $key = hash('sha256', $key, true);

        $plaintext = (string)$id;
        $ivlen = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($ivlen);

        $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($ciphertext === false) return false;

        // construímos token = iv || ciphertext
        $token_data = $iv . $ciphertext;

        // MAC (HMAC-SHA256) sobre token_data
        $hmac = hash_hmac('sha256', $token_data, $key, true);

        // retorno: iv||ciphertext||hmac (tudo codificado base64url)
        return self::base64url_encode($token_data . $hmac);
    }

    /**
     * Decripta o token e retorna id (string) ou false se inválido.
     * @param string $token base64url
     * @param string $key
     * @return string|false
     */
    public static function decrypt_id_token($token, $key) {
        $key = hash('sha256', $key, true);

        $data = self::base64url_decode($token);
        if ($data === false) return false;

        $ivlen = openssl_cipher_iv_length('AES-256-CBC');
        $hmac_len = 32; // sha256 raw = 32 bytes

        if (strlen($data) <= $ivlen + $hmac_len) return false; // inválido

        $iv = substr($data, 0, $ivlen);
        $hmac = substr($data, -$hmac_len);
        $ciphertext = substr($data, $ivlen, -$hmac_len);

        // validar HMAC (timing-safe)
        $calc_hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);
        if (!hash_equals($calc_hmac, $hmac)) return false;

        $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false) return false;

        return $plaintext; // id original (string)
    }

    public static function sanitize($str) {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    public static function checkCsrf($token) {
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function generateCsrf() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

}

