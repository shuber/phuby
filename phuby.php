<?php

foreach (array('environment', 'phuby') as $file) require_once 'lib'.DIRECTORY_SEPARATOR.$file.'.php';

Environment::register_error_handler('Phuby::non_static_method_call_error_handler');
Environment::register_error_handler('Phuby::undefined_constant_error_handler');
set_error_handler('Environment::error_handler');

Environment::append_include_path(__DIR__.DIRECTORY_SEPARATOR.'lib');
spl_autoload_register('Phuby::autoload');