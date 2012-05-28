<?php

class Manager_Part {

    private $_environment;

    public function __construct() {
        $this->_environment = array();
    }

    /**
     * This function accepts a key and value or an array of key => value pairs
     * and enriches the views' environment with it.
     * @param $key
     * @param null $value
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function set($key, $value = NULL) {

        if(is_array($key)) {
            foreach($key as $k => $v) {
                $this->_environment[$k] = $v;
            }
        }
        else {
            $this->_environment[$key] = $value;
        }

    }

    /*
     * hoe moet het renderen werken?
     *
     * Controller ->
     * Part('template')
     *  ->set('content', Part('page/main'))
     *
     */
}