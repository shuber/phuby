<?php

namespace Phuby\Core {
    class ReflectionClass extends \ReflectionClass {

        const INSTANCE_METHOD = 'instance';
        const STATIC_METHOD = 'static';

        protected $methods;
        protected $parent;

        static protected $instances = array();

        function __construct($class) {
            parent::__construct($class);
            $this->parent = get_parent_class($class);
        }

        function getInstanceMethods() {
            return $this->getCachedMethods(self::INSTANCE_METHOD);
        }

        function getParentClass() {
            if ($this->parent) return static::instance($this->parent);
        }

        function getStaticMethods() {
            return $this->getCachedMethods(self::STATIC_METHOD);
        }

        function lookupMethod($method, $method_type) {
            $methods = $this->getCachedMethods($method_type);
            return isset($methods[$method]) ? $methods[$method] : false;
        }

        protected function getCachedMethods($method_type = null) {
            if (!in_array($method_type, array(null, self::INSTANCE_METHOD, self::STATIC_METHOD))) {
                throw new \InvalidArgumentException('Invalid method type "'.$method_type.'"');
            } else if (!isset($this->methods)) {
                $this->methods = array(
                    self::INSTANCE_METHOD => array(),
                    self::STATIC_METHOD => array()
                );
                foreach ($this->getMethods() as $method) {
                    if ($method->getDeclaringClass()->name == $this->name) {
                        $type = $method->isStatic() ? self::STATIC_METHOD : self::INSTANCE_METHOD;
                        $this->methods[$type][$method->name] = $method;
                    }
                }
            }
            return is_null($method_type) ? $this->methods : $this->methods[$method_type];
        }

        static function instance($class) {
            if (!isset(self::$instances[$class])) self::$instances[$class] = new static($class);
            return self::$instances[$class];
        }

    }
}