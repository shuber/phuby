<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase {
    function testFilename() {
        $classes = [
            'User' => 'user.php',
            'TestIng' => 'test_ing.php',
            '\TestIng' => 'test_ing.php',
            '\Namespaced\Test\Ing' => 'namespaced/test/ing.php',
            'Namespaced\Test\Ing' => 'namespaced/test/ing.php',
            'TestIng_Underscore' => 'test_ing_underscore.php'
        ];
        foreach ($classes as $class => $expected) {
            $result = Phuby\Environment::filename($class);
            $this->assertEquals($expected, $result);
        }
    }
}