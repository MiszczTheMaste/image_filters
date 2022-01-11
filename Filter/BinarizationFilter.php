<?php

namespace App\Filter;

require_once "required.php";

use App\ValueObject\RGBValueObject;
use GdImage;

class BinarizationFilter implements AbstractFilter
{
    private int $treshold = 0;

    public function setTreshold($value): void
    {
        $this->treshold = $value;
    }

    public function filter(GdImage $srcImage): GdImage
    {
        $newImage = imageCreateTrueColor(ImageSX($srcImage), ImageSY($srcImage));
        $pixelCount = imageSX($srcImage) * imageSY($srcImage);

        $imageWidth = ImageSX($srcImage);

        for ($pixel = 0; $pixel < $pixelCount; $pixel++) {
            $x = $pixel % $imageWidth;
            $y = ($pixel - $x) / $imageWidth;

            $colorIndex = imagecolorat($srcImage, $x, $y);
            $rgbArray = imagecolorsforindex($srcImage, $colorIndex);

            $avarageRgb = new RGBValueObject(($rgbArray['red'] + $rgbArray['green'] + $rgbArray['blue']) / 3);
            if ($avarageRgb->isGreaterThan($this->treshold)) {
                $avarageRgb = new RGBValueObject(255);
            } else {
                $avarageRgb = new RGBValueObject(0);
            }
            $newColorIndex = imageColorAllocate($newImage, $avarageRgb->getValue(), $avarageRgb->getValue(), $avarageRgb->getValue());
            imageSetPixel($newImage, $x, $y, $newColorIndex);
        }

        return $newImage;
    }

    private function calcultaeNegative($value): RGBValueObject
    {
        return new RGBValueObject(255 - $value);
    }
}
