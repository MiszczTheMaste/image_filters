<?php

namespace App\ValueObject;

class RGBValueObject extends AbstractValueObject
{
    protected function validate($value): void
    {
    }

    protected function initialConversion($value)
    {
        if ($value > 255) {
            $value = 255;
        }

        if ($value < 0) {
            $value = 0;
        }

        return $value;
    }

    public function isGreaterThan($compareValue){
        return $this->value > $compareValue;
    }
}
