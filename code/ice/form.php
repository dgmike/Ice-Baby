<?php
class Form
{
    var $debug = false;
    var $elements = array();

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
            'upload' => false,
        );
        $config = $args+$default_configs;
        extract($config, EXTR_SKIP);

        if (true===$debug) {
            $this->debug = true;
        }
        if ($this->debug) {
            print '[FORM INIT. Elements: 0]'.PHP_EOL;
        }
        if ($upload) {
            $method = 'post';
            $enctype = 'multipart/form-data';
        }
        foreach (array('action', 'method', 'class', 'extra', 'enctype') as $item) {
            $this->$item = $$item;
        }
    }

    public function show()
    {
        $open_form = array();
        $return = '%s';
        foreach (array('action', 'method', 'class', 'enctype') as $item) {
            if (!is_null($this->$item)) {
                $open_form[] = "$item=\"{$this->$item}\"";
            }
        }
        if (!is_null($this->extra)) {
            $open_form[] = $this->extra;
        }
        if ($open_form) {
            $return = '<form '.implode(' ', $open_form).'>%s</form>';
        }
        return sprintf($return, implode(PHP_EOL, $this->elements));
    }

    public function addElement($element)
    {
        $this->elements[] = $element;
        if ($this->debug) {
            print '[FORM ADDED. Elements: '.count($this->elements).']'.PHP_EOL;
        }
        return $element;
    }

    public function text($name = null, $value = null, $extra = null)
    {
        $text = '<input %s />';
        $attributes = array('type="text"');
        foreach (array('name', 'value') as $item) {
            if (!is_null($$item)) {
                $attributes[] = "{$item}=\"{$$item}\"";
            }
        }
        if (!is_null($extra)) {
            $attributes[] = $extra;
        }
        $text = sprintf($text, implode(' ', $attributes));
        return $this->addElement($text);
    }
}
