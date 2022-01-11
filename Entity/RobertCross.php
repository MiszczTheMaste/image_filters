<?php

namespace App\Entity;

require_once "required.php";


class RobertCross
{
    private array $firstKernel;
    private array $secondKernel;

    const KERNEL_SIZE = 2;

    public function __construct()
    {
        $this->generateKernel();
    }

    public function getKernel(): array
    {
        return $this->kernel;
    }

    public function getFirstMask(int $y, int $x): float
    {
        return $this->firstKernel[$y][$x];
    }
    
    public function getSecondMask(int $y, int $x): float
    {
        return $this->secondKernel[$y][$x];
    }

    private function generateKernel(): void
    {
        $this->firstKernel[0][0] = 1;
        $this->firstKernel[0][1] = -1;
        $this->firstKernel[1][0] = 0;
        $this->firstKernel[1][1] = 0;
        
        $this->secondKernel[0][0] = 1;
        $this->secondKernel[0][1] = 0;
        $this->secondKernel[1][0] = -1;
        $this->secondKernel[1][1] = 0;
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
