<?php

class Form
{
    private $debug = false;

    function __construct($args=false)
    {
        if (!in_array(gettype($args), array('array', 'object'))) {
            $args = array('debug' => (bool) $args);
        }
        $default_configs = array(
            'debug' => false,
        );
        $config = $args+$default_configs;
        extract($config, EXTR_SKIP);

        if (true===$debug) {
            $this->debug = true;
        }
        if ($this->debug) {
            print '[FORM INIT. Elements: 0]';
        }
    }
}
