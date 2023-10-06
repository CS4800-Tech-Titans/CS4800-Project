<?php

use PHPUnit\Framework\TestCase;

class getLastWordTest extends TestCase {
    public function testGetLastWord() 
    {
        require '../getLastWord.php';

        // Test cases
        $this->assertEquals("World", getLastWord('Hello World'));
        $this->assertEquals("cool", getLastWord('PHP is pretty cool'));
        $this->assertEquals("case", getLastWord('Heres an interesting test   case       '));
    }
}