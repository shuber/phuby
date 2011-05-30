<?php

namespace Phuby {
    abstract class ErrorHandler {

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

    }
}