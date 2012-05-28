<?php

class Module {

    private static $_modules;
    private static $_modules_ordered;

    /**
     * This function loads the modules from the module directory
     * parameter and creates the load order.
     *
     * @static
     * @param $module_directory
     */
    public static function boot($module_directory) {

        // get all modules in the module directory
        $module_directories = glob($module_directory . '*', GLOB_ONLYDIR);

        foreach ($module_directories as $d) {
            self::load($d);
        }

        self::order_modules();

    }

    /**
     * This function attempts to create and add a module. The
     * load order needs to be recreated after using this
     * function manually.
     *
     * @static
     * @param $directory
     */
    public static function load($directory) {

        $name = basename(realpath($directory));

        self::add(new Module($name, $directory));

    }

    /**
     * This function simply adds a module so that it can be
     * requested through the get() method.
     *
     * @static
     * @param $module
     */
    public static function add(Module $module) {

        self::$_modules[$module->name()] = $module;

    }

    /**
     * This function returns a module if it was loaded. It is
     * case-insensitive.
     *
     * @static
     * @param $module_name
     * @return Module the module if it is found, null otherwise.
     */
    public static function get($module_name) {

        $module_name = strtolower(trim($module_name));

        if (isset(self::$_modules[$module_name])) {
            return self::$_modules[$module_name];
        }

        return null;

    }

    /**
     * This function creates the module load order so that the
     * Path class visits the modules in the right order.
     *
     * @static
     * @return array
     * @throws Exception
     */
    public static function order_modules() {

        $modules = self::$_modules;

        // reversed load order
        // modulename => moduleinstance
        $reversed = array();

        while (count($modules) > 0) {

            $old_length = count($modules);

            foreach ($modules as $mkey => $m) {

                $can_load = true;
                $extensionees = $m->extensionees();
                foreach ($extensionees as $e) {
                    // if the to be extended module is not yet 'loaded'
                    // aka still present in $modules
                    if (isset($modules[$e])) {
                        $can_load = false;
                        break;
                    }
                }

                if ($can_load) {
                    unset($modules[$mkey]);
                    $reversed[] = $m;
                }

            }

            // if no progress has been made
            if ($old_length === count($modules)) {
                // then we have found a circular dependency
                throw new Exception("Circular Dependency detected: " . print_r($modules, true));
            }

        }

        // reverse the loading order
        self::$_modules_ordered = array_reverse($reversed);

    }

    public static function modules_ordered() {
        return self::$_modules_ordered;
    }

    // NON STATIC

    private $_name;
    private $_directory;
    private $_properties;

    public function __construct($name, $directory, array $properties = NULL) {

        $this->_name = strtolower(trim($name));
        $this->_directory = realpath($directory) . DIRECTORY_SEPARATOR;

        if ($properties) {
            $this->_properties = $properties;
        }
        else {
            $this->_properties = $this->load_properties();
        }

    }

    public function name() {
        return $this->_name;
    }

    public function directory($subdirectory = NULL) {
        if($subdirectory) {
            return realpath($this->_directory.$subdirectory).DIRECTORY_SEPARATOR;
        }
        return $this->_directory;
    }

    public function file($a, $b = NULL) {
        if($b) {
            return $this->directory($a).$b;
        }
        return $this->_directory.$a;
    }

    public function property($property_name) {
        return isset($this->_properties[$property_name]) ? $this->_properties[$property_name] : null;
    }

    public function extensionees() {
        $d = $this->property('extends');
        return $d ? $d : array();
    }

    private function load_properties() {
        return require $this->file('properties.php');
    }

}
