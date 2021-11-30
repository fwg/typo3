<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Core\Utility;

/**
 * Class with helper functions for string handling
 */
class StringUtility
{
    /**
     * This function generates a unique id by using the more entropy parameter.
     * Furthermore the dots are removed so the id can be used inside HTML attributes e.g. id.
     *
     * @param string $prefix
     * @return string
     */
    public static function getUniqueId($prefix = '')
    {
        $uniqueId = uniqid($prefix, true);
        return str_replace('.', '', $uniqueId);
    }

    /**
     * Escape a CSS selector to be used for DOM queries
     *
     * This method takes care to escape any CSS selector meta character.
     * The result may be used to query the DOM like $('#' + escapedSelector)
     *
     * @param string $selector
     * @return string
     */
    public static function escapeCssSelector(string $selector): string
    {
        return preg_replace('/([#:.\\[\\],=@])/', '\\\\$1', $selector);
    }

    /**
     * Removes the Byte Order Mark (BOM) from the input string. This method supports UTF-8 encoded strings only!
     *
     * @param string $input
     * @return string
     */
    public static function removeByteOrderMark(string $input): string
    {
        if (strpos($input, "\xef\xbb\xbf") === 0) {
            $input = substr($input, 3);
        }

        return $input;
    }

    /**
     * Matching two strings against each other, supporting a "*" wildcard (match many) or a "?" wildcard (match one= or (if wrapped in "/") PCRE regular expressions
     *
     * @param string $haystack The string in which to find $needle.
     * @param string $needle The string to find in $haystack
     * @return bool Returns TRUE if $needle matches or is found in (according to wildcards) $haystack. E.g. if $haystack is "Netscape 6.5" and $needle is "Net*" or "Net*ape" then it returns TRUE.
     */
    public static function searchStringWildcard($haystack, $needle): bool
    {
        $result = false;
        if ($haystack === $needle) {
            $result = true;
        } elseif ($needle) {
            if (preg_match('/^\\/.+\\/$/', $needle)) {
                // Regular expression, only "//" is allowed as delimiter
                $regex = $needle;
            } else {
                $needle = str_replace(['*', '?'], ['%%%MANY%%%', '%%%ONE%%%'], $needle);
                $regex = '/^' . preg_quote($needle, '/') . '$/';
                // Replace the marker with .* to match anything (wildcard)
                $regex = str_replace(['%%%MANY%%%', '%%%ONE%%%'], ['.*', '.'], $regex);
            }
            $result = (bool)preg_match($regex, $haystack);
        }
        return $result;
    }

    /**
     * Takes a comma-separated list and removes all duplicates.
     * If a value in the list is trim(empty), the value is ignored.
     *
     * @param string $list Accept a comma-separated list of values.
     * @return string Returns the list without any duplicates of values, space around values are trimmed.
     */
    public static function uniqueList(string $list): string
    {
        return implode(',', array_unique(GeneralUtility::trimExplode(',', $list, true)));
    }

    /**
     * Works the same as str_pad() except that it correctly handles strings with multibyte characters
     * and takes an additional optional argument $encoding.
     *
     * @param string $string
     * @param int $length
     * @param string $pad_string
     * @param int $pad_type
     * @param string $encoding
     * @return string
     */
    public static function multibyteStringPad(string $string, int $length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT, string $encoding = 'UTF-8'): string
    {
        $len = mb_strlen($string, $encoding);
        $pad_string_len = mb_strlen($pad_string, $encoding);
        if ($len >= $length || $pad_string_len === 0) {
            return $string;
        }

        switch ($pad_type) {
            case STR_PAD_RIGHT:
                $string .= str_repeat($pad_string, (int)(($length - $len)/$pad_string_len));
                $string .= mb_substr($pad_string, 0, ($length - $len) % $pad_string_len);
                return $string;

            case STR_PAD_LEFT:
                $leftPad = str_repeat($pad_string, (int)(($length - $len)/$pad_string_len));
                $leftPad .= mb_substr($pad_string, 0, ($length - $len) % $pad_string_len);
                return $leftPad . $string;

            case STR_PAD_BOTH:
                $leftPadCount = (int)(($length - $len)/2);
                $len += $leftPadCount;
                $padded = ((int)($leftPadCount / $pad_string_len)) * $pad_string_len;
                $leftPad = str_repeat($pad_string, (int)($leftPadCount / $pad_string_len));
                $leftPad .= mb_substr($pad_string, 0, $leftPadCount - $padded);
                $string = $leftPad . $string . str_repeat($pad_string, (int)(($length - $len)/$pad_string_len));
                $string .= mb_substr($pad_string, 0, ($length - $len) % $pad_string_len);
                return $string;
        }
        return $string;
    }
}
