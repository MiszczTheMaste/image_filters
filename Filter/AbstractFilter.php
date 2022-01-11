<?php

namespace App\Filter;

require_once "required.php";

use GdImage;

interface AbstractFilter
{
    public function filter(GdImage $srcImage): GdImage;
}