<?php

namespace App\Traits;

trait AppThemeTrait
{
    function getAppTheme(int $cookie)
    {
        $cookie === 1 ? $theme = 'dark-mode' : $theme = '';
        return $theme;
    }
}