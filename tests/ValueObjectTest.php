<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Vendimia\DataContainer\ValueObject;

require __DIR__ . '/../vendor/autoload.php';

class ValueObjectTest extends TestCase
{
    public function testCreation(): ValueObject
    {
        if (version_compare(PHP_VERSION, "8.1", '>=')) {
            define('VERSION80', false);
            require(__DIR__ . '/PHP81class.php');
        } else {
            define('VERSION80', true);
            require(__DIR__ . '/PHP80class.php');
        }

        $vo = new TestValueObjectClass(
            name: "Oliver",
            score: 100,
            remarks: 'Ferpect'
        );

        $this->assertInstanceof(TestValueObjectClass::class, $vo);

        return $vo;
    }

    /**
     * @depends testCreation
     */
    public function testMustEnforceImmutability(ValueObject $vo)
    {
        /**
         * PHP8.1 throws an Error. PHP8.0 should throw an InvalidArgument
         */
        if (VERSION80) {
            $this->expectException(LogicException::class);
        } else {
            $this->expectException(Error::class);
        }

        $vo->name = "John Doe";
        $vo->newproperty = "a value";
    }

    /**
     * @depends testCreation
     */
    public function testAccessAsProperty(ValueObject $vo)
    {
        $this->assertEquals(
            "Oliver",
            $vo->name
        );
    }
}