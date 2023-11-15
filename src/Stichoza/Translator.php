<?php
namespace Richardson\PdfTranslator\Stichoza;
use Richardson\PdfTranslator\Includes\Constants;

use Stichoza\GoogleTranslate\GoogleTranslate;
use GuzzleHttp\Client;

/**
 * Translator Class
 *
 * The Translator class, developed by Richardson Oge, facilitates language-related operations, including language detection and text translation. It integrates seamlessly with Google Translate services through the Stichoza\GoogleTranslate library, providing a robust translation toolkit.

 * @package Richardson\PdfTranslator
 * @author Richardson Oge
 * @version 1.0.0
 * @link https://github.com/richardsonoge/PdfTranslator
 *
 * Translator Functions:
 *
 * - `generateRandomIPv4Address()`: Generates a random IPv4 address.
 * - `generateRandomIPv6Address()`: Generates a random IPv6 address.
 * - `generateRandomUserAgent()`: Generates a random User Agent string for emulating different web browsers.
 * - `detectLanguage($text, $langTransTo = 'en')`: Detects the language of the given text.
 * - `translate($text, $langTransFrom, $langTransTo)`: Translates the given text from the source language to the target language.

 * Dependencies:
 *
 * - Stichoza\GoogleTranslate: Utilized for interacting with Google Translate services.
 * - GuzzleHttp\Client: Employed for making HTTP requests, including proxy configuration and User-Agent emulation.
 *
 * Note: Ensure accurate language detection and translation by configuring proxy settings and User-Agent headers appropriately.
 */

class Translator
{

    /**
     * Generates a random IPv4 address.
     *
     * @return string Random IPv4 address.
     */
    public static function generateRandomIPv4Address()
    {
        return long2ip(mt_rand());
    }

    /**
     * Generates a random IPv6 address.
     *
     * @return string Random IPv6 address.
     */
    public static function generateRandomIPv6Address()
    {
        $ipv6Segments = [];
        for ($i = 0; $i < Constants::IPV6_SEGMENTS; $i++) {
            $ipv6Segments[] = bin2hex(random_bytes(2));
        }
        return implode(':', $ipv6Segments);
    }

    /**
     * Generates a random User Agent string for emulating different web browsers.
     *
     * @return string Random User Agent string.
     */
    public static function generateRandomUserAgent()
    {
        $browsers = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
            // Add more User Agents as needed
        ];
        return $browsers[array_rand($browsers)];
    }

    /**
     * Detects the language of the given text.
     *
     * @param string $text Text to detect language from.
     * @param string $langTransTo Target language for translation.
     * @return string Detected language code.
     */
    public static function detectLanguage($text, $langTransTo = 'en')
    {
        $translator = new GoogleTranslate($langTransTo);

        $translator->httpClient = new Client([
            'proxy' => 'http://' . self::generateRandomIPv4Address() . ':' . Constants::PROXY_PORT,
            'headers' => [
                'User-Agent' => self::generateRandomUserAgent(),
            ],
            'timeout' => 60,
        ]);

        $translator->translate($text);

        return $translator->getLastDetectedSource();
    }

    /**
     * Translates the given text from the source language to the target language.
     *
     * @param string $text Text to translate.
     * @param string $langTransFrom Source language code.
     * @param string $langTransTo Target language code.
     * @return string Translated text.
     */
    public static function translate($text, $langTransFrom, $langTransTo)
    {
        $translator = new GoogleTranslate();

        if (!empty($langTransFrom)) {
            $translator->setSource($langTransFrom);
        } else {
            $translator->setSource();  // Set the source language
        }

        $translator->setTarget($langTransTo);

        $translator->httpClient = new Client([
            'proxy' => 'http://' . self::generateRandomIPv4Address() . ':' . Constants::PROXY_PORT,
            'headers' => [
                'User-Agent' => self::generateRandomUserAgent(),
            ],
            'timeout' => 60,
        ]);

        $translation = $translator->translate($text);

        return $translation ?: $text;
    }
}