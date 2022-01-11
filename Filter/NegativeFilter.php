<?php

namespace App\Filter;

require_once "required.php";

use App\ValueObject\RGBValueObject;
use GdImage;

class NegativeFilter implements AbstractFilter
{
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

            $red = $this->calcultaeNegative($rgbArray['red']);
            $green = $this->calcultaeNegative($rgbArray['green']);
            $blue = $this->calcultaeNegative($rgbArray['blue']);

            $newColorIndex = imageColorAllocate($newImage, $red->getValue(), $green->getValue(), $blue->getValue());
            imageSetPixel($newImage, $x, $y, $newColorIndex);
        }

        return $newImage;
    }

    private function calcultaeNegative($value): RGBValueObject
    {
        return new RGBValueObject(255 - $value);
    }
}
