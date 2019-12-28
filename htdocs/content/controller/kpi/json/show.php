<?php

require_once('libraries/database.php');
require_once('libraries/json_service.php');
require_once('model/kpi.php');

use function Simona\KpiService\Database\connect;
use Simona\KpiService\JsonService;
use Simona\KpiService\Model\Kpi;

$js = new JsonService('show');

try {
    $id = $js->get_item_id();

    $db_handle = connect();
    try {
        $kpi = Kpi::getById($id, $db_handle);
        echo $js->respond_success($kpi);
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    echo $js->respond_error($e);
}
