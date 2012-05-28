<?php

class Path {

    private static $document_root;

    public static function boot() {
        self::$document_root = self::directory($_SERVER['DOCUMENT_ROOT']);
    }

    public static function directory($directory) {
        return realpath($directory).DIRECTORY_SEPARATOR;
    }

	private static function split_class_name($class_name) {
		$result = array();
		preg_match_all('/[A-Z][a-z]*/', $class_name, $matches);
		foreach($matches[0] as $k => $v) {
			$result[$k] = strtolower($v);
		}
		return $result;
	}

	public static function php($class_name) {

		$modules = Module::modules_ordered();

        $c = explode('_', $class_name);

        if(count($c) === 2) {
            // a module specific class
            $module_name = $c[0];
            $class_name = $c[1];

            $class_split = self::split_class_name($class_name);
            $class_path = join(DIRECTORY_SEPARATOR, $class_split);

            $module = Module::get($module_name);

            $path = $module->file('classes', $class_path).'.php';
            if(file_exists($path)) {
                return $path;
            }

        }

        // an 'alias' class
		$class_split = self::split_class_name($class_name);
		$class_path = join(DIRECTORY_SEPARATOR, $class_split);

		foreach($modules as $module) {
			$path = $module->file('include', $class_path).'.php';

			if(file_exists($path)) {
				return $path;
			}
		}

		return null;
	}

    private static function find($file_name, $extension) {

        $modules = Module::modules_ordered();

        foreach($modules as $module) {
            $path = $module->file($extension, $file_name).'.'.$extension;
            if(file_exists($path)) {
                return to_web_path($path);
            }
        }

        return null;
    }

    private static function to_web_path($path) {

        if(substr_compare($path, self::$document_root, 0) === 0) {
            return substr($path, strlen(self::$document_root));
        }

        return $path;
    }

    public static function js($file_name) {
        return self::find($file_name, 'js');
    }

    public static function css($file_name) {
        return self::find($file_name, 'js');
    }

}

?>
