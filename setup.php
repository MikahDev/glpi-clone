<?php

define('CLONEITEMS_VERSION', '0.0.1');

/**
 * Init the hooks of the plugins - Needed
 *
 * @return void
 */
function plugin_init_myexample() {
    global $PLUGIN_HOOKS;

    //required!
    $PLUGIN_HOOKS['csrf_compliant']['cloneitems'] = true;

    //some code here, like call to Plugin::registerClass(), populating PLUGIN_HOOKS, ...
}

/**
 * Get the name and the version of the plugin - Needed
 *
 * @return array
 */
function plugin_version_cloneitems() {
    return [
        'name'           => 'Clone Items',
        'version'        => CLONEITEMS_VERSION,
        'author'         => 'Michael Villeprat, <a href="https://www.mcv-fr.com">M.C.V</a>',
        'license'        => 'GLPv3',
        'homepage'       => 'https://www.mcv-fr.com',
        'requirements'   => [
            'glpi'   => [
                'min' => '9.3'
            ]
        ]
    ];
}

/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 *
 * @return boolean
 */
function plugin_cloneitems_check_prerequisites() {
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
function plugin_cloneitems_check_config($verbose = false) {
    if (true) { // Your configuration check
        return true;
    }

    if ($verbose) {
        echo "Installed, but not configured";
    }
    return false;
}
