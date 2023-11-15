<?php
namespace Richardson\PdfTranslator\Includes;
/**
 * Constants Class
 *
 * This class, authored by Richardson Oge, serves as a central repository for defining constants used throughout the translation toolkit. It encapsulates essential values for various aspects of the translation and processing operations, ensuring consistency and easy configuration.

 * @package Richardson\Translate
 * @author Richardson Oge
 * @version 1.0.0
 * @link https://github.com/richardsonoge/PdfTranslator
 *
 * Constants:
 *
 * - `IPV6_SEGMENTS`: Number of segments in an IPv6 address. IPv6 addresses consist of eight segments separated by colons.
 * - `PROXY_PORT`: Default port number for a proxy server. Port 8080 is commonly used for proxy communication.
 * - `NUMBER_CHARACTERS_GOOGLE`: Maximum number of characters allowed for processing by Google services.
 * - `DEFAULT_MAX_PAGES`: Default number of pages used in various operations or configurations.
 * - `SPLIT_OF_PAGES`: Threshold for splitting PDF files into smaller sections during certain operations.
 * - `SLEEP_BEFORE_START_TRANSLATION`: Sleep duration (in seconds) after each translation operation. Adjust this value to control the delay between translations.
 * - `EXPIRATION_TIME`: Expiration time for files in seconds. The value represents the duration after which files are considered old and eligible for deletion. It is set to 1 hour (3600 seconds) by default.
 * - `FOLDER_NAMES`: Array of folder names to delete files from.
 */
class Constants
{
    /**
     * Number of segments in an IPv6 address.
     * IPv6 addresses consist of eight segments separated by colons.
     */
    const IPV6_SEGMENTS = 8;

    /**
     * Default port number for a proxy server.
     * Port 8080 is commonly used for proxy communication.
     */
    const PROXY_PORT = 8080;

    /**
     * Maximum number of characters allowed for processing by Google services.
     */
    const NUMBER_CHARACTERS_GOOGLE = 3700;

    /**
     * Default number of pages used in various operations or configurations.
     */
    const DEFAULT_MAX_PAGES = 100;

    /**
     * Threshold for splitting PDF files into smaller sections during certain operations.
     */
    const SPLIT_OF_PAGES = 20;

    /**
     * Sleep duration (in seconds) after each translation operation.
     * Adjust this value to control the delay between translations.
     */
    const SLEEP_BEFORE_START_TRANSLATION = 1;

    /**
     * Expiration time for files in seconds.
     * The value represents the duration after which files are considered old
     * and eligible for deletion. It is set to 1 hour (3600 seconds) by default.
     *
     * @var int EXPIRATION_TIME
     */
    const EXPIRATION_TIME = 3600;

    /**
     * Array of folder names to delete files from.
     *
     * @var array FOLDER_NAMES
     */
    const FOLDER_NAMES = [
        'files/split',
        'files/txt/original',
        'files/txt/translate',
    ];

    /**
     * The folder path for storing split PDF files.
     *
     * This constant defines the folder path where the split PDF files will be stored.
     * It is used in the context of dividing a PDF document into smaller parts.
     *
     * @var string FOLDER_SPLIT The folder path for split PDF files.
     */
    const FOLDER_SPLIT = '/files/split/';

    /**
     * The folder path for storing translated files.
     *
     * This constant defines the folder path where the translated files will be stored.
     * It is used in the context of converting PDF files to HTML and translating HTML files.
     *
     * @var string FOLDER_TRANSLATE The folder path for translated files.
     */
    const FOLDER_TRANSLATE = 'files/translate';

    const ARRAY_FOLDER_TRANSLATE = ['files/translate'];

    const PATH_TEXT_FILES_ORIGINAL = 'files/txt/original/';
    const PATH_TEXT_FILES_TRANSLATE = 'files/txt/translate/';
    const COMMAND = 'original_';
    const COMMAND_TRANSLATE = 'translate_';

    /**************** Language accepted to translate your document PDF ********************/
        const LANG_ACCEPT_TRANSLATEFILE = [
            'af' => 'Afrikaans',
            'sq' => 'Albanian',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'hy' => 'Armenian',
            'as' => 'Assamese',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
            'bm' => 'Bambara',
            'eu' => 'Basque',
            'be' => 'Belarusian',
            'bn' => 'Bengali',
            'bho' => 'Bhojpuri',
            'bs' => 'Bosnian',
            'bg' => 'Bulgarian',
            'ca' => 'Catalan',
            'ceb' => 'Cebuano',
            'zh-CN' => 'Chinese (Simplified)',
            'zh-TW' => 'Chinese (Traditional)',
            'co' => 'Corsican',
            'hr' => 'Croatian',
            'cs' => 'Czech',
            'da' => 'Danish',
            'dv' => 'Dhivehi',
            'doi' => 'Dogri',
            'nl' => 'Dutch',
            'en' => 'English',
            'eo' => 'Esperanto',
            'et' => 'Estonian',
            'ee' => 'Ewe',
            'fil' => 'Filipino (Tagalog)',
            'fi' => 'Finnish',
            'fr' => 'French',
            'fy' => 'Frisian',
            'gl' => 'Galician',
            'ka' => 'Georgian',
            'de' => 'German',
            'el' => 'Greek',
            'gn' => 'Guarani',
            'gu' => 'Gujarati',
            'ht' => 'Haitian Creole',
            'ha' => 'Hausa',
            'haw' => 'Hawaiian',
            'he' => 'Hebrew',
            'iw' => 'Hebrew',
            'hi' => 'Hindi',
            'hmn' => 'Hmong',
            'hu' => 'Hungarian',
            'is' => 'Icelandic',
            'ig' => 'Igbo',
            'ilo' => 'Ilocano',
            'id' => 'Indonesian',
            'ga' => 'Irish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'jv' => 'Javanese',
            'kn' => 'Kannada',
            'kk' => 'Kazakh',
            'km' => 'Khmer',
            'rw' => 'Kinyarwanda',
            'gom' => 'Konkani',
            'ko' => 'Korean',
            'kri' => 'Krio',
            'ku' => 'Kurdish',
            'ckb' => 'Kurdish (Sorani)',
            'ky' => 'Kyrgyz',
            'lo' => 'Lao',
            'la' => 'Latin',
            'lv' => 'Latvian',
            'ln' => 'Lingala',
            'lt' => 'Lithuanian',
            'lg' => 'Luganda',
            'lb' => 'Luxembourgish',
            'mk' => 'Macedonian',
            'mai' => 'Maithili',
            'mg' => 'Malagasy',
            'ms' => 'Malay',
            'ml' => 'Malayalam',
            'mt' => 'Maltese',
            'mi' => 'Maori',
            'mr' => 'Marathi',
            'mni-Mtei' => 'Meiteilon (Manipuri)',
            'lus' => 'Mizo',
            'mn' => 'Mongolian',
            'my' => 'Myanmar (Burmese)',
            'ne' => 'Nepali',
            'no' => 'Norwegian',
            'ny' => 'Nyanja (Chichewa)',
            'or' => 'Odia (Oriya)',
            'om' => 'Oromo',
            'ps' => 'Pashto',
            'fa' => 'Persian',
            'pl' => 'Polish',
            'pt' => 'Portuguese (Portugal, Brazil)',
            'pa' => 'Punjabi',
            'qu' => 'Quechua',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sm' => 'Samoan',
            'sa' => 'Sanskrit',
            'gd' => 'Scots Gaelic',
            'nso' => 'Sepedi',
            'sr' => 'Serbian',
            'st' => 'Sesotho',
            'sn' => 'Shona',
            'sd' => 'Sindhi',
            'si' => 'Sinhala (Sinhalese)',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'so' => 'Somali',
            'es' => 'Spanish',
            'su' => 'Sundanese',
            'sw' => 'Swahili',
            'sv' => 'Swedish',
            'tl' => 'Tagalog (Filipino)',
            'tg' => 'Tajik',
            'ta' => 'Tamil',
            'tt' => 'Tatar',
            'te' => 'Telugu',
            'th' => 'Thai',
            'ti' => 'Tigrinya',
            'ts' => 'Tsonga',
            'tr' => 'Turkish',
            'tk' => 'Turkmen',
            'ak' => 'Twi (Akan)',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'ug' => 'Uyghur',
            'uz' => 'Uzbek',
            'vi' => 'Vietnamese',
            'cy' => 'Welsh',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'yo' => 'Yoruba',
            'zu' => 'Zulu'
        ];
    /**************** END Language accepted to translate your document PDF ********************/

}