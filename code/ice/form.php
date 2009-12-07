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
            if (isset($$item) AND $$item !== null) {
                $this->$item = $$item;
            }
        }
    }

    public function show()
    {
        $open_form = array();
        $return = '%s';
        foreach (array('action', 'method', 'class', 'enctype') as $item) {
            if (isset($this->$item) AND $this->$item !== null) {
                $open_form[] = "$item=\"{$this->$item}\"";
            }
        }
        if (isset($this->extra) AND $this->extra !== null) {
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

    public function _attrs($args=null)
    {
        extract($args, EXTR_SKIP);
        $attributes = array();
        foreach (array('type', 'name', 'value', 'class', 'id') as $item) {
            if (isset($$item) AND $$item !== null) {
                $attributes[] = "{$item}=\"{$$item}\"";
            }
        }
        if (isset($extra) AND $extra !== null) {
            $attributes[] = $extra;
        }
        return implode(' ', $attributes);
    }

    public function _input($type, $args = null)
    {
        if (is_scalar($args)) {
            $args = array('name' => $args);
            if (func_num_args() > 2) {
                $args['value'] = func_get_arg(2);
            }
            if (func_num_args() > 3) {
                $args['extra'] = func_get_arg(3);
            }
        } else {
            $args = (array) $args;
        }
        $args = array('type' => $type) + $args;
        $input = '<input %s />';
        $attributes = call_user_func(array($this, '_attrs'), $args);
        return sprintf($input, $attributes);
    }

    public function text()
    {
        $args = func_get_args();
        array_unshift($args, 'text');
        $text = call_user_func_array(array($this, '_input'), $args);
        return $this->addElement($text);
    }
}
