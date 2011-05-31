# phuby (foo-bee)

[rubyisms](http://www.ruby-lang.org/) in php


## Status

Pre-alpha

Code is stable and test backed, however the api may change as features are implemented or refactored


## Dependencies

* php 5.3+


## Usage preview

	require_once 'phuby/phuby.php';

	namespace Person {
	    class InstanceMethods {
	        public $name;

	        function initialize($name) {
	            $this->name = $name;
	        }

	        function greet() {
	            return 'Hello, my name is '.$this->name;
	        }
	    }

	    class Surname {
	        public $surname;

	        function initialize($name, $surname = '') {
	            $this->super($name);
	            $this->surname = $surname;
	        }

	        function greet() {
	            $greeting = $this->super;
	            if ($this->surname) $greeting .= ' '.$this->surname;
	            return $greeting;
	        }
	    }
	}

	namespace {
	    function c($class) { return Klass::instance($class); }

	    class Person extends Object { }
	    c(Person)->include('Person\InstanceMethods');

	    $tom = c(Person)->new('Tom');
	    echo $tom->greet; // Hello, my name is Tom

	    c(Person)->include('Person\Surname');

	    $tom->surname = 'Jones';
	    echo $tom->greet; // Hello, my name is Tom Jones

	    $jane = c(Person)->new('Jane', 'Smith');
	    echo $jane->greet; // Hello, my name is Jane Smith
	}


## Testing

Phuby uses [ztest](http://github.com/jaz303/ztest) - simply download it to `phuby/test/ztest` (or anywhere else in your php `include_path`), then run `test/run`