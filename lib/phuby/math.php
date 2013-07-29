<?php

namespace Phuby;

class Math {
    function acos($x) {
        return acos($x);
    }

    function acosh($x) {
        return acosh($x);
    }

    function asin($x) {
        return asin($x);
    }

    function asinh($x) {
        return asinh($x);
    }

    function atan($x) {
        return atan($x);
    }

    function atan2($x, $y) {
        return atan2($x, $y);
    }

    function atanh($x) {
        return atanh($x);
    }

    function cos($x) {
        return cos($x);
    }

    function cosh($x) {
        return cosh($x);
    }

    function exp($x) {
        return exp($x);
    }

    function hypot($x, $y) {
        return hypot($x, $y);
    }

    function log($x) {
        return call_user_func_array('log', func_get_args());
    }

    function log10($x) {
        return log10($x);
    }

    function sin($x) {
        return sin($x);
    }

    function sinh($x) {
        return sinh($x);
    }

    function sqrt($x) {
        return sqrt($x);
    }

    function tan($x) {
        return tan($x);
    }

    function tanh($x) {
        return tanh($x);
    }
}