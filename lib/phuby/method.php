<?php

namespace Phuby;

class Method extends Object {
    static function initialized($self) {
        $self->attr_reader('receiver');
    }

    function initialize($unbound, $receiver) {
        $this->{'@unbound'} = $unbound;
        $this->{'@receiver'} = $receiver;
    }

    function call($args = null) {
        return call_user_func_array($this->to_proc(), func_get_args());
    }

    function inspect() {
        return '<'.get_called_class().': '.get_class($this->{'@receiver'}).'#'.$this->{'@unbound'}->name().'>';
    }

    function splat($args) {
        return call_user_func_array([$this, 'call'], $args);
    }

    function to_proc() {
        return $this->{'@unbound'}->to_proc()->bindTo($this->{'@receiver'});
    }

    function unbind() {
        return $this->{'@unbound'};
    }
}