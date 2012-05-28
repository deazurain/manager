<?php

class Manager {

	static $_directory;

	public static function boot($manager_directory, $module_directory) {
		// normalize directories
		$manager_directory = Path::directory($manager_directory);
		$module_directory = Path::directory($module_directory);

		self::$_directory = $manager_directory;

		// load modules
		Module::boot($module_directory);

        // initialize path class
        Path::boot();

        // register class loader
        spl_autoload_register("Manager::class_loader", true);
	}

    public static function class_loader($class_name) {
        $path = Path::php($class_name);

        if(!$path) {
            throw new Exception("Can't load '$class_name'.");
        }

        require $path;
    }

}