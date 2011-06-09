<?php

namespace Phuby\Core {
    class ReflectionClass extends \ReflectionClass {

        const INSTANCE_METHOD = 'instance';
        const STATIC_METHOD = 'static';

        protected $_methods;
        protected $_parent;

        static protected $_instances = array();

        function __construct($class) {
            parent::__construct($class);
            $this->_parent = get_parent_class($class);
        }

        function getInstanceMethods() {
            return $this->getCachedMethods(self::INSTANCE_METHOD);
        }

        function getParentClass() {
            if ($this->_parent) return static::instance($this->_parent);
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
            } else if (!isset($this->_methods)) {
                $this->_methods = array(
                    self::INSTANCE_METHOD => array(),
                    self::STATIC_METHOD => array()
                );
                foreach ($this->getMethods() as $method) {
                    if ($method->getDeclaringClass()->name == $this->name) {
                        $type = $method->isStatic() ? self::STATIC_METHOD : self::INSTANCE_METHOD;
                        $this->_methods[$type][$method->name] = $method;
                    }
                }
            }
            return is_null($method_type) ? $this->_methods : $this->_methods[$method_type];
        }

        static function instance($class) {
            if (!isset(self::$_instances[$class])) self::$_instances[$class] = new static($class);
            return self::$_instances[$class];
        }

    }
}