<?php

namespace Phuby\Core {
    /**
     * Handles Phuby related errors.
    **/
    abstract class ErrorHandler {

        const NON_STATIC_METHOD_CALL = 2048;
        const UNDEFINED_CONSTANT = 8;

        /**
         * Hides the "Non-static method should not be called statically" strict errors since this functionality is required.
         *
         * @param int $number
         * @param string $message
         * @param string $file
         * @param string $line
         * @param array $context
         * @return void | bool
        **/
        static function non_static_method_call_error_handler($number, $message, $file, $line, &$context) {
            if ($number != static::NON_STATIC_METHOD_CALL) return false;
        }

        /**
         * Hides the "undefined constant Foo" errors if Foo is the name of a class.
         *
         * @param int $number
         * @param string $message
         * @param string $file
         * @param string $line
         * @param array $context
         * @return void | bool
        **/
        static function undefined_constant_error_handler($number, $message, $file, $line, &$context) {
            if ($number != static::UNDEFINED_CONSTANT || !preg_match('#constant\s+(\S+)#', $message, $matches) || !class_exists($matches[1])) return false;
        }

    }
}