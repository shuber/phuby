<?php

namespace Phuby {
    if ($initializers = glob(__DIR__.DIRECTORY_SEPARATOR.'initializers'.DIRECTORY_SEPARATOR.'*')) {
        sort($initializers);
        foreach ($initializers as $initializer) require $initializer;
    }
}