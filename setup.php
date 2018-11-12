<?php

define('MYEXAMPLE_VERSION', '1.2.10');

/**
 * Init the hooks of the plugins - Needed
 *
 * @return void
 */
function plugin_init_myexample() {
    global $PLUGIN_HOOKS;

    //required!
    $PLUGIN_HOOKS['csrf_compliant']['myexample'] = true;

    //some code here, like call to Plugin::registerClass(), populating PLUGIN_HOOKS, ...
}

/**
 * Get the name and the version of the plugin - Needed
 *
 * @return array
 */
function plugin_version_myexample() {
    return [
        'name'           => 'Plugin name that will be displayed',
        'version'        => MYEXAMPLE_VERSION,
        'author'         => 'John Doe and <a href="http://foobar.com">Foo Bar</a>',
        'license'        => 'GLPv3',
        'homepage'       => 'http://perdu.com',
        'requirements'   => [
            'glpi'   => [
                'min' => '9.1'
            ]
        ]
    ];
}

/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 *
 * @return boolean
 */
function plugin_myexample_check_prerequisites() {
    //do what the checks you want
    return true;
}

/**
 * Check configuration process for plugin : need to return true if succeeded
 * Can display a message only if failure and $verbose is true
 *
 * @param boolean $verbose Enable verbosity. Default to false
 *
 * @return boolean
 */
function plugin_myexample_check_config($verbose = false) {
    if (true) { // Your configuration check
        return true;
    }

    if ($verbose) {
        echo "Installed, but not configured";
    }
    return false;
}
