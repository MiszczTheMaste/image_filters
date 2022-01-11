<?php

namespace App\Entity;

require_once "required.php";


class GaussMask
{
    private array $kernel;

    private float $kernelSum = 0;

    private int $kernelSize;

    const STANDARD_DEVIATION = 1;

    public function __construct(int $kernelSize)
    {
        $this->kernelSize = $kernelSize;
        $this->generateKernel();
        $this->normalizeKernel();
    }

    public function getKernel(): array
    {
        return $this->kernel;
    }

    public function getMask(int $y, int $x): float
    {
        return $this->kernel[$y][$x];
    }

    private function normalizeKernel(): void
    {
        for ($x = 0; $x < $this->kernelSize; $x++) {
            for ($y = 0; $y < $this->kernelSize; $y++) {
                $this->kernel[$x][$y] /= $this->kernelSum;
            }
        }
    }

    private function generateKernel(): void
    {
        $kernelStart = 0 - floor($this->kernelSize / 2);
        $kernelEnd = $this->kernelSize + $kernelStart;
        for ($x = $kernelStart; $x < $kernelEnd; $x++) {
            for ($y = $kernelStart; $y < $kernelEnd; $y++) {
                //for the equasion 0,0 needs to be center of kernel
                $x2 = $x - $kernelStart;
                $y2 = $y - $kernelStart;

                //substity for shorter equasion
                $r = sqrt($x * $x + $y * $y);
                //substity for shorter equasion
                $s = self::STANDARD_DEVIATION * self::STANDARD_DEVIATION * 2;
                //the equasion
                $this->kernel[$x2][$y2] = (exp(- ($r * $r) / $s)) / (pi() * $s);

                $this->kernelSum += $this->kernel[$x2][$y2];
            }
        }
    }

    public function getKernelSize(): int
    {
        return $this->kernelSize;
    }

    public function getKernelBoundary(): int
    {
        return floor($this->kernelSize / 2);
    }
}
