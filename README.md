# phuby

## usage

You can integrate `Phuby` with your code in 3 ways:

### 1) class inheritance

    class Blog extends Phuby\Object { }

### 2) traits

This is useful when your class needs to inherit from an existing library and
can't extend `Phuby\Object`.

    class Blog extends ActiveRecord\Base {
        use Phuby;
    }

### 3) the `Phuby` function

This allows you to inject `Phuby` features into *any* object.

    echo Phuby('this is a sentence.')->upcase;

    Phuby([1,2,3])->each(function($number) {
      echo $number;
    });

    echo Phuby(7)->days->from_now;

* `string` becomes `Phuby\String`
* `string` becomes `Phuby\Module` if it is a class name
* `array` becomes `Phuby\Hash`
* `array` becomes `Phuby\Array` if it has all numeric keys
* `number` becomes some type of `Phuby\Numeric` (float or int)
* `object` becomes a `Phuby\Proxy` which allows us to integrate `Phuby` into specific object instances


## todo

### general
* phpunit tests
* catch errors and raise them as exceptions
* Ruby conventions for method visibility
* `Phuby()` namespace resolution
* <strike>all classes inherit `Object`</strike>
* <strike>classes may `use Phuby` instead of inheriting `Phuby\Object`</strike>
* <strike>autoloading with ruby naming conventions</strike>
* <strike>optional parenthesis for method calls</strike>
* <strike>`__callStatic` delegates to `Module` instances</strike>

### errors
* <strike>`ArgumentError`</strike>
* <strike>`Exception`</strike>
* <strike>`NameError`</strike>
* <strike>`NoMethodError`</strike>
* <strike>`RuntimeError`</strike>
* <strike>`StandardError`</strike>

### hooks
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

### lib

#### Array
#### Base64
* <strike>`decode64`</strike>
* <strike>`encode64`</strike>

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

#### Comparable
#### Date
#### Dir
#### Encoding
#### Enumerable
#### Enumerator
#### File
#### Fixnum
#### Float
#### Hash
#### IO
#### Integer
#### Kernel
* `caller`
* `super`
* <strike>`[]`</strike>
* <strike>`[]=`</strike>
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

#### Marshal
* <strike>`dump`</strike>
* <strike>`load`</strike>
* <strike>`restore`</strike>

#### MatchData
* ==
* []
* begin
* end
* eql?
* hash
* inspect
* length
* names
* offset
* post_match
* pre_match
* size
* to_a
* to_s
* values_at
* <strike>captures</strike>
* <strike>regexp</strike>
* <strike>string</strike>

#### Math
#### Module
* anonymous classes
* `const_missing`
* `const_set`
* `constants`
* `freeze`
* `method_added`
* `method_removed`
* `method_undefined`
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
* <strike>`class_eval`</strike>
* <strike>`class_exec`</strike>
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
* <strike>`method_defined`</strike>
* <strike>`module_eval`</strike>
* <strike>`module_exec`</strike>
* <strike>`name`</strike>
* <strike>`new`</strike>
* <strike>`prepend`</strike>
* <strike>`prepend_features`</strike>
* <strike>`prepended`</strike>
* <strike>`superclass`</strike>
* <strike>`to_s`</strike>

#### Numeric
#### Object
* <strike>`include Kernel`</strike>

#### ObjectSpace
#### Proc
#### Random
#### Range
#### Regexp
#### RegexpError
#### StopIteration
#### String
#### Struct
#### Time

## notes

I'm trying a new method coding style in this project. Usually I try to structure methods so that they have as few `return` statements or endpoints as possible (usually 1). This time I'm using guard style conditions at the beginning of methods and I'm starting to see the beauty of how readable and simple the source code reads. I'm using many `if` and `return` statements in favor of `else` and `if else`. `if` expressions are never written inline and the statements are separated by newlines. Bracket `{ }` are only added to `if` and `foreach` loops if necessary. It makes source code files longer but everything is condensed horizontally and naturally less than 80 characters most of the time. It makes me appreciate the python source code white spacing more.