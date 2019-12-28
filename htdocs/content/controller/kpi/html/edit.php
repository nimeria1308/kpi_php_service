<?php

require_once('libraries/database.php');
require_once('libraries/myview.php');
require_once('model/kpi.php');

use Simona\MyView;
use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;

const REDIRECT_HEADER = "Location: /kpis";

$id = $_GET['id'];

try {
    $db_handle = connect();
    try {
        $kpi = Kpi::getById($id, $db_handle);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            if ($name and ($kpi->getName() != $name)) {
                $kpi->setName($name);
                var_dump($kpi->getName());
                $kpi->update($db_handle);
            }
            header(REDIRECT_HEADER);
        } else {
            $t = new MyView('header.phtml');
            $t->title = "Rename KPI";
            $t->render();

            $t = new MyView('kpi_rename.phtml');
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
