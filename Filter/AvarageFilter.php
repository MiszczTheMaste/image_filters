<?php

namespace App\Filter;

require_once "required.php";

use App\ValueObject\RGBValueObject;
use GdImage;

class AvarageFilter implements AbstractFilter
{
    private int $maskSize = 3;

    private int $maskBoundary = 1;

    private int $maskWeight = 3;

    public function setMaskSize(int $maskSize): void
    {
        $this->maskSize = $maskSize;
        $this->maskBoundary = floor($this->maskSize / 2);
    }

    public function setMastWeight(int $weight): void
    {
        $this->maskWeight = $weight;
    }

    public function filter(GdImage $srcImage): GdImage
    {
        $newImage = imageCreateTrueColor(ImageSX($srcImage), ImageSY($srcImage));
        $pixelCount = imageSX($srcImage) * imageSY($srcImage);

        $imageWidth = ImageSX($srcImage);
        $imageHeight = imageSY($srcImage);

        for ($pixel = 0; $pixel < $pixelCount; $pixel++) {
            $x = $pixel % $imageWidth;
            $y = ($pixel - $x) / $imageWidth;

            if ($this->isOutOfBoundary($x, $imageWidth) || $this->isOutOfBoundary($y, $imageHeight)) {
                $colorIndex = imagecolorat($srcImage, $x, $y);
                imageSetPixel($newImage, $x, $y, $colorIndex);
                continue;
            }

            $neighborPixels = [];
            for ($i = 0; $i < $this->maskSize; $i++) {
                for ($j = 0; $j < $this->maskSize; $j++) {
                    $neighborPixels[$i][$j] = imagecolorat($srcImage, $x + $i - $this->maskBoundary, $y + $i - $this->maskBoundary);
                }
            }

            $red = $this->calculatePixelValue("red", $neighborPixels, $srcImage);
            $green = $this->calculatePixelValue("green", $neighborPixels, $srcImage);
            $blue = $this->calculatePixelValue("blue", $neighborPixels, $srcImage);

            $newColorIndex = imageColorAllocate($newImage, $red->getValue(), $green->getValue(), $blue->getValue());
            imageSetPixel($newImage, $x, $y, $newColorIndex);
        }
        return $newImage;
    }

    private function calculatePixelValue(string $color, array $neighborPixels, GdImage $srcImage): RGBValueObject
    {
        $pixelSum = 0;
        $maskSum = 0;
        for ($i = 0; $i < $this->maskSize; $i++) {
            for ($j = 0; $j < $this->maskSize; $j++) {
                $rgbArray = imagecolorsforindex($srcImage, $neighborPixels[$i][$j]);
                $pixelSum += $rgbArray[$color] * $this->maskWeight;
                $maskSum += $this->maskWeight;
            }
        }

        return new RGBValueObject($pixelSum / $maskSum);
    }

    private function isOutOfBoundary(int $coordinate, int $boundary): bool
    {
        return ($coordinate <= $this->maskBoundary || $coordinate >= $boundary - $this->maskBoundary);
    }
}
