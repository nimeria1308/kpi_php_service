<?php

namespace Simona\KpiService\Model;

require_once('sql_entity.php');
require_once('kpi_entry.php');

use DateTime;
use InvalidArgumentException;
use JsonSerializable;

class Kpi extends SqlEntity implements JsonSerializable
{
    const TYPE_NUMBER = "number";
    const TYPE_STRING = "string";
    const TYPES = [
        Kpi::TYPE_NUMBER,
        Kpi::TYPE_STRING,
    ];

    protected $id;
    protected $name;
    protected $type;

    protected function __construct($name, $type, $id = null)
    {
        if ($name == null or $type == null) {
            throw new InvalidArgumentException("name or type not provided");
        }

        if (!in_array($type, Kpi::TYPES)) {
            throw new InvalidArgumentException("Unknown type " . $type);
        }

        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }

    # ID
    public function getId()
    {
        return $this->id;
    }

    # Type
    public function getType()
    {
        return $this->type;
    }

    # Name
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return sprintf("Kpi<%d:%s:%s>", $this->id, $this->name, $this->type);
    }

    # We need this as fields are not public
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
        ];
    }

    protected function entryFromResult($result)
    {
        $entry = null;

        $data = $result['data'];
        $id = intval($result['id']);
        $timestamp = DateTime::createFromFormat("Y-m-d H:i:s", $result['timestamp']);
        $entry = new KpiEntry($this, $data, $id, $timestamp);

        return $entry;
    }

    # entries
    public function getEntries($db_handle)
    {
        $func = function ($value) {
            return $this->entryfromResult($value);
        };

        $query = sprintf("Select * FROM `kpi_%s_entries` WHERE `kpi_id` = %s", $this->type, $this->id);
        $result = $db_handle->query($query);
        try {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            return array_map($func, $rows);
        } finally {
            $result->close();
        }
    }

    public function getEntriesCount($db_handle) {
        $query = sprintf("Select COUNT(*) FROM `kpi_%s_entries` WHERE `kpi_id` = %s", $this->type, $this->id);
        $result = $db_handle->query($query);
        try {
            $rows = $result->fetch_row();
            return intval($rows[0]);
        } finally {
            $result->close();
        }
    }

    public function getEntry($id, $db_handle)
    {
        $query = sprintf("Select * FROM `kpi_%s_entries` WHERE `id` = $id", $this->type);
        $result = $db_handle->query($query);
        try {
            $entry = $result->fetch_assoc();
            if (!$entry) {
                throw new InvalidArgumentException("Could not find entry with id $id");
            }
            return $this->entryFromResult($entry);
        } finally {
            $result->close();
        }
    }

    public function createEntry($data, $db_handle)
    {
        $res = new KpiEntry($this, $data);
        $res->insert($db_handle);
        return $res;
    }

    protected function insert($db_handle)
    {
        $name = $db_handle->real_escape_string($this->name);
        $type = $this->type;

        $db_handle->query(
            "INSERT INTO `kpis` (`name`, `type`) VALUES ('$name', '$type')"
        );

        $this->id = $db_handle->insert_id;
    }

    public function update($db_handle)
    {
        $name = $db_handle->real_escape_string($this->name);
        $db_handle->query(
            "UPDATE `kpis` SET `name` = '$name' WHERE `id` = $this->id"
        );
    }

    public function delete($db_handle)
    {
        # first remove all KpiEntries, or the next one will fail
        # because of the foreign key constraint

        $db_handle->query(
            sprintf(
                "DELETE FROM `kpi_%s_entries` WHERE `kpi_id` = $this->id",
                $this->type
            )
        );

        $db_handle->query(
            "DELETE FROM `kpis` WHERE `id` = $this->id"
        );
    }

    public static function create($name, $type, $db_handle)
    {
        $res = new Kpi($name, $type);
        $res->insert($db_handle);
        return $res;
    }

    protected static function fromResult($res)
    {
        return new Kpi($res['name'], $res['type'], intval($res['id']));
    }

    protected static function getResultById($id, $db_handle)
    {
        $result = $db_handle->query("Select * FROM `kpis` WHERE `id` = $id");
        try {
            return $result->fetch_assoc();
        } finally {
            $result->close();
        }
    }

    public static function getById($id, $db_handle)
    {
        if (!$id) {
            throw new InvalidArgumentException("Id $id is not valid");
        }

        $result = Kpi::getResultById($id, $db_handle);
        if (!$result) {
            throw new InvalidArgumentException("Could not find KPI with id $id");
        }
        return Kpi::fromResult($result);
    }

    public static function getKPIs($db_handle)
    {
        $func = function ($value) {
            return Kpi::fromResult($value);
        };

        $result = $db_handle->query("Select * FROM `kpis`");
        try {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            return array_map($func, $rows);
        } finally {
            $result->close();
        }
    }

    # table creation
    private const CREATE_SQL = <<< EOT

    CREATE TABLE `kpis` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `type` VARCHAR(16) NOT NULL CHECK(type in ('number', 'string')),

        PRIMARY KEY (`id`)
    )
EOT;

    public static function createTables($db_handle)
    {
        $db_handle->query(Kpi::CREATE_SQL);
    }
}
