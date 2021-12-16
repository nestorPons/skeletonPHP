<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ComponentTest extends TestCase
{
    /**
     * @test 
     * @covers Component
     */
    public function testStyleScoped(): void
    {
        try {

            define('FOLDER\COMPONENTS','/home/admin/Projects/skeletonPHP/src/components/');
            $el = new \core\Component('m-input', '{"id":"mycomponent","label":"Introduce un valor"}' , 'null');
            $this->assertInstanceOf('\core\Component', $el);

        } catch (\Throwable $th) {
            $this->fail($th->getMessage());
        }
    }
}
