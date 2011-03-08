<?php

namespace Method\SplatTest {
    class User extends \Object { }
    \Klass::instance('Method\SplatTest\User')->__include('Method\Splat');
}

namespace Method {
    class SplatTest extends \ztest\UnitTestCase {

        function setup() {
            $this->user = new SplatTest\User;
        }

        function test_should_intercept_splat_calls() {
            ensure($this->user->respond_to_array(array('__class')));
            ensure(!$this->user->respond_to_array(array('invalid_method')));
        }

        function test_should_pass_call_to_super() {
            $user = $this->user;
            assert_throws('BadMethodCallException', function() use ($user) { $user->invalid_method_call(); });
        }

        function test_should_respond_to_splat_methods() {
            ensure($this->user->respond_to('object_id_array'));
        }

        function test_should_return_splat_method() {
            assert_equal('object_id', $this->user->splat_method('object_id_array'));
            ensure(!$this->user->splat_method('object_id'));
            ensure(!$this->user->splat_method('invalid_method'));
        }
    }
}