<?php

namespace Phuby;

class Module extends Object {
    private static $constants = [];
    private static $keywords = [
        '__halt_compiler',
        'abstract', 'and', 'array', 'as',
        'break',
        'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue',
        'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends',
        'final', 'for', 'foreach', 'function',
        'global', 'goto',
        'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset',
        'list',
        'namespace', 'new',
        'or',
        'print', 'private', 'protected', 'public',
        'require', 'require_once', 'return',
        'static', 'switch',
        'throw', 'trait', 'try',
        'unset', 'use',
        'var',
        'while',
        'yield',
        'xor'
    ];

    static function initialized($self) {
        $self->__include(__CLASS__.'\Accessor');
        $self->__include(__CLASS__.'\Alias');
        $self->__include(__CLASS__.'\Reflection');
    }

    static function const_get($name) {
        if (isset(self::$constants[$name]))
            return self::$constants[$name];

        if (class_exists($name)) {
            self::$constants[$name] = new self;
            self::$constants[$name]->initialize($name);

            return self::$constants[$name];
        }

        throw new NameError("uninitialized constant $name");
    }

    function initialize($name = null, $superclass = null, $block = null) {
        if (!$name) {
            $class = 'Anonymous_'.uniqid();
            $namespace = __NAMESPACE__.'\Compiled';
            $name = "$namespace\\$class";

            $definition = "namespace $namespace { class $class extends \\".__NAMESPACE__.'\\Object { } }';

            eval($definition);
        }

        $this->{'@name'} = $name;
        $this->{'@includes'} = [];
        $this->{'@prepends'} = [];
        $this->{'@methods'} = [];
        $this->{'@reflection'} = new \ReflectionClass($name);

        $instance = $this->allocate();

        foreach ($this->{'@reflection'}->getMethods() as $method)
            if (!$method->isStatic())
                $this->define_method($method->getName(), $method->getClosure($instance));

        if ($superclass || $superclass = get_parent_class($name)) {
            array_unshift($this->{'@includes'}, $superclass);
            $this->{'@superclass'} = self::const_get($superclass);
        }

        if (method_exists($name, 'initialized'))
            call_user_func("$name::initialized", $this);

        if ($block)
            $block($this);

        if (class_exists("$name\ClassMethods"))
            $this->__extend__("$name\ClassMethods");

        if (class_exists("$name\InstanceMethods"))
            $this->__include("$name\InstanceMethods");

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

    function allocate() {
        return $this->{'@reflection'}->newInstanceWithoutConstructor();
    }

    function ancestors(&$list = []) {
        foreach ($this->{'@includes'} as $ancestor)
            self::const_get($ancestor)->ancestors($list);

        if (!in_array($this, $list))
            array_unshift($list, $this);

        foreach ($this->{'@prepends'} as $ancestor)
            self::const_get($ancestor)->ancestors($list);

        return $list;
    }

    function append_features($module) {
        $this->{'@includes'}[] = $module;
    }

    function define_method($method_name, $block) {
        return $this->{'@methods'}[$method_name] = new UnboundMethod($this->name(), $method_name, $block);
    }

    function heritage() {
        $module = $this;
        $heritage = [$module];

        while ($superclass = $module->{'@superclass'}) {
            $module = $superclass;
            $heritage[] = $module;
        }

        return $heritage;
    }

    function instance_method($method_name, $include_ancestors = true) {
        if ($include_ancestors) {
            foreach ($this->ancestors() as $ancestor)
                if ($method = $ancestor->instance_method($method_name, false))
                    return $method;
        } else if (isset($this->{'@methods'}[$method_name])) {
            return $this->{'@methods'}[$method_name];
        } else if (in_array($method_name, self::$keywords) && isset($this->{'@methods'}["__$method_name"])) {
            return $this->{'@methods'}["__$method_name"];
        }
    }

    function name() {
        return $this->{'@name'};
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
        array_unshift($this->{'@prepends'}, $module);
    }
}