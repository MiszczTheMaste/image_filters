<?php

namespace App\Filter;

require_once "required.php";

use App\Entity\RobertCross;
use App\ValueObject\RGBValueObject;
use GdImage;

class RobertFilter implements AbstractFilter
{
    private RobertCross $rpbertCross;

    public function setMask(RobertCross $rpbertCross): void
    {
        $this->rpbertCross = $rpbertCross;
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
                $rgbArray = imagecolorsforindex($srcImage, $colorIndex);
                $avarageColor = ($rgbArray['red'] + $rgbArray['green'] + $rgbArray['blue']) / 3;
                $newColorIndex = imageColorAllocate($newImage, $avarageColor, $avarageColor, $avarageColor);
                imageSetPixel($newImage, $x, $y, $newColorIndex);
                continue;
            }

            $neighborPixels = [];
            for ($i = 0; $i < $this->rpbertCross->getKernelSize(); $i++) {
                for ($j = 0; $j < $this->rpbertCross->getKernelSize(); $j++) {
                    $neighborPixels[$i][$j] = imagecolorat($srcImage, $x + $i, $y + $i);
                }
            }

            $red = $this->calculatePixelValue("red", $neighborPixels, $srcImage);
            $green = $this->calculatePixelValue("green", $neighborPixels, $srcImage);
            $blue = $this->calculatePixelValue("blue", $neighborPixels, $srcImage);
            $avarageColor = ($red->getValue() + $green->getValue() + $blue->getValue()) / 3;

            $newColorIndex = imageColorAllocate($newImage, $avarageColor, $avarageColor, $avarageColor);
            imageSetPixel($newImage, $x, $y, $newColorIndex);
        }
        return $newImage;
    }

    private function calculatePixelValue(string $color, array $neighborPixels, GdImage $srcImage): RGBValueObject
    {
        $pixelSum = 0;
        for ($i = 0; $i < $this->rpbertCross->getKernelSize(); $i++) {
            for ($j = 0; $j < $this->rpbertCross->getKernelSize(); $j++) {
                $rgbArray = imagecolorsforindex($srcImage, $neighborPixels[$i][$j]);
                $pixelSum += $rgbArray[$color] * $this->rpbertCross->getFirstMask($i, $j);
            }
        }
        $secondPixelSum = 0;
        for ($i = 0; $i < $this->rpbertCross->getKernelSize(); $i++) {
            for ($j = 0; $j < $this->rpbertCross->getKernelSize(); $j++) {
                $rgbArray = imagecolorsforindex($srcImage, $neighborPixels[$i][$j]);
                $secondPixelSum += $rgbArray[$color] * $this->rpbertCross->getSecondMask($i, $j);
            }
        }
        return new RGBValueObject(round(abs($pixelSum) + abs($secondPixelSum)));
    }

    private function isOutOfBoundary(int $coordinate, int $boundary): bool
    {
        return ($coordinate >= $boundary - $this->rpbertCross->getKernelBoundary());
    }
}
