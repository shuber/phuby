<?php

namespace Phuby {
    $initializers = glob(__DIR__.DIRECTORY_SEPARATOR.'initializers'.DIRECTORY_SEPARATOR.'*');
    if ($initializers) foreach ($initializers as $initializer) require $initializer;
}