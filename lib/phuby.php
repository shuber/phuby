<?php

abstract class Phuby {

    const VERSION = '0.0.0';

    /**
     * Delegates the call to Environment::autoload($class).
     * If the class is successfully loaded, a function with the name of the class is created (if it doesn't already exist)
     * which calls its corresponding class's invoke method.
     *
     * @param string $class
     * @return void
     * @static
    **/
    static function autoload($class) {
        Environment::autoload($class);
        if (class_exists($class, false)) {
            if (!function_exists($class)) {
                $reflection = new ReflectionClass($class);
                $function = sprintf('
                    namespace %s {
                        function %s() {
                            return Klass::instance("%s")->send_array("call", func_get_args());
                        }
                    }',
                    $reflection->getNamespaceName(),
                    $reflection->getShortName(),
                    $class
                );
                eval($function);
            }
        }
    }

    /**
     * Hides the "Non-static method should not be called statically" strict errors since this functionality is required.
     *
     * @param int $number
     * @param string $message
     * @param string $file
     * @param string $line
     * @param array $context
     * @return void|bool
     * @static
    **/
    static function non_static_method_call_error_handler($number, $message, $file, $line, &$context) {
        if ($number != 2048) return false;
    }

    /**
     * Hides the "undefined constant XXX" errors if XXX is the name of a class.
     *
     * @param int $number
     * @param string $message
     * @param string $file
     * @param string $line
     * @param array $context
     * @return void|bool
     * @static
    **/
    static function undefined_constant_error_handler($number, $message, $file, $line, &$context) {
        if ($number != 8 || !preg_match('#constant (\S+)#', $message, $matches) || !class_exists($matches[1])) return false;
    }

}