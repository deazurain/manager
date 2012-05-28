<?php

class Myapp_Request extends Manager_Request {

	public function __construct() {
        parent::__construct();
		echo 'constructed myapp request';
	}

}

?>