<?php

namespace Phuby\Core {
    require ROOT.DS.'phuby'.DS.'core'.DS.'environment.php';

    spl_autoload_register(__CORE__.'Environment::autoload');
    Environment::append_include_path(ROOT);

    set_error_handler(__CORE__.'Environment::error_handler');
    Environment::append_error_handler(__CORE__.'ErrorHandler::undefined_constant_error_handler');
}