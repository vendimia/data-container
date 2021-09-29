<?php
use Vendimia\DataContainer\ValueObject;

class TestValueObjectClass extends ValueObject
{
    public function __construct
    (
        public readonly string $name,
        public readonly int $score,
        public readonly ?string $remarks,
    )
    {

    }
};
