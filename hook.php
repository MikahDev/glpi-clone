<?php
/**
 * Install hook
 *
 * @return boolean
 */

//require_once "inc/clone.class.php";

function plugin_cloneitems_install() {
    global $DB;

    //instanciate migration with version
    $migration = new Migration(100);

    //Create table only if it does not exists yet!
    if (!TableExists('glpi_plugin_myexample_configs')) {
        //table creation query
        $query = "CREATE TABLE `glpi_plugin_cloneitems_configs` (
                  `id` INT(11) NOT NULL AUTO_INCREMENT,
                  `name` VARCHAR(255) NOT NULL,
                  PRIMARY KEY  (`id`)
               ) ENGINE=innodb  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $DB->queryOrDie($query, $DB->error());
    }

    if (TableExists('glpi_plugin_cloneitems_configs')) {
        //missed value for configuration
        $migration->addField(
            'glpi_plugin_cloneitems_configs',
            'value',
            'string'
        );

        $migration->addKey(
            'glpi_plugin_cloneitems_configs',
            'name'
        );
    }

    //execute the whole migration
    $migration->executeMigration();

    return true;
}

/**
 * Uninstall hook
 *
 * @return boolean
 */
function plugin_cloneitems_uninstall() {
    global $DB;

    $tables = [
        'configs'
    ];

    foreach ($tables as $table) {
        $tablename = 'glpi_plugin_cloneitems_' . $table;
        //Create table only if it does not exists yet!
        if (TableExists($tablename)) {
            $DB->queryOrDie(
                "DROP TABLE `$tablename`",
                $DB->error()
            );
        }
    }

    return true;
}


function plugin_cloneitems_MassiveActions($type) {
    $actions = [];
    switch ($type) {
        case 'TicketTemplate' :
            return ['PluginCloneitemsClone'.MassiveAction::CLASS_ACTION_SEPARATOR.'CloneTicketTemplate' =>
                __("Duplicate Item", 'cloneitems')];

    }
    return $actions;
}