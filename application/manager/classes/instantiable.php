<?php

class Manager_Instantiable {

	private static $_instance;

	protected static function instance() {
		if(!self::$_instance) {
			// use static for late binding so the 
			// descendent is instanciated
			$_instance = new static();
		}
		return $_instance;
	}

}

?>
