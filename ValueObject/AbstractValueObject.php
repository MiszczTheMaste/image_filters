<?php
namespace App\ValueObject;

abstract class AbstractValueObject
{
    protected $value;

    public function __construct($value)
    {
        $this->validate($value);
        $value = $this->initialConversion($value);
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString():string
    {
        return (string)$this->getValue();
    }

    abstract protected function validate($value): void;

    abstract protected function initialConversion($value);
}