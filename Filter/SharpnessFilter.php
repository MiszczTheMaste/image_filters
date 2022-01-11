<?php

namespace App\Filter;

require_once "required.php";

use App\Entity\SharpnessMask;
use App\ValueObject\RGBValueObject;
use GdImage;

class SharpnessFilter implements AbstractFilter
{
    private SharpnessMask $sharpnessMask;
    public function setMask(SharpnessMask $sharpnessMask): void
    {
        $this->sharpnessMask = $sharpnessMask;
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
            for ($i = 0; $i < $this->sharpnessMask->getKernelSize(); $i++) {
                for ($j = 0; $j < $this->sharpnessMask->getKernelSize(); $j++) {
                    $neighborPixels[$i][$j] = imagecolorat($srcImage, $x + $i - $this->sharpnessMask->getKernelBoundary(), $y + $i - $this->sharpnessMask->getKernelBoundary());
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
        for ($i = 0; $i < $this->sharpnessMask->getKernelSize(); $i++) {
            for ($j = 0; $j < $this->sharpnessMask->getKernelSize(); $j++) {
                $rgbArray = imagecolorsforindex($srcImage, $neighborPixels[$i][$j]);
                $pixelSum += $rgbArray[$color] * $this->sharpnessMask->getMask($i, $j);
                $maskSum += $this->sharpnessMask->getMask($i, $j);
            }
        }
        if ($maskSum == 0) {
            $maskSum = 1;
        }
        return new RGBValueObject(round($pixelSum / $maskSum));
    }

    private function isOutOfBoundary(int $coordinate, int $boundary): bool
    {
        return ($coordinate <= $this->sharpnessMask->getKernelBoundary() || $coordinate >= $boundary - $this->sharpnessMask->getKernelBoundary());
    }
}
