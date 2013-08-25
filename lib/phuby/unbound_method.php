<?php

namespace Phuby;

class UnboundMethod extends Object {
    static function initialized($self) {
        $self->attr_reader('name');
    }

    function initialize($owner, $name, $block) {
        $this->{'@owner'} = $owner;
        $this->{'@name'} = $name;
        $this->{'@block'} = $block;
    }

    function bind($receiver) {
        return new Method($this, $receiver);
    }

    function inspect() {
        return '#<'.get_called_class().': '.$this->{'@owner'}.'#'.$this->{'@name'}.'>';
    }

    function name() {
        return $this->{'@name'};
    }

    function owner() {
        return Module::const_get($this->{'@owner'});
    }

    function to_proc() {
        return $this->{'@block'};
    }
}