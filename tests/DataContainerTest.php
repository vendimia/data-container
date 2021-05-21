<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Vendimia\DataContainer\DataContainer;

require __DIR__ . '/../src/DataContainer.php';

class Data extends DataContainer
{
    public $name;

    public int $code;
}

final class DataContainerTest extends TestCase
{
    public function testCreation(): DataContainer
    {
        $dc = new Data;

        // No idea
        $this->assertTrue($dc instanceof DataContainer);

        return $dc;
    }

    /** 
     * @depends testCreation
     */
    public function testInvalidPropertyMustThrowException(DataContainer $dc): void
    {
        $this->expectException(LogicException::class);

        $dc->nonExistantProperty = "hi";
    }

    /** 
     * @depends testCreation
     */
    public function testInvalidArrayIndexMustThrowException(DataContainer $dc): void
    {
        $this->expectException(LogicException::class);

        $dc['nonExistantIndex'] = "hi";
    }

    /** 
     * @depends testCreation
     */
    public function testWritePropertyAndReadIndex(DataContainer $dc): void
    {
        $dc->name = 'John';

        $this->assertSame($dc['name'], 'John');
    }

    /** 
     * @depends testCreation
     */
    /*public function testPhpShouldEnforceValueTypeIntegrity(DataContainer $dc): void
    {
        $this->expectException(TypeError::class);

        $dc->code = "bazzinga";
    }*/

    /** 
     * @depends testCreation
     */
    public function testNullValuesShouldReturnIncomplete(DataContainer $dc): void
    {
        $dc->name = null;
        
        $this->assertSame($dc->isComplete(), false);
    }
}