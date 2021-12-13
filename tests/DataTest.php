<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class DataTest extends TestCase
{
    /**
     * @dataProvider additionProviderArray
     */
    public function testData(array $provider): void
    { 
        $class = new \core\Data(['valorA1', 'valorA2']);
        $this->assertIsArray($class->getAll(), 'No es array');
        //fwrite(STDOUT, var_dump($class->getAll()) . "\n");

        $class->addItem('valueA3'); 
        $this->assertIsArray($class->getAll(), 'No es array');
        //fwrite(STDOUT, var_dump($class->getAll()) . "\n");

        $class->addItems(['valueA4', 'valueA5']); 
        $this->assertIsArray($class->getAll(), 'No es array');
        //fwrite(STDOUT, var_dump($class->getAll()) . "\n");
    }

    public function additionProviderArray()
    {
        $arr1 = array('valorA1', 'valorA2');
        $arr2 = array('valorB121', 'valorB2');
        return [[$arr1, $arr2]];
    }
}