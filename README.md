# phuby

## general

* phpunit tests
* catch errors and raise them as exceptions
* Ruby conventions for method visibility
* <strike>all classes inherit `Object`</strike>
* <strike>classes may `use Phuby` instead of inheriting `Phuby\Object`</strike>
* <strike>autoloading with ruby naming conventions</strike>
* <strike>optional parenthesis for method calls</strike>
* <strike>`__callStatic` delegates to `Module` instances</strike>

## hooks

* `coerce`
* `const_missing`
* `induced_from`
* `initialize_copy`
* `marshal_dump`
* `marshal_load`
* `method_added`
* `method_removed`
* `method_undefined`
* `singleton_method_added`
* `singleton_method_removed`
* `singleton_method_undefined`
* `to_xxx`
* <strike>`append_features`</strike>
* <strike>`extend_object`</strike>
* <strike>`extended`</strike>
* <strike>`included`</strike>
* <strike>`inherited`</strike>
* <strike>`initialized`</strike>
* <strike>`method_missing`</strike>
* <strike>`prepended`</strike>
* <strike>`prepend_features`</strike>

## core

#### BasicObject

* `__splat__('__super__', $args)`
* `__super__`
* `equal`
* `singleton_method_added`
* `singleton_method_removed`
* `singleton_method_undefined`
* <strike>`__id__`</strike>
* <strike>`__send__`</strike>
* <strike>`__splat__`</strike>
* <strike>`class`</strike>
* <strike>`initialize`</strike>
* <strike>`instance_eval`</strike>
* <strike>`instance_exec`</strike>
* <strike>`instance_variable_get`</strike>
* <strike>`instance_variable_set`</strike>
* <strike>`instance_variables`</strike>
* <strike>`method_missing`</strike>
* <strike>`singleton_class`</strike>

#### Kernel

* `caller`
* `super`
* <strike>`extend`</strike>
* <strike>`inspect`</strike>
* <strike>`is_a`</strike>
* <strike>`method`</strike>
* <strike>`methods`</strike>
* <strike>`object_id`</strike>
* <strike>`respond_to`</strike>
* <strike>`respond_to_missing`</strike>
* <strike>`send`</strike>
* <strike>`splat`</strike>
* <strike>`tap`</strike>
* <strike>`to_s`</strike>

#### Object

* <strike>`include Kernel`</strike>

#### Module

* anonymous classes
* `class_eval`
* `class_exec`
* `const_get`
* `const_missing`
* `const_set`
* `constants`
* `freeze`
* `method_added`
* `method_defined`
* `method_removed`
* `method_undefined`
* `module_eval`
* `module_exec`
* `module_function`
* `refine`
* `remove_method`
* `undef_method`
* `using`
* <strike>`alias_method`</strike>
* <strike>`alias_method_chain`</strike>
* <strike>`allocate`</strike>
* <strike>`ancestors`</strike>
* <strike>`append_features`</strike>
* <strike>`attr_accessor`</strike>
* <strike>`attr_reader`</strike>
* <strike>`attr_writer`</strike>
* <strike>`const_get`</strike>
* <strike>`define_method`</strike>
* <strike>`extend`</strike>
* <strike>`extend_object`</strike>
* <strike>`extended`</strike>
* <strike>`include`</strike>
* <strike>`included`</strike>
* <strike>`inherited`</strike>
* <strike>`initialized`</strike>
* <strike>`inspect`</strike>
* <strike>`instance_method`</strike>
* <strike>`instance_methods`</strike>
* <strike>`name`</strike>
* <strike>`new`</strike>
* <strike>`prepend`</strike>
* <strike>`prepend_features`</strike>
* <strike>`prepended`</strike>
* <strike>`to_s`</strike>

## stdlib

#### Base64

* <strike>`decode64`</strike>
* <strike>`encode64`</strike>