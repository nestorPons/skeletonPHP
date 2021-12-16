<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TagsTest extends TestCase
{
    /**
     * @test
     * @testdox Prueba de borrado de atributos  
     */
    public function delAttr(): void
    {
        try {
            $el = self::tagComponent();
            $tag = new \core\Tag($el);
            $this->assertInstanceOf('\core\Tag', $tag);
            $delete = $tag->delAttr('lang');
            $this->assertTrue($delete);

            //fwrite(STDOUT, $tag->element() . "\n");
        } catch (\Throwable $th) {

            $this->fail($th->getMessage());
        }
    }
    /**
     * @test
     * @testdox Comprueba el correcto funcionamiento del método extract_pattern_attr de la clase Tag. 
     */
    public function ExtractPatternAttr(): void
    {
        try {
            $el = self::tagComponent();
            $tag = new \core\Tag($el);
            $this->assertInstanceOf('\core\Tag', $tag);

            // Extrae el atibuto pattern del elemento
            $ex = $tag->extract_pattern_attr($el);
            $this->assertIsArray($ex);
            // Comprobamos que debuelva un true pq debe de encontrar el atributo
            $this->assertTrue($ex[0]);
            // Comprobamos que ha extraido el atributo 

            $ex2 = $tag->extract_pattern_attr($ex[3]);
            //$this->assertFalse($ex2[0]);
            //fwrite(STDOUT, var_dump($ex) . "\n");
        } catch (\Throwable $th) {

            $this->fail($th->getMessage());
        }
    }

    public static function tagComponent()
    {
        return
            ' 
        <m-input lang="less" pattern="\[.*?]\">
            Esto es un componente
        </m-input>
        ';
    }
}
