<?php

namespace Phuby;

trait Core {
    static function __callStatic($method_name, $args) {
        return Module::const_get(get_called_class())->splat($method_name, $args);
    }

    private $__phuby__ = [];

    function __construct() {
        $args = func_get_args();

        if (!empty($args))
            call_user_func_array([$this, 'initialize'], $args);
    }

    function __call($method_name, $args = []) {
        if ($method = $this->singleton_class()->instance_method($method_name))
            return $method->bind($this)->splat($args);

        if ($method = $this->singleton_class()->instance_method('method_missing'))
            return $method->bind($this)->call($method_name, $args);

        return $this->__undefined__($method_name);
    }

    function __caller__($ignore_methods = []) {
        $backtrace = debug_backtrace();
        $ignore_modules = [__CLASS__, __NAMESPACE__.'\Kernel', __NAMESPACE__.'\Method', __NAMESPACE__.'\Module\Alias'];

        foreach ($backtrace as $index => $trace)
            if (isset($trace['class']) && !in_array($trace['class'], $ignore_modules) && !in_array($trace['function'], $ignore_methods))
                return array_merge(['index' => $index], $trace);
    }

    function __class() {
        if (!$this->{'@__class__'})
            $this->{'@__class__'} = Module::const_get(get_class($this));

        return $this->{'@__class__'};
    }

    function __clone() {
        if ($this->singleton_class()->instance_method('initialize_copy'))
            $this->__call('initialize_copy', [$this->__caller__()['object']]);
    }

    function __extend__($module) {
        if ($module == 'self' && is_a($this, __NAMESPACE__.'\Module'))
            $module = $this->name();

        foreach ($this->singleton_class()->ancestors() as $ancestor)
            if ($ancestor->name() == $module)
                return $this;

        $this->singleton_class()->__include($module);

        if (is_a($this, __NAMESPACE__.'\Module')) {
            if (method_exists($module, 'extended'))
                call_user_func("$module::extended", $this);
        } else if (method_exists($module, 'extend_object')) {
            call_user_func("$module::extend_object", $this);
        }

        return $this;
    }

    function &__get($method_name) {
        $value = null;

        if (preg_match('/^[A-Z]/', $method_name)) {
            foreach ($this->singleton_class()->ancestors() as $module) {
                $namespaces = array_slice(explode('\\', $module->name()), 0, -1);

                if (!$namespaces[0])
                    unset($namespaces[0]);

                while (!empty($namespaces)) {
                    $namespace = implode('\\', $namespaces);
                    $class = $namespace.'\\'.$method_name;

                    if (class_exists($class)) {
                        $constant = Module::const_get($class);
                        return $constant;
                    }

                    array_pop($namespaces);
                }
            }
        }

        if (preg_match('/^@@(.+)/', $method_name, $matches)) {
            $module = is_a($this, __NAMESPACE__.'\Module') ? $this : $this->singleton_class();

            if ($this->__isset($method_name)) {
                foreach ($module->heritage() as $module)
                    if (isset($module->__phuby__[$method_name]))
                        $value = &$module->__phuby__[$method_name];
            } else {
                throw new NameError("uninitialized class variable '$method_name' for ".$module->name());
            }
        } else if (preg_match('/^@(.+)/', $method_name, $matches)) {
            if ($this->__isset($method_name))
                $value = &$this->__phuby__[$matches[1]];
        } else if (preg_match('/^\$(.+)/', $method_name, $matches)) {
            if ($this->__isset($method_name))
                $value = &$GLOBALS[$matches[1]];
        } else {
            $value = $this->__call($method_name);
        }

        return $value;
    }

    function __id__() {
        if (!$this->{'@__id__'})
            $this->{'@__id__'} = spl_object_hash($this);

        return $this->{'@__id__'};
    }

    function __isset($method_name) {
        if (preg_match('/^(@@|@|\$)(.+)/', $method_name, $matches))
            switch($matches[1]) {
                case '@': return isset($this->__phuby__[$matches[2]]);
                case '$': return isset($GLOBALS[$matches[2]]);
                case '@@':
                    $module = is_a($this, __NAMESPACE__.'\Module') ? $this : $this->singleton_class();

                    foreach ($module->heritage() as $module)
                        if (isset($module->__phuby__[$method_name]))
                            return true;
            }

        return false;
    }

    function __send__($method_name) {
        $args = array_slice(func_get_args(), 1);
        return $this->__call($method_name, $args);
    }

    function &__set($method_name, $args) {
        if (preg_match('/^@@(.+)/', $method_name, $matches)) {
            $module = is_a($this, __NAMESPACE__.'\Module') ? $this : $this->singleton_class();

            foreach (array_reverse($module->heritage()) as $module)
                if ($module->__isset($method_name))
                    break;

            $module->__phuby__[$method_name] = $args;
            $value = &$module->__phuby__[$method_name];
        } else if (preg_match('/^@(.+)/', $method_name, $matches)) {
            $this->__phuby__[$matches[1]] = $args;
            $value = &$this->__phuby__[$matches[1]];
        } else if (preg_match('/^\$(.+)/', $method_name, $matches)) {
            $GLOBALS[$matches[1]] = $args;
            $value = &$GLOBALS[$matches[1]];
        } else {
            $value = $this->__call("$method_name=", [$args]);
        }

        return $value;
    }

    function __toString() {
        return $this->__call('to_s');
    }

    function __undefined__($method_name) {
        throw new NoMethodError("undefined method '$method_name' for ".$this->__class()->name());
    }

    function __unset($method_name) {
        if (preg_match('/^(@@|@|\$)(.+)/', $method_name, $matches)) {
            switch($matches[1]) {
                case '@': unset($this->__phuby__[$matches[2]]);
                case '$': unset($GLOBALS[$matches[2]]);
                case '@@':
                    $module = is_a($this, __NAMESPACE__.'\Module') ? $this : $this->singleton_class();

                    foreach ($module->heritage() as $module)
                        if (isset($module->__phuby__[$method_name]))
                            unset($module->__phuby__[$method_name]);
            }
        }
    }

    function instance_eval($block) {
        $singleton = $this->singleton_class();
        return $block->bindTo($singleton, $singleton)->invoke();
    }

    function instance_exec($args, $block) {
        $args = array_slice(0, -1, func_get_args());
        $singleton = $this->singleton_class();

        return $block->bindTo($singleton, $singleton)->invokeArgs($args);
    }

    function singleton_class() {
        if (!$this->{'@__singleton_class__'})
            $this->{'@__singleton_class__'} = new Module(null, $this->__class()->name());

        return $this->{'@__singleton_class__'};
    }

    function super() {
        $args = func_get_args();

        if ($caller = $this->__caller__(['send', 'splat'])) {
            $module = $caller['class'];

            foreach ($this->singleton_class()->ancestors() as $ancestor)
                if ($ancestor->name() == $module) {
                    $found = true;
                } else if (isset($found) && $method = $ancestor->instance_method($caller['function'])) {
                    return $method->bind($this)->splat($args);
                }
        }

        return $this->__undefined__(__FUNCTION__, $args);
    }
}