<?php

namespace Phuby {
    $initializers = glob(__DIR__.DIRECTORY_SEPARATOR.'initializers'.DIRECTORY_SEPARATOR.'*');
    foreach ($initializers as $initializer) require $initializer;
}