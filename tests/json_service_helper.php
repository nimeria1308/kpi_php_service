<?php

const HOST = 'http://localhost:8080/';

const OPTS = [
    CURLOPT_RETURNTRANSFER => true,
    // CURLINFO_HEADER_OUT => true,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
    ],
    // CURLOPT_VERBOSE => true,
];

function send_request($path, $opts = null)
{
    $ch = curl_init(HOST . $path);
    try {
        curl_setopt_array($ch, OPTS);
        if ($opts) {
            curl_setopt_array($ch, $opts);
        }

        $result = curl_exec($ch);

        # Keep this here for debugging
        if (strlen($result) < 100) {
            var_dump($result);
        }

        # Convert result back from JSON
        $result = json_decode($result);

        # Make sure response was OK
        if ($result->status != 'ok') {
            throw new Exception($result->error);
        }
        return $result->payload;
    } finally {
        curl_close($ch);
    }
}

function list_resource($name, $query=null)
{
    return send_request("$name?$query");
}

function create_resource($name, $payload, $query=null)
{
    return send_request("$name?$query", [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ["payload" => json_encode($payload)],
    ]);
}

function show_resource($name, $id, $query=null)
{
    return send_request("$name/$id?$query");
}

function update_resource($name, $id, $payload, $query=null)
{
    # Unfortunatelly, there's no clean way to process REQUEST_BODY data for non-POST requests
    # So we will need to do this in a non-REST way.

    return send_request("$name/$id/edit?$query", [
        // CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ["payload" => json_encode($payload)],
    ]);
}

function delete_resource($name, $id, $query=null)
{
    return send_request("$name/$id?$query", [
        CURLOPT_CUSTOMREQUEST => "DELETE",
    ]);
}
