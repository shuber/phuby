= phuby

== Usage

	namespace Person {
	    class InstanceMethods {
	        public $name;

	        function initialize($name) {
	            $this->name = $name;
	        }

	        function greet() {
	            return 'Hi '.$name;
	        }
	    }

	    class Surname {
	        public $surname;

	        function initialize($name, $surname = '') {
	            $this->super($name);
	            $this->surname = $surname;
	        }
	    }
	}

	namespace {
	    class Person extends Object { }
	    ${Person}->include('Person\InstanceMethods');

	    $tom = ${Person}->new('Tom');
	    echo $tom->greet; // Hi Tom

	    ${Person}->include('Person\Surname');

	    $tom->surname = 'Jones';
	    echo $tom->greet; // Hi Tom Jones

	    $jane = ${Person}->new('Jane', 'Smith');
	    echo $jane->greet; // Hi Jane Smith
	}

== Todo

* class\_eval and instance\_eval - (should also accept string arguments which are embedded in a new random module and evaluated, then included/extended)
* ${ClassName} syntax
* instance/class variables (class's instance\_vars array should store references to real vars)
* method_added, included, extended, inherited, call
* check access scope in __get, __set, __call