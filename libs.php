<?php
/**
 * @link https://github.com/OpenACalendar/OpenACalendar-Twitter
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) JMB Technology Limited, http://jmbtechnology.co.uk/
 */

function getTweetContent($prefix, $subject, $url) {
    $out = ($prefix ? $prefix . ' ': '').
           $subject;

    $shortenedLinksLength = 30;

    if (strlen($out) < (140 - $shortenedLinksLength)) {
        return $out . ' '. $url;
    } else {
        while(substr($out, -1) != ' ' || strlen($out) > (140 - $shortenedLinksLength - 3)) {
            $out = substr($out, 0, -1);
        }
        return $out . '... '. $url;
    }
}
