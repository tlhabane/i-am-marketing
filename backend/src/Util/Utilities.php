<?php

namespace App\Util;

use App\Exception\ValidationException;
use Exception;
use DateTime;

class Utilities
{
    /**
     * String sanitizer
     *
     * @param string|null $inputString
     * @return string
     */
    public static function sanitizeString(?string $inputString): string
    {
        if (is_null($inputString)) {
            return '';
        }

        $search = array(
            '@<script[^>]*?>.*?</script>@si',   /* strip out javascript */
            '@<[\/\!]*?[^<>]*?>@si',            /* strip out HTML tags */
            '@<style[^>]*?>.*?</style>@siU',    /* strip style tags properly */
            '@<![\s\S]*?--[ \t\n\r]*>@'         /* strip multi-line comments */
        );

        return preg_replace($search, '', $inputString);
    }

    /**
     * Email address sanitizer
     *
     * @param string|null $emailAddress
     * @return string
     */
    public static function sanitizeEmail(?string $emailAddress): string
    {
        return is_null($emailAddress) ? '' : filter_var($emailAddress, FILTER_SANITIZE_EMAIL);
    }

    /**
     *  Encode string
     *
     * @param string|null $inputString
     * @return string - ut8 encoded
     */
    public static function encodeUTF8(?string $inputString) : string
    {
        if (is_null($inputString)) {
            return '';
        }
        return (htmlentities($inputString, ENT_QUOTES, 'UTF-8'));
    }

    /**
     *  Decode string
     *
     * @param string|null $inputString - utf-8 encoded
     * @return string - utf-8 decoded
     */
    public static function decodeUTF8(?string $inputString) : string
    {
        if (is_null($inputString)) {
            return  '';
        }
        return (html_entity_decode($inputString, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Sanitize string and UTF-8 encoding
     *
     * @param string|null $inputString
     * @return string
     */
    public static function sanitizeAndEncodeString(?string $inputString): string
    {
        return self::encodeUTF8(self::sanitizeString($inputString));
    }

    /**
     * Remove non-digit characters
     *
     * @param string $raw
     * @return string
     */
    public static function removeNonDigits(string $raw): string
    {
        return (string)preg_replace('/\D+/', '', $raw);
    }

    /**
     * Validate phone number (E.164 format)
     *
     * @param string $phone
     * @return bool
     */
    public static function isValidPhone(string $phone): bool
    {
        // E.164 format: starts with +, first digit 1-9, total length 8-15 digits (inclusive)
        // Examples: +254712345678, +15551234567
        $e164 = '/^\+[1-9]\d{7,14}$/';
        return (bool)preg_match($e164, $phone);
    }

    /**
     * @param string $string
     * @param string $prefix
     * @return string
     */
    public static function ltrim_exact_match(string $string, string $prefix): string {
        if (str_starts_with($string, $prefix)) {
            return substr($string, strlen($prefix));
        }
        return $string;
    }

    /**
     * @param string $phone
     * @param string $dialPrefix
     * @return string
     */
    public static function sanitizePhoneNumber(string $phone, string $dialPrefix = "+27"): string
    {
        $pattern = '/[^0-9+]/';
        $replacement = '';
        $phone_number = preg_replace($pattern, $replacement, $phone);

        $dial_prefix = self::removeNonDigits($dialPrefix);
        $numbers = ltrim(self::ltrim_exact_match($phone_number, $dial_prefix), '0');
        $alternative_number = "{$dialPrefix}{$numbers}";

        $formatted_numbers = [
            "+{$phone_number}",
            "{$dialPrefix}{$numbers}"
        ];

        foreach ($formatted_numbers as $number) {
            if (self::isValidPhone($number)) {
                return (string)$number;
            }
        }

        return $phone_number;
    }

    /* Random generators
    ---------------------------------------------------*/

    /**
     * Token string generator
     *
     * @param int $length
     * @return string $token
     */
    public static function generateToken(int $length = 32): string
    {
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        do {
            $token .= (strlen($token) === 0) ?
                self::randomCharString(3) : $codeAlphabet[self::cryptoRandSecure(0, strlen($codeAlphabet))];
        } while (strlen($token) < $length);

        return $token;
    }

    /**
     * Token string generator helper function
     *
     * @param int $min
     * @param int $max
     * @return int $token
     * @throws Exception
     */
    private static function cryptoRandSecure(int $min, int $max): int
    {
        $range = $max - $min;

        if ($range < 0) {
            return $min;
        }

        // not so random...
        $log    = log($range, 2);
        $bytes  = (int)($log / 8) + 1; // length in bytes
        $bits   = (int)$log + 1; // length in bits
        $filter = (int)(1 << $bits) - 1; // set all lower bits to 1

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);

        return $min + $rnd;
    }

    /**
     * Random Number Generator
     *
     * @param int $length,
     * @param int $min,
     * @param int $max,
     * @return int|string
     */
    public static function randomNumber(int $length = 8, int $min = 0, int $max = 9): int|string
    {
        $num = '';
        for ($i = 0; $i < $length; $i++) {
            $num .= mt_rand($min, $max);
        }
        return $num;
    }

    /**
     * Random String Generator
     *
     * @param int $length
     * @return string
     */
    public static function randomCharString(int $length = 10): string
    {
        $characters         = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength   = strlen($characters);
        $randomString       = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Removes extra spaces between strings & capitalizes 1st characters of a word
     * @example normalizeString('John  doe') => 'John Doe'
     *
     * @param string $text
     * @return string
     */
    public static function normalizeString(string $text): string
    {
        $updatedString = implode(' ', array_filter(explode(' ', $text)));
        return ucwords(strtolower(trim($updatedString)));
    }

    /**
     * Format date and time
     * 1. Convert date or time string to DateTime object
     * 2. Format date or time using a DateTime object
     * @example formatDateAndTime('2021-01-01 12:00:00', 'Y-m-d H:i:s') => '2021-01-01 12:00:00'
     * @param string $dateOrTime
     * @param string $format
     * @return string
     */
    public static function formatDateAndTime(string $dateOrTime, string $format = 'Y-m-d'): string
    {
        try {
            $dateTimeObject = new DateTime($dateOrTime);
            return $dateTimeObject->format($format);
        } catch (Exception $exception) {
            throw new ValidationException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     *  Formats a file size number
     *
     * @example formatFileSize(1024) => '1.00 Kb'
     * @param float $filesize
     * @return string
     */
    public static function formatFileSize(float $filesize): string
    {
        $sizes = array('bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb');
        if ($filesize == 0) return('n/a');

        return (round($filesize/pow(1024, ($i = floor(log($filesize, 1024)))), 2) . ' ' . $sizes[$i]);
    }
}