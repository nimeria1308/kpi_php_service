<?php

require_once('libraries/database.php');
require_once('libraries/json_service.php');
require_once('model/kpi.php');

use function Simona\KpiService\Database\connect;
use Simona\KpiService\JsonService;
use Simona\KpiService\Model\Kpi;

$js = new JsonService('list');

try {
    $db_handle = connect();
    try {
        $kpis = Kpi::getKPIs($db_handle);
        echo $js->respond_success($kpis);
    } finally {
        $db_handle->close();
    }
} catch (Throwable $e) {
    echo $js->respond_error($e);
}
