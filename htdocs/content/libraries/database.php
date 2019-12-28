<?php

namespace Simona\KpiService\Database;

use Throwable;

function connect($select_db = true, $config = null)
{
    if (!$config) {
        $config = require('config.php');
        $config = $config['database'];
    }

    $host = $config['host'];
    $username = $config['username'];
    $password = $config['password'];
    $name = $config['name'];

    $handle = new \mysqli($host, $username, $password);

    try {
        # activate reporting
        # this also makes sure queries throw exceptions
        $driver = new \mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_STRICT;

        if ($select_db) {
            $handle->select_db($name);
        }
    } catch (Throwable $e) {
        $handle->close();
        throw $e;
    }

    return $handle;
}
