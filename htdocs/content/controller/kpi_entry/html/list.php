<?php

require_once('libraries/database.php');
require_once('libraries/myview.php');
require_once('model/kpi.php');

use Simona\MyView;
use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;

$kpi_id = $_GET['kpi_id'];

try {
    $db_handle = connect();
    try {
        $kpi = Kpi::getById($kpi_id, $db_handle);
        $entries = $kpi->getEntries($db_handle);

        $t = new MyView('header.phtml');
        $t->title = $kpi->getName();
        $t->render();

        $t = new MyView('kpi_entry.phtml');
        $t->kpi = $kpi;
        $t->entries = $entries;
        $t->render();
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    echo strval($e);
}

$t = new MyView('footer.phtml');
$t->render();
?>
