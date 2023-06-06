<?php

namespace App\Common;

class FormatSizeFile
{
    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 0) . ' ГБ';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 0) . ' МБ';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 0) . ' КБ';
        }
        elseif ($bytes > 1 || $bytes == 1)
        {
            $bytes = $bytes . ' Байт';
        }
        else
        {
            $bytes = '0 Байт';
        }

        return $bytes;
    }
}
