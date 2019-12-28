<?php

declare(strict_types=1);

namespace Simona\KpiService;

class JsonService
{
    private $requestType;

    public function __construct($requestType)
    {
        $this->requestType = $requestType;
    }

    function get_item_id()
    {
        return intval($_GET['id']);
    }

    function get_item_payload()
    {
        return json_decode($_POST['payload']);
    }

    function respond_success($payload = null)
    {
        return json_encode([
            'type' => $this->requestType,
            'status' => 'ok',
            'payload' => $payload,
        ]);
    }

    function respond_error($error)
    {
        return json_encode([
            'type' => $this->requestType,
            'status' => 'error',
            // 'error' => $error->getMessage(),
            'error' => strval($error),
        ]);
    }
}
