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
            'debug'  => false,
            'action' => null,
            'method' => null,
            'class'  => null,
            'extra'  => null,
        );
        $config = $args+$default_configs;
        extract($config, EXTR_SKIP);

        if (true===$debug) {
            $this->debug = true;
        }
        if ($this->debug) {
            print '[FORM INIT. Elements: 0]';
        }
        foreach (array('action', 'method', 'class', 'extra') as $item) {
            $this->$item = $$item;
        }
    }

    public function show()
    {
        $open_form = array();
        foreach (array('action', 'method', 'class') as $item) {
            if (!is_null($this->$item)) {
                $open_form[] = "$item=\"{$this->$item}\"";
            }
        }
        if (!is_null($this->extra)) {
            $open_form[] = $this->extra;
        }
        if ($open_form) {
            return '<form '.implode(' ', $open_form).'></form>';
        }
    }
}
