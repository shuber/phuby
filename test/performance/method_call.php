<?php

require_once __DIR__.'/../../phuby.php';

$_ENV['value'] = 3.1415962654;

class NativeClass {
    function test() {
        return $_ENV['value'];
    }
}

class PhubyClass extends Object {
    function test() {
        return $_ENV['value'];
    }
}

class MixinClass extends Object { }
class Mixin {
    function test() {
        return $_ENV['value'];
    }
}
Klass::instance('MixinClass')->include('Mixin');

$classes = array('NativeClass', 'PhubyClass', 'MixinClass');

foreach ($classes as $class) {
    $times = array();
    for ($t = 0; $t < 5; $t++) {
        $start_time = microtime(true);
        for ($i = 0; $i <= 1000; $i++) {
            $c = new $class;
            $c->test();
        }
        $times[$t] = round(microtime(true) - $start_time, 7);
    }
    echo "\n$class: ";
    $times['A'] = round(array_sum($times) / count($times), 7);
    print_r($times);
}