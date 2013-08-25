<?php

class EnvironmentTest extends PHPUnit_Framework_TestCase {
    /**
     * @dataProvider files
     */
    function test_filename($class, $filename) {
        $result = Phuby\Environment::filename($class);
        $this->assertEquals($filename, $result);
    }

    function files() {
        return [
            ['User','user.php'],
            ['TestIng', 'test_ing.php'],
            ['\TestIng', 'test_ing.php'],
            ['\Namespaced\Test\Ing', 'namespaced/test/ing.php'],
            ['Namespaced\Test\Ing', 'namespaced/test/ing.php'],
            ['TestIng_Underscore', 'test_ing_underscore.php']
        ];
    }
}