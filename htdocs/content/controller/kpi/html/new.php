<?php

require_once('libraries/database.php');
require_once('libraries/myview.php');
require_once('model/kpi.php');

use Simona\MyView;
use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;

const REDIRECT_HEADER = "Location: /kpis";

try {
    $db_handle = connect();
    try {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $type = $_POST['type'];
            $name = $_POST['name'];
            if ($name) {
                Kpi::create($name, $type, $db_handle);
            }
            header(REDIRECT_HEADER);
        } else {
            $t = new MyView('header.phtml');
            $t->title = "New KPI";
            $t->render();

            $t = new MyView('kpi_create.phtml');
            $t->kpi = $kpi;
            $t->render();

            $t = new MyView('footer.phtml');
            $t->render();
        }
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    header(REDIRECT_HEADER);
}
