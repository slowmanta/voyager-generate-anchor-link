<?php

namespace NamMT\VoyagerGenerateAnchorLink;

class Seed
{
    public static function getFolderName()
    {
        return version_compare(app()->version(), '8.0') >= 0 ? 'seeders' : 'seeds';
    }
}
