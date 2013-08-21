# phuby

A port of [ruby](http://www.ruby-lang.org/) 2.0 to native [php](http://php.net/) 5.4+


## features

* runtime mixins with `include`, `extend`, `prepend` and their associated callbacks
* `Object` and `Kernel` methods including `super`, `send`, `respond_to?`, `method_missing`
* `Module` methods including `alias_method`, `define_method`, and `attr` accessors
* support for methods with special characters like `?` using this syntax `$this->{'empty?'}`
* instance variables are private and accessed with `$this->{'@name'}`
* class variables are supported as well `$this->{'@@name'}`
* even global variables are supported `$this->{'$redis'}`
* ported core library including `BasicObject`, `Kernel`, `Object`, `Module`, `Method`, `UnboundMethod`
* [incomplete] ported standard library including `Array`, `Hash`, `String`, `Enumerable`, `Comparable`
* autoloading with ruby `underscore` naming conventions
* ruby style namespace resolution
* optional parenthesis for method calls with no arguments
* everything is an object, including classes


## installation

If you're using `composer` simply require the `shuber-phuby` package.

Otherwise you can download the `phuby` tar/zip or `git clone` this
repository somewhere in your php include path.

Then inside of your php files you can `require 'phuby/phuby.php'` to load the library.


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

### 3) the `Phuby` function (like [jQuery](http://jquery.com/)'s `$` function)

This allows you to inject `Phuby` features into *any* object.

    echo Phuby('this is a sentence.')->upcase;

    $evens = Phuby([1,2,3,4,5])->select(function($number) {
      return $number % 2 == 0;
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
#### BasicObject
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
#### Marshal
#### MatchData
#### Math
#### Module
#### Numeric
#### Object
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

Usually I try to structure methods so that they have as few `return` statements or endpoints as possible (1 ideally). In this project I'm using guard style conditions at the beginning of methods and I'm starting to see the beauty of how readable and simple the source code reads. I'm using many `if` and `return` statements in favor of `else` and `if else`. `if` expressions are never written inline and the statements are separated by newlines. Brackets `{ }` are only added to `if` and `foreach` loops if necessary. It makes source code files longer but everything is condensed horizontally and naturally less than 80 characters most of the time. It makes me appreciate the enforced whitespace in python more.