<?php

require_once('libraries/database.php');
require_once('libraries/myview.php');
require_once('model/kpi.php');

use Simona\MyView;
use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;

$kpi_id = $_GET['kpi_id'];

$header = "Location: /kpi/$kpi_id";

try {
    $db_handle = connect();
    try {
        $kpi = Kpi::getById($kpi_id, $db_handle);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $_POST['data'];
            $entry = $kpi->createEntry($data, $db_handle);
            header($header);
        } else {
            $t = new MyView('header.phtml');
            $t->title = "New entry for ".$kpi->getName();
            $t->render();

            $t = new MyView('kpi_entry_create.phtml');
            $t->kpi = $kpi;
            $t->render();

            $t = new MyView('footer.phtml');
            $t->render();
        }
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    header($header);
}
