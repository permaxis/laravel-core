<?php
/**
 * Created by Permaxis.
 * User: mk2
 * Date: 18/12/2018
 * Time: 15:24
 */

namespace Permaxis\LaravelCore\App\Services\Utils;



class UtilsString
{
    /**
     * @var \Permaxis\LaravelCore\App\Services\Utils
     * @access private
     * @static
     */
    private static $_instance = null;

    /**
     * @var string
     */
    private $env;

    /**
     * class constructor
     *
     * @param void
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Method to create instance of class
     * created if does not exists.
     *
     * @param void
     * @return \Permaxis\LaravelCore\App\Services\Utils
     */
    public static function getInstance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new UtilsString();
        }

        return self::$_instance;
    }

    /**
     * Converts all accent characters to ASCII characters.
     *
     * If there are no accent characters, then the string given is just returned.
     *
     * @param string $string Text that might have accent characters
     * @return string Filtered string with replaced "nice" characters.
     */

    public function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;
        if ($this->isUtf8($string)) {
            $chars = array(
                // Decompositions for Latin-1 Supplement
                chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
                chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
                chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
                chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
                chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
                chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
                chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
                chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
                chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
                chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
                chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
                chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
                chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
                chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
                chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
                chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
                chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
                chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
                chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
                chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
                chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
                chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
                chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
                chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
                chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
                chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
                chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
                chr(195) . chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
                chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
                chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
                chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
                chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
                chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
                chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
                chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
                chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
                chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
                chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
                chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
                chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
                chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
                chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
                chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
                chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
                chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
                chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
                chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
                chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
                chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
                chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
                chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
                chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
                chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
                chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
                chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
                chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
                chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
                chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
                chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
                chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
                chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
                chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
                chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
                chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
                chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
                chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
                chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
                chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
                chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
                chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
                chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
                chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
                chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
                chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
                chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
                chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
                chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
                chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
                chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
                chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
                chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
                chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
                chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
                chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
                chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
                chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
                chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
                chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
                chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
                chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
                chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
                // Euro Sign
                chr(226) . chr(130) . chr(172) => 'E',
                // GBP (Pound) Sign
                chr(194) . chr(163) => '');
            $string = strtr($string, $chars);
        } else {
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
                . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
                . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
                . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
                . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
                . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
                . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
                . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
                . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
                . chr(252) . chr(253) . chr(255);
            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }
        return $string;
    }

    /**
     * Checks to see if a string is utf8 encoded.
     *
     * @author bmorel at ssi dot fr
     *
     * @param string $Str The string to be checked
     * @return bool True if $Str fits a UTF-8 model, false otherwise.
     */
    public function isUtf8($Str)
    { # by bmorel at ssi dot fr
        $length = strlen($Str);
        for ($i = 0; $i < $length; $i++) {
            if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
            elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n = 1; # 110bbbbb
            elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n = 2; # 1110bbbb
            elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n = 3; # 11110bbb
            elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n = 4; # 111110bb
            elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n = 5; # 1111110b
            else return false; # Does not match any model
            for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
                if ((++$i == $length) || ((ord($Str[$i]) & 0xC0) != 0x80))
                    return false;
            }
        }
        return true;
    }

    public function utf8UriEncode($utf8_string, $length = 0)
    {
        $unicode = '';
        $values = array();
        $num_octets = 1;
        $unicode_length = 0;
        $string_length = strlen($utf8_string);
        for ($i = 0; $i < $string_length; $i++) {
            $value = ord($utf8_string[$i]);
            if ($value < 128) {
                if ($length && ($unicode_length >= $length))
                    break;
                $unicode .= chr($value);
                $unicode_length++;
            } else {
                if (count($values) == 0) $num_octets = ($value < 224) ? 2 : 3;
                $values[] = $value;
                if ($length && ($unicode_length + ($num_octets * 3)) > $length)
                    break;
                if (count($values) == $num_octets) {
                    if ($num_octets == 3) {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
                        $unicode_length += 9;
                    } else {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
                        $unicode_length += 6;
                    }
                    $values = array();
                    $num_octets = 1;
                }
            }
        }
        return $unicode;
    }

    /**
     * Sanitizes title, replacing whitespace with dashes.
     *
     * Limits the output to alphanumeric characters, underscore (_) and dash (-).
     * Whitespace becomes a dash.
     *
     * @param string $title The title to be sanitized.
     * @return string The sanitized title.
     */
    public function slugify($title)
    {
        $title = strip_tags($title);
        // Preserve escaped octets.
        $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
        // Remove percent signs that are not part of an octet.
        $title = str_replace('%', '', $title);
        // Restore octets.
        $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
        $title = $this->removeAccents($title);
        if ($this->isUtf8($title)) {
            if (function_exists('mb_strtolower')) {
                $title = mb_strtolower($title, 'UTF-8');
            }
            $title = $this->utf8UriEncode($title, 200);
        }
        $title = strtolower($title);
        $title = preg_replace('/&.+?;/', '-', $title); // kill entities
        $title = preg_replace('/[^%a-z0-9_-]/', '', $title);
        $title = preg_replace('/\s+/', '-', $title);
        $title = preg_replace('|-+|', '-', $title);
        $title = trim($title, '-');
        return $title;
    }

    public function formatInput($input = array(), $options = array())
    {
        array_walk_recursive($input, function (&$v, &$k) use ($options, &$input) {
            if (!is_array($v)) {
                $v = trim($v);
                if (isset($options['removeEmpty']) && $options['removeEmpty']) {
                    if ($v == '') {
                        unset($input[$k]);
                    }
                }
            }

        });

        if (is_array($input) && empty($input) && isset($options['nullIfEmpty']) && $options['nullIfEmpty']) {
            return null;
        }

        return $input;
    }


    public static function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }
    //JSON Validator function
    function json_validator($data = NULL)
    {
        try {
            if (!empty($data)) {
                @json_decode($data);
                return (json_last_error() === JSON_ERROR_NONE);
            }
        } catch (\Exception $e) {
            return false;
        }

    }
    public function getEnv()
    {
        // default env
        if (!empty($this->env))
        {
            return $this->env ;
        }

        $env = 'prod';

        if (defined('APPLICATION_ENV'))
        {
            $env = constant('APPLICATION_ENV');
        }
        elseif (getenv('APPLICATION_ENV'))
        {
            $env = getenv('APPLICATION_ENV');
            if (!defined('APPLICATION_ENV'))
            {
                define('APPLICATION_ENV',$env);
            }
        }

        if (!defined('APPLICATION_ENV'))
        {
            define('APPLICATION_ENV',$env);
        }

        return $env;
    }

    public function setEnv($env)
    {
        $this->env = $env;
    }


    /**
     * Indents a flat JSON string to make it more human-readable.
     *
     * @param string $json The original JSON string to process.
     *
     * @return string Indented version of the original JSON string.
     */
    function prettyPrintJson($json) {

        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

    public function Utf8ToAnsi($str) {

        $utf8_ansi2 = array(
            "\u00c0" =>"ï¿½",
            "\u00c1" =>"ï¿½",
            "\u00c2" =>"ï¿½",
            "\u00c3" =>"ï¿½",
            "\u00c4" =>"ï¿½",
            "\u00c5" =>"ï¿½",
            "\u00c6" =>"ï¿½",
            "\u00c7" =>"ï¿½",
            "\u00c8" =>"ï¿½",
            "\u00c9" =>"ï¿½",
            "\u00ca" =>"ï¿½",
            "\u00cb" =>"ï¿½",
            "\u00cc" =>"ï¿½",
            "\u00cd" =>"ï¿½",
            "\u00ce" =>"ï¿½",
            "\u00cf" =>"ï¿½",
            "\u00d1" =>"ï¿½",
            "\u00d2" =>"ï¿½",
            "\u00d3" =>"ï¿½",
            "\u00d4" =>"ï¿½",
            "\u00d5" =>"ï¿½",
            "\u00d6" =>"ï¿½",
            "\u00d8" =>"ï¿½",
            "\u00d9" =>"ï¿½",
            "\u00da" =>"ï¿½",
            "\u00db" =>"ï¿½",
            "\u00dc" =>"ï¿½",
            "\u00dd" =>"ï¿½",
            "\u00df" =>"ï¿½",
            "\u00e0" =>"ï¿½",
            "\u00e1" =>"ï¿½",
            "\u00e2" =>"ï¿½",
            "\u00e3" =>"ï¿½",
            "\u00e4" =>"ï¿½",
            "\u00e5" =>"ï¿½",
            "\u00e6" =>"ï¿½",
            "\u00e7" =>"ï¿½",
            "\u00e8" =>"ï¿½",
            "\u00e9" =>"ï¿½",
            "\u00ea" =>"ï¿½",
            "\u00eb" =>"ï¿½",
            "\u00ec" =>"ï¿½",
            "\u00ed" =>"ï¿½",
            "\u00ee" =>"ï¿½",
            "\u00ef" =>"ï¿½",
            "\u00f0" =>"ï¿½",
            "\u00f1" =>"ï¿½",
            "\u00f2" =>"ï¿½",
            "\u00f3" =>"ï¿½",
            "\u00f4" =>"ï¿½",
            "\u00f5" =>"ï¿½",
            "\u00f6" =>"ï¿½",
            "\u00f8" =>"ï¿½",
            "\u00f9" =>"ï¿½",
            "\u00fa" =>"ï¿½",
            "\u00fb" =>"ï¿½",
            "\u00fc" =>"ï¿½",
            "\u00fd" =>"ï¿½",
            "\u00ff" =>"ï¿½");

        return strtr($str, $utf8_ansi2);

    }

    public function utf8_converter($array, $encode = true)
    {
        array_walk_recursive($array, function(&$item, $key) use($encode) {
            if($encode && !mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
            }
            elseif (!$encode && mb_detect_encoding($item, 'utf-8', true))
            {
                $item = utf8_decode($item);
            }
        });

        return $array;
    }

    public function formatRequest(&$input,$options = array())
    {
        foreach ($input as $k1 => $v1)
        {
            if (!is_array($v1))
            {
                if (isset($options['trim']) && $options['trim'])
                {
                    $v1 = trim($v1);
                }
                if ($v1 == '' && isset($options['removeIfEmpty']) && $options['removeIfEmpty'])
                {
                    unset($input[$k1]);
                }
            }
            else
            {
                $this->formatRequest($v1, $options );
                $input[$k1] = $v1;
            }

        }
    }

    public function isNullOrEmpty($str)
    {
        return (is_null($str) || (is_string($str) && trim($str) == '')) ;

    }
}