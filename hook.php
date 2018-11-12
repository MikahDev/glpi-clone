<?php
/**
 * Install hook
 *
 * @return boolean
 */
function plugin_cloneitems_install() {
    global $DB;

    //instanciate migration with version
    $migration = new Migration(100);

    //Create table only if it does not exists yet!
    if (!TableExists('glpi_plugin_cloneitems_configs')) {
        //table creation query
        $query = "CREATE TABLE `glpi_plugin_cloneitems_config` (
                  `id` INT(11) NOT NULL autoincrement,
                  `name` VARCHAR(255) NOT NULL,
                  PRIMARY KEY  (`id`)
               ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
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
    //to some stuff, like removing tables, generated files, ...
    return true;
}
