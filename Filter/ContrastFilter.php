<?php

namespace App\Filter;

require_once "required.php";

use App\ValueObject\RGBValueObject;
use GdImage;

class ContrastFilter implements AbstractFilter
{
    private RGBValueObject $minValue;

    private RGBValueObject $maxValue;

    public function filter(GdImage $srcImage): GdImage
    {
        $grayScaleFilter = new GrayScaleFilter();
        $grayScaleImage = $grayScaleFilter->filter($srcImage);

        $newImage = imageCreateTrueColor(ImageSX($srcImage), ImageSY($srcImage));
        $pixelCount = imageSX($srcImage) * imageSY($srcImage);

        $imageWidth = ImageSX($srcImage);

        $this->getMinMaxValues($grayScaleImage, $pixelCount, $imageWidth);

        for ($pixel = 0; $pixel < $pixelCount; $pixel++) {
            $x = $pixel % $imageWidth;
            $y = ($pixel - $x) / $imageWidth;

            $colorIndex = imagecolorat($srcImage, $x, $y);
            $rgbArray = imagecolorsforindex($srcImage, $colorIndex);

            $red = $this->calculateStretching($rgbArray['red']);
            $green = $this->calculateStretching($rgbArray['green']);
            $blue = $this->calculateStretching($rgbArray['blue']);

            $newColorIndex = imageColorAllocate($newImage, $red->getValue(), $green->getValue(), $blue->getValue());
            imageSetPixel($newImage, $x, $y, $newColorIndex);
        }
        return $newImage;
    }

    private function calculateStretching($value): RGBValueObject
    {
        return new RGBValueObject((($value - $this->minValue->getValue()) / ($this->maxValue->getValue() - $this->minValue->getValue())) * 255);
    }

    private function getMinMaxValues(GdImage $grayScaleImage, int $pixelCount, int $imageWidth): void
    {
        $minValue = 255;
        $maxValue = 0;
        for ($pixel = 0; $pixel < $pixelCount; $pixel++) {
            $x = $pixel % $imageWidth;
            $y = ($pixel - $x) / $imageWidth;

            $colorIndex = imagecolorat($grayScaleImage, $x, $y);
            $rgbArray = imagecolorsforindex($grayScaleImage, $colorIndex);

            $value = $rgbArray['red'];

            if ($value < $minValue) {
                $minValue = $value;
            }

            if ($value > $maxValue) {
                $maxValue = $value;
            }
        }

        $this->minValue = new RGBValueObject($minValue);
        $this->maxValue = new RGBValueObject($maxValue);
    }
}
