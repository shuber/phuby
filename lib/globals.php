<?php

$Object = Phuby('Phuby\Object');

$Object->{'$0'} = $_SERVER['SCRIPT_NAME'];
$Object->{'$*'} = array_slice($_SERVER['argv'], 1);
$Object->{'$$'} = getmypid();