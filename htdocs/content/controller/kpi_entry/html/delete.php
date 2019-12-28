<?php

require_once('libraries/database.php');
require_once('libraries/myview.php');
require_once('model/kpi.php');

use Simona\MyView;
use function Simona\KpiService\Database\connect;
use Simona\KpiService\Model\Kpi;

$id = $_GET['id'];
$kpi_id = $_GET['kpi_id'];
$header = "Location: /kpi/$kpi_id";

try {
    $db_handle = connect();
    try {
        $kpi = Kpi::getById($kpi_id, $db_handle);
        $entry = $kpi->getEntry($id, $db_handle);
        var_dump($entry);
        $entry->delete($db_handle);
        header($header);
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    header($header);
}
