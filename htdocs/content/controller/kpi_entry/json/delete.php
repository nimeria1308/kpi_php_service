<?php

require_once('libraries/database.php');
require_once('libraries/json_service.php');
require_once('model/kpi.php');

use function Simona\KpiService\Database\connect;
use Simona\KpiService\JsonService;
use Simona\KpiService\Model\Kpi;

$js = new JsonService('delete');
$id = $js->get_item_id();
$kpi_id = $_GET['kpi_id'];

try {
    $db_handle = connect();
    try {
        $kpi = Kpi::getById($kpi_id, $db_handle);
        $entry = $kpi->getEntry($id, $db_handle);
        $entry->delete($db_handle);
        echo $js->respond_success();
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    echo $js->respond_error($e);
}
