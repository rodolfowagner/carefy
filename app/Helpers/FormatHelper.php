<?php

/*
 * include in config/app.php to use in blade
 */

namespace App\Helpers;

class FormatHelper
{
    public static function invertDate($date, $separator=null) 
	{
        return implode(!strstr($date, '/') ? "/" : "-", array_reverse(explode(!strstr($date, '/') ? "-" : "/", $date)));
    }
}