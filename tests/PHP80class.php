<?php
use Vendimia\DataContainer\ValueObject;

class TestValueObjectClass extends ValueObject
{
    public function __construct
    (
        protected string $name,
        protected int $score,
        protected ?string $remarks,
    )
    {

    }
};
