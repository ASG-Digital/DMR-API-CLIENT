<?php

if (!function_exists('urlsafeB64Decode')) {
    function urlsafeB64Decode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }
}

if (!function_exists('urlsafeB64Encode')) {
    function urlsafeB64Encode($input) {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }
}

if (!function_exists('array_key_first')) {
    function array_key_first($array) {
        return array_keys($array)[0];
    }
}