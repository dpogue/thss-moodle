<?php

function xmldb_theme_thss_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.
    if ($result && $oldversion < 2010011601) {

    /// Define field id to be added to theme_thss_menu
        $table = new xmldb_table('theme_thss_menu');
        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);

    /// Conditionally launch add field id
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// thss savepoint reached
        upgrade_plugin_savepoint($result, 2010011601, 'theme', 'thss');
    }
}
