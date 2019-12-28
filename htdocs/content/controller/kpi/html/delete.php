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
        $kpi->delete($db_handle);
        header(REDIRECT_HEADER);
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    header(REDIRECT_HEADER);
}
