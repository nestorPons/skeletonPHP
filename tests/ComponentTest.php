<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ComponentTest extends TestCase
{
    /**
     * @test 
     */
    public function styleScoped(): void
    {
        try {

            define('FOLDER\COMPONENTS','/home/admin/Projects/skeletonPHP/src/components/');
            $el = new \core\Component('m-input', '{"id":"mycomponent","label":"Introduce un valor"}' , 'null');
            fwrite(STDOUT, var_dump($el->print()) . "\n");
            $this->assertInstanceOf('\core\Component', $el);

        } catch (\Throwable $th) {

            $this->fail($th->getMessage());
        }
    }
}
