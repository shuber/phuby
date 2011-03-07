# phuby (foo-bee)

[rubyisms](http://www.ruby-lang.org/) in php


## Status

Pre-alpha

Code is stable and test backed, however the api may change as features are implemented or refactored


## Dependencies

* php 5.3+


## Features (incomplete)

* mixins
* classes are objects
* eigenclasses
* method\_missing, respond\_to, respond\_to_missing, send, super
* splat
* extended, included, inherited

(See TODO below)


## Usage preview (instance variables aren't supported yet)

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


## Todo

* class\_eval and instance\_eval - (should also accept string arguments which are embedded in a new random module and evaluated, then included/extended)
* ${ClassName} syntax
* instance/class variables (class's instance\_vars array should store references to real vars)
* method_added, included, extended, inherited, call
* check access scope in __get, __set, __call - see if we can get something working with Object#coerce (just changes __class?) and ReflectionMethod
* implicitly pass all arguments when calling super with __get
* Object#caller
* respond\_to('super'), send('super'), send_array('super')
* ruby style method access http://en.wikibooks.org/wiki/Ruby_Programming/Syntax/Classes#Public
* extend('self')
* move everything under Phuby namespace
* alias methods
* c(User)->include vs __include issue
* splat
* Klass#freeze