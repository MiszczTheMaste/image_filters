<?php

namespace App\Entity;

require_once "required.php";

use GdImage;

class Image
{
    private GdImage $srcImage;

    const IMAGE_DIRECTORY = "output/";

    public function __construct(string $imageFile)
    {
        $this->srcImage = imagecreatefromjpeg($imageFile);
    }

    public function getSrcImage(): GdImage
    {
        return $this->srcImage;
    }

    public function saveImage(GdImage $imageToSave, string $imageName): void
    {
        imagepng($imageToSave, self::IMAGE_DIRECTORY . $imageName);
    }
}
