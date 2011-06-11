<?php

namespace Phuby\Core\ErrorHandlerTest {
    class User { }
}

namespace Phuby\Core {
    class ErrorHandlerTest extends \ztest\UnitTestCase {

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