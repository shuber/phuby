<?php

namespace Phuby\ErrorHandlerTest {
    class User { }
}

namespace Phuby {
    class ErrorHandlerTest extends \ztest\UnitTestCase {

        function test_static_non_static_method_call_error_handler() {
            $handler = $this->handler('non_static_method_call_error_handler');
            assert_equal(false, $handler());
            assert_equal(null, $handler(ErrorHandler::NON_STATIC_METHOD_CALL));
        }

        function test_static_undefined_constant_error_handler() {
            $handler = $this->handler('undefined_constant_error_handler');
            assert_equal(false, $handler());
            assert_equal(false, $handler(ErrorHandler::UNDEFINED_CONSTANT));
            assert_equal(false, $handler(ErrorHandler::UNDEFINED_CONSTANT, 'constant '.__CLASS__.NS.'Invalid'));
            assert_equal(null, $handler(ErrorHandler::UNDEFINED_CONSTANT, 'constant '.__CLASS__.NS.'User'));
        }

        protected function handler($method) {
            return function() use ($method) {
                $arguments = array(0, 'message', 'file', 'line', array());
                foreach (func_get_args() as $key => $argument) $arguments[$key] = $argument;
                return @call_user_func_array(__NS__.'ErrorHandler::'.$method, $arguments);
            };
        }

    }
}