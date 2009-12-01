<?php

class Form
{
    private $debug = false;

    public function __construct($args=false)
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
        foreach (array('action', 'method') as $item) {
            if (isset($$item)) {
                $this->$item = $$item;
            }
        }
    }

    public function show()
    {
        $open_form = array();
        if (!is_null($this->action)) {
            $open_form[] = "action=\"{$this->action}\"";
        }
        if (!is_null($this->method)) {
            $open_form[] = "method=\"{$this->method}\"";
        }
        if ($open_form) {
            return '<form '.implode(' ', $open_form).'></form>';
        }
    }
}
