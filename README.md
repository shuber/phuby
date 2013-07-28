# phuby

A port of [ruby](http://www.ruby-lang.org/) 2.0 to native [php](http://php.net/) 5.4+

## features

* mixins with `include`, `extend`, `prepend` and their associated callbacks
* `Object` and `Kernel` methods including `super`, `send`, `respond_to?`, `method_missing`
* `Module` methods including `alias_method`, `define_method`, and `attr` accessors
* support for methods with special characters like `?` using this syntax `$this->{'empty?'}`
* instance variables are private and accessed with `$this->{'@name'}`
* class variables are supported as well `$this->{'@@name'}`
* even global variables are supported `$this->{'$redis'}`
* ported core library including `BasicObject`, `Kernel`, `Object`, `Module`, `Method`, `UnboundMethod`
* [incomplete] ported standard library including `Array`, `Hash`, `String`, `Enumerable`, `Comparable`
* [incomplete] ruby style namespace resolution thru the `Phuby` function
* autoloading with ruby `underscore` naming conventions
* parenthesis are optional for method calls with no arguments
* everything is an object, including classes

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
* `Phuby()` aliases e.g. Array => Arr
* ✓ all classes inherit `Object`
* ✓ classes may `use Phuby` instead of inheriting `Phuby\Object`
* ✓ autoloading with ruby naming conventions
* ✓ optional parenthesis for method calls
* ✓ `__callStatic` delegates to `Module` instances

### errors
* ✓ `ArgumentError`
* ✓ `Exception`
* ✓ `NameError`
* ✓ `NoMethodError`
* ✓ `RuntimeError`
* ✓ `StandardError`

### hooks
* `coerce`
* `const_missing`
* `marshal_dump`
* `marshal_load`
* `method_added`
* `method_removed`
* `method_undefined`
* `singleton_method_added`
* `singleton_method_removed`
* `singleton_method_undefined`
* `to_xxx`
* ✓ `append_features`
* ✓ `extend_object`
* ✓ `extended`
* ✓ `included`
* ✓ `inherited`
* ✓ `initialize_copy`
* ✓ `initialized`
* ✓ `method_missing`
* ✓ `prepended`
* ✓ `prepend_features`

### lib

#### Array
#### Base64
* ✓ `decode64`
* ✓ `encode64`

#### BasicObject
* `singleton_method_added`
* `singleton_method_removed`
* `singleton_method_undefined`
* ✓ `__caller__`
* ✓ `__id__`
* ✓ `__send__`
* ✓ `__splat__`
* ✓ `__undefined__`
* ✓ `class`
* ✓ `equal?`
* ✓ `initialize`
* ✓ `instance_eval`
* ✓ `instance_exec`
* ✓ `instance_variable_get`
* ✓ `instance_variable_set`
* ✓ `instance_variables`
* ✓ `method_missing`
* ✓ `singleton_class`
* ✓ `super`

#### Comparable
#### Date
#### Dir
#### Encoding
#### Enumerable
* `all?`
* `any?`
* `chunk`
* `collect`
* `collect_concat`
* `count`
* `cycle`
* `detect`
* `drop`
* `drop_while`
* `each_cons`
* `each_entry`
* `each_slice`
* `each_with_index`
* `each_with_object`
* `entries`
* `find`
* `find_all`
* `find_index`
* `first`
* `flat_map`
* `grep`
* `group_by`
* `include?`
* `inject`
* `lazy`
* `map`
* `max`
* `max_by`
* `member?`
* `min`
* `min_by`
* `minmax`
* `minmax_by`
* `none?`
* `one?`
* `partition`
* `reduce`
* `reject`
* `reverse_each`
* `select`
* `slice_before`
* `sort`
* `sort_by`
* `take`
* `take_while`
* `to_a`
* `zip`

#### Enumerator
#### File
#### Fixnum
#### Float
#### Hash
#### IO
#### Integer
#### Kernel
* ✓ `[]`
* ✓ `[]=`
* ✓ `caller`
* ✓ `dup`
* ✓ `extend`
* ✓ `inspect`
* ✓ `is_a?`
* ✓ `kind_of?`
* ✓ `method`
* ✓ `methods`
* ✓ `object_id`
* ✓ `respond_to?`
* ✓ `respond_to_missing?`
* ✓ `send`
* ✓ `splat`
* ✓ `tap`
* ✓ `to_s`

#### Marshal
* ✓ `dump`
* ✓ `load`
* ✓ `restore`

#### MatchData
* `==`
* `[]`
* `begin`
* `end`
* `eql?`
* `hash`
* `inspect`
* `length`
* `names`
* `offset`
* `post_match`
* `pre_match`
* `size`
* `to_a`
* `to_s`
* `values_at`
* ✓ `captures`
* ✓ `regexp`
* ✓ `string`

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
* ✓ `alias_method`
* ✓ `alias_method_chain`
* ✓ `allocate`
* ✓ `ancestors`
* ✓ `append_features`
* ✓ `attr_accessor`
* ✓ `attr_reader`
* ✓ `attr_writer`
* ✓ `class_eval`
* ✓ `class_exec`
* ✓ `const_get`
* ✓ `define_method`
* ✓ `extend`
* ✓ `extend_object`
* ✓ `extended`
* ✓ `include`
* ✓ `include?`
* ✓ `included`
* ✓ `inherited`
* ✓ `initialized`
* ✓ `inspect`
* ✓ `instance_method`
* ✓ `instance_methods`
* ✓ `method_defined?`
* ✓ `module_eval`
* ✓ `module_exec`
* ✓ `name`
* ✓ `new`
* ✓ `prepend`
* ✓ `prepend_features`
* ✓ `prepended`
* ✓ `superclass`
* ✓ `to_s`

#### Numeric
#### Object
* ✓ `include Kernel`

#### ObjectSpace
#### Proc
#### Random
#### Range
#### Regexp
#### RegexpError
#### StopIteration
#### String
* `%`
* `+`
* `<=>`
* `==`
* `=~`
* `[]`
* `[]=`
* `ascii_only?`
* `b`
* `bytes`
* `bytesize`
* `byteslice`
* `casecmp`
* `center`
* `chars`
* `chomp`
* `chomp!`
* `chop`
* `chop!`
* `chr`
* `codepoints`
* `count`
* `crypt`
* `delete`
* `delete!`
* `dump`
* `each_byte`
* `each_char`
* `each_codepoint`
* `each_line`
* `encode`
* `encode!`
* `encoding`
* `end_with?`
* `eql?`
* `force_encoding`
* `getbyte`
* `gsub`
* `gsub!`
* `hash`
* `hex`
* `include?`
* `index`
* `initialize_copy`
* `insert`
* `inspect`
* `intern`
* `length`
* `lines`
* `ljust`
* `match`
* `next`
* `next!`
* `oct`
* `ord`
* `partition`
* `rindex`
* `rjust`
* `rpartition`
* `scan`
* `scrub`
* `scrub!`
* `setbyte`
* `slice`
* `slice!`
* `split`
* `squeeze`
* `squeeze!`
* `start_with?`
* `sub`
* `sub!`
* `succ`
* `succ!`
* `sum`
* `swapcase`
* `swapcase!`
* `to_c`
* `to_f`
* `to_i`
* `to_r`
* `to_sym`
* `tr`
* `tr!`
* `tr_s!`
* `unpack`
* `upto`
* `valid_encoding?`
* ✓ `*`
* ✓ `<<`
* ✓ `capitalize`
* ✓ `capitalize!`
* ✓ `clear`
* ✓ `concat`
* ✓ `copy`
* ✓ `downcase`
* ✓ `downcase!`
* ✓ `empty?`
* ✓ `lstrip`
* ✓ `lstrip!`
* ✓ `prepend`
* ✓ `replace`
* ✓ `reverse`
* ✓ `reverse!`
* ✓ `rstrip`
* ✓ `rstrip!`
* ✓ `size`
* ✓ `strip`
* ✓ `strip!`
* ✓ `to_s`
* ✓ `to_str`
* ✓ `upcase`
* ✓ `upcase!`

#### Struct
#### Time

## notes

Usually I try to structure methods so that they have as few `return` statements or endpoints as possible (1 ideally). In this project I'm using guard style conditions at the beginning of methods and I'm starting to see the beauty of how readable and simple the source code reads. I'm using many `if` and `return` statements in favor of `else` and `if else`. `if` expressions are never written inline and the statements are separated by newlines. Brackets `{ }` are only added to `if` and `foreach` loops if necessary. It makes source code files longer but everything is condensed horizontally and naturally less than 80 characters most of the time. It makes me appreciate the enforced whitespace in python more.