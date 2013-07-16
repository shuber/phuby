# phuby

### Features

* autoloading with ruby naming conventions e.g. `test_case/phuby/basic_object.php` for `TestCase\Phuby\BasicObject`
* mixins with support for `extend`, `include`, and `prepend`
* optional parenthesis for method calls
* everything inherits from `Phuby\Object` including classes
* familiar `Object` methods like `initialize`, `method_missing`, `send`, `super` and new ones like `splat`
* instance variables are private
* attr accessors, readers, writers

### Todo

* splat super
* Anonymous classes initialized with blocks
* Add phpunit tests
* attr reader, writer, accessor
* hooks
  x method_missing
  - method_added
  - singleton_method_added
  - method_removed
  - singleton_method_removed
  - method_undefined
  - singleton_method_undefined
  x initialized
  x inherited
  x append_features
  x prepend_features
  x included
  x prepended
  x extend_object
  x extended
  - initialize_copy
  - const_missing
  - marshal_dump
  - marshal_load
  - coerce
  - induced_from
  - to_xxx