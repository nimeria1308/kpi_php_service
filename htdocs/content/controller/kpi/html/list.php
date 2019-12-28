<?php

require_once('libraries/database.php');
require_once('libraries/myview.php');
require_once('model/kpi.php');

use Simona\MyView;
use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;

$t = new MyView('header.phtml');
$t->title = "KPIs";
$t->render();

try {
    $db_handle = connect();
    try {
        $kpis = Kpi::getKPIs($db_handle);
        foreach ($kpis as $kpi) {
            $kpi->count = $kpi->getEntriesCount($db_handle);
        }

        $t = new MyView('kpi.phtml');
        $t->kpis = $kpis;
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
