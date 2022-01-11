<?php

namespace App\Histogram;

require_once "required.php";

use GdImage;

class HistogramGenerator
{
    private function generateArray(): array
    {
        $array = [];
        for ($i = 0; $i < 256; $i++) {
            $array[$i] = 0;
        }
        return $array;
    }
    public function generate(GdImage $srcImage): array
    {
        $pixelCount = imageSX($srcImage) * imageSY($srcImage);

        $imageWidth = ImageSX($srcImage);

        $redArray = $this->generateArray();
        $greenArray = $this->generateArray();
        $blueArray = $this->generateArray();

        for ($pixel = 0; $pixel < $pixelCount; $pixel++) {
            $x = $pixel % $imageWidth;
            $y = ($pixel - $x) / $imageWidth;

            $rgb = imagecolorat($srcImage, $x, $y);
            $pix = imagecolorsforindex($srcImage, $rgb);
            $redArray[$pix['red']]++;
            $greenArray[$pix['green']]++;
            $blueArray[$pix['blue']]++;
        }

        return ["red" => $redArray, "green" => $greenArray, "blue" => $blueArray];
    }
}
