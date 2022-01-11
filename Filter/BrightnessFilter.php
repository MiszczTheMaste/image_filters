<?php

namespace App\Filter;

require_once "required.php";

use App\ValueObject\RGBValueObject;
use GdImage;

class BrightnessFilter implements AbstractFilter
{
    private int $brightnessValue = 0;

    public function setBrightnessValue($value): void
    {
        $this->brightnessValue = $value;
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

            $red = new RGBValueObject($rgbArray['red'] + $this->brightnessValue);
            $green = new RGBValueObject($rgbArray['green'] + $this->brightnessValue);
            $blue = new RGBValueObject($rgbArray['blue'] + $this->brightnessValue);

            $newColorIndex = imageColorAllocate($newImage, $red->getValue(), $green->getValue(), $blue->getValue());
            imageSetPixel($newImage, $x, $y, $newColorIndex);
        }
        return $newImage;
    }
}
