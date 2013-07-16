<?php

namespace Phuby;

class Module extends Object {
    private static $keywords = ['__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor'];

    static function initialized($self) {
        $self->__include(__CLASS__.'\Accessor');
    }

    protected $name;
    protected $methods = [];
    protected $includes = [];
    protected $prepends = [];
    protected $reflection;
    protected $superclass;

    function initialize($name, $superclass = null) {
        $this->name = $name;

        $instance = $this->name == __CLASS__ ? $this : $this->allocate();
        foreach ($this->reflection()->getMethods() as $method)
            if (!$method->isStatic())
                $this->define_method($method->getName(), $method->getClosure($instance));

        if (!$superclass) $superclass = get_parent_class($this->name);
        if ($superclass) {
            array_unshift($this->includes, $superclass);
            $this->superclass = BasicObject::const_get($superclass);
        }

        if (method_exists($this->name, 'initialized'))
            call_user_func("$this->name::initialized", $this);

        if ($superclass && method_exists($superclass, 'inherited'))
            call_user_func("$superclass::inherited", $this);
    }

    function __include($module) {
        foreach ($this->ancestors() as $ancestor)
            if ($ancestor->name() == $module)
                return $this;

        $this->append_features($module);

        if (method_exists($module, 'included'))
            call_user_func("$module::included", $this);

        return $this;
    }

    function __new() {
        $instance = $this->allocate();
        if ($method = $instance->method('initialize'))
            $method->splat(func_get_args());
        return $instance;
    }

    function allocate() {
        return new $this->name;
    }

    function ancestors(&$list = []) {
        foreach ($this->includes as $ancestor)
            BasicObject::const_get($ancestor)->ancestors($list);
        if (!in_array($this, $list))
            array_unshift($list, $this);
        foreach ($this->prepends as $ancestor)
            BasicObject::const_get($ancestor)->ancestors($list);
        return $list;
    }

    function append_features($module) {
        $this->includes[] = $module;
    }

    function define_method($method_name, $block) {
        $this->methods[$method_name] = new UnboundMethod($this->name, $method_name, $block);
        return $this->methods[$method_name];
    }

    function instance_method($method_name, $include_ancestors = true) {
        if ($include_ancestors) {
            foreach ($this->ancestors() as $ancestor)
                if ($method = $ancestor->instance_method($method_name, false))
                    return $method;
        } else if (isset($this->methods[$method_name])) {
            return $this->methods[$method_name];
        } else if (in_array($method_name, self::$keywords) && isset($this->methods["__$method_name"])) {
            return $this->methods["__$method_name"];
        }
    }

    function instance_methods($include_ancestors = true, $list = []) {
        if ($include_ancestors) {
            foreach ($includes as $module)
                BasicObject::const_get($module)->instance_methods(true, $list);
            foreach ($prepends as $module)
                BasicObject::const_get($module)->instance_methods(true, $list);
        }
        foreach (array_keys($this->methods) as $method_name)
            if (!in_array($method_name, $list))
                $list[] = $method_name;
        return $list;
    }

    function name() {
        return $this->name;
    }

    function prepend($module) {
        foreach ($this->ancestors() as $ancestor)
            if ($ancestor->name() == $module)
                return $this;

        $this->prepend_features($module);

        if (method_exists($module, 'prepended'))
            call_user_func("$module::prepended", $this);

        return $this;
    }

    function prepend_features($module) {
        array_unshift($this->prepends, $module);
    }

    function reflection() {
        if (!isset($this->reflection));
            $this->reflection = new \ReflectionClass($this->name);
        return $this->reflection;
    }

    function remove_method($method_name) {
        unset($this->methods[$method_name]);
        return $this;
    }

    function superclass() {
        return $this->superclass;
    }

    function to_s() {
        return $this->name;
    }

    // TODO: prepended modules will still respond to this method
    function undef_method($method_name) {
        $this->define_method($method_name, function() use ($method_name) {
            throw new \BadMethodCallException("Undefined method $method_name for ".$this->__class()->name());
        });
    }
}