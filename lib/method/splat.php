<?php

namespace Method {
    class Splat {

        function method_missing($method, $arguments) {
            if ($splat_method = $this->splat_method($method)) {
                $response = $this->send_array($splat_method, array_shift($arguments));
            } else {
                $response = $this->super($method, $arguments);
            }
            return $response;
        }

        function respond_to_missing($method) {
            return !!$this->splat_method($method) || $this->super($method);
        }

        function splat_method($method) {
            return preg_match('#^(.+)_array$#', $method, $matches) && $this->respond_to($matches[1]) ? $matches[1] : false;
        }

    }
}