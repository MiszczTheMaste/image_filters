<?php

namespace App\Entity;

require_once "required.php";


class SharpnessMask
{
    private array $kernel;

    private int $kernelWeight;

    const KERNEL_SIZE = 3;

    public function __construct(int $kernelWeight)
    {
        $this->kernelWeight = $kernelWeight;
        $this->generateKernel();
    }

    public function getKernel(): array
    {
        return $this->kernel;
    }

    public function getMask(int $y, int $x): float
    {
        return $this->kernel[$y][$x];
    }

    private function generateKernel(): void
    {
        $restToDivide = 1 - $this->kernelWeight;
        $outerFieldsValue = $restToDivide / (pow(self::KERNEL_SIZE,2) - 1);

        for ($x = 0; $x < self::KERNEL_SIZE; $x++) {
            for ($y = 0; $y < self::KERNEL_SIZE; $y++) {
                $this->kernel[$x][$y] = $outerFieldsValue;
            }
        }
        $this->kernel[1][1] = $this->kernelWeight;
    }

    public function getKernelSize(): int
    {
        return self::KERNEL_SIZE;
    }

    public function getKernelBoundary(): int
    {
        return floor(self::KERNEL_SIZE / 2);
    }
}
