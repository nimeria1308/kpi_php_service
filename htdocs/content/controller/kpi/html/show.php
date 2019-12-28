<?php

require_once('libraries/database.php');
require_once('libraries/myview.php');
require_once('model/kpi.php');

use Simona\MyView;
use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;

$id = $_GET['id'];

try {
    $db_handle = connect();
    try {
        $kpi = Kpi::getById($id, $db_handle);
        $entries = $kpi->getEntries($db_handle);

        $t = new MyView('header.phtml');
        $t->title = $kpi->getName();
        $t->render();

        $entries_view = new MyView('kpi_entry.phtml');
        $entries_view->kpi = $kpi;
        $entries_view->entries = $entries;

        $t = new MyView('kpi_show.phtml');
        $t->entries_view = $entries_view;

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
