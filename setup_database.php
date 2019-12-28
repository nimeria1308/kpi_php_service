<?php

$debug = defined('DEBUG');

const CONTENT_DIR = "htdocs/content/";
const LIB_DIR = CONTENT_DIR . "libraries/";
const MODEL_DIR = CONTENT_DIR . "model/";

require_once(LIB_DIR . "database.php");
require_once(MODEL_DIR . "kpi.php");
require_once(MODEL_DIR . "kpi_entry.php");

use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;
use Simona\KpiService\Model\KpiEntry;

$config = require(CONTENT_DIR . 'config.php');

if ($debug) {
    $config['database']['name'] .= "_test";
}

$db_name = $config['database']['name'];

# Connect to DBMS
$db_handle = connect(false, $config['database']);

try {
    // always remove DB for now
    // if ($debug) {
        # when debugging we would like to remove the test database
        $db_handle->query("DROP DATABASE IF EXISTS $db_name");
    // }

    # Check if exists
    $db_exists = $db_handle->select_db($db_name);

    if (!$db_exists) {
        echo "Creating db\n";

        # Create the database
        $db_handle->query("CREATE DATABASE $db_name");
        $db_handle->select_db($db_name);

        # Create table(s) for the Kpi model
        Kpi::createTables($db_handle);

        # Create table(s) for the KpiEntry model
        KpiEntry::createTables($db_handle);

        echo "Database `$db_name` created successfully\n";
    }
} finally {
    $db_handle->close();
}

return $config['database'];
