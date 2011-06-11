<?php

namespace Phuby\Core {
    /**
     * Handles Phuby related errors.
    **/
    abstract class ErrorHandler {

        const UNDEFINED_CONSTANT = 8;

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