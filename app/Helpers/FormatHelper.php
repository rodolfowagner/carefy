<?php

namespace App\Helpers;

class FormatHelper
{
    public static function invertDate($date, $separator=null) 
	{
        return implode(!strstr($date, '/') ? "/" : "-", array_reverse(explode(!strstr($date, '/') ? "-" : "/", $date)));
    }
}