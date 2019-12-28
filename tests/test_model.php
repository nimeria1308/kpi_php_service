<?php

# Make sure all exceptions are thrown
# Define debug so that db_setup can set up a test database
const DEBUG = true;

$db_config = require('setup_database.php');

use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;
use Simona\KpiService\Model\KpiEntry;

# Connect to DBMS
$db_handle = connect(true, $config['database']);

try {
    # Create 4 KPI types
    Kpi::create("test number 1", Kpi::TYPE_NUMBER, $db_handle);
    Kpi::create("test number 2", Kpi::TYPE_NUMBER, $db_handle);
    Kpi::create("test string 1", Kpi::TYPE_STRING, $db_handle);
    Kpi::create("test string 2", Kpi::TYPE_STRING, $db_handle);

    # Get all current KPIs
    $kpis = Kpi::getKPIs($db_handle);
    var_dump($kpis);

    # Change name of `test number 2` to `test other number`
    $test_number_2 = Kpi::getById(2, $db_handle);
    $test_number_2->setName("test other number");
    $test_number_2->update($db_handle);

    # Check name has updated
    $kpis = Kpi::getKPIs($db_handle);
    var_dump($kpis);

    # Delete KPI `string 1`
    $test_string_1 = Kpi::getById(3, $db_handle);
    $test_string_1->delete($db_handle);
    $kpis = Kpi::getKPIs($db_handle);
    var_dump($kpis);

    # Add entries
    srand(0);
    $test_number_1 = Kpi::getById(1, $db_handle);

    for ($i = 0; $i < 20; $i++) {
        echo $test_number_1->createEntry(rand(0, 32), $db_handle)."\n";
    }

    $test_number_2 = Kpi::getById(2, $db_handle);
    echo $test_number_2->createEntry(13, $db_handle)."\n";

    $entries = $test_number_1->getEntries($db_handle);
    var_dump($entries);

    $test_string_2 = Kpi::getById(4, $db_handle);
    echo $test_string_2->createEntry("hello", $db_handle)."\n";
    echo $test_string_2->createEntry("world", $db_handle)."\n";

    $entries = $test_string_2->getEntries($db_handle);
    var_dump($entries);

    echo $test_string_1->getEntry(2, $db_handle)."\n";

    $e = $test_string_2->getEntry(1, $db_handle);
    $e->delete($db_handle);

    $entries = $test_string_2->getEntries($db_handle);
    var_dump($entries);

    echo $test_string_1->getEntry(2, $db_handle)."\n";

    # Going to remove test number 1 and all of its entries
    $test_number_1->delete($db_handle);

} finally {
    $db_handle->close();
}
