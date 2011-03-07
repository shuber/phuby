<?php

namespace Method {
    class Splat {

        function method_missing($method, $arguments) {
            if (preg_match('#^(.+)_array$#', $method, $matches) && $this->respond_to($matches[1])) {
                $response = $this->send_array($matches[1], array_shift($arguments));
            } else {
                $response = $this->super($method, $arguments);
            }
            return $response;
        }

    }
}