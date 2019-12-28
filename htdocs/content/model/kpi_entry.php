<?php

namespace Simona\KpiService\Model;

require_once('sql_entity.php');

use DateTime;
use Exception;
use InvalidArgumentException;
use JsonSerializable;

class KpiEntry extends SqlEntity implements JsonSerializable
{
    protected $kpi;
    protected $id;
    protected $timestamp;
    protected $data;

    public function __construct($kpi, $data, $id = null, $timestamp = null)
    {
        if (!$kpi or !$data) {
            throw new InvalidArgumentException("kpi or data not provided");
        }

        $this->kpi = $kpi;
        $this->id = $id;
        $this->timestamp = $timestamp;
        $this->data = $data;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getKpi()
    {
        return $this->kpi;
    }

    public function __toString()
    {
        return sprintf(
            "KpiEntry<%d:%s:%s:%s>",
            $this->id,
            $this->kpi,
            $this->timestamp ? $this->timestamp->format(DateTime::W3C) : "-",
            $this->data
        );
    }

    # We need this as fields are not public
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'kpi_id' => $this->kpi->getId(),
            'timestamp' => $this->timestamp,
            'data' => $this->data,
        ];
    }

    protected function insert($db_handle)
    {
        $kpi_id = $this->kpi->getId();
        $data = $db_handle->real_escape_string($this->data);

        $db_handle->query(
            sprintf("INSERT INTO `kpi_%s_entries` (`data`, `kpi_id`) VALUES ('$data', '$kpi_id')", $this->kpi->getType())
        );

        $this->id = $db_handle->insert_id;
    }

    public function update($db_handle)
    {
        throw new Exception("KPI Entries are read-only");
    }

    public function delete($db_handle)
    {
        $query = sprintf(
            "DELETE FROM `kpi_%s_entries` WHERE `id` = %s",
            $this->kpi->getType(),
            $this->id
        );
        $db_handle->query($query);
    }

    private const CREATE_SQL = <<< EOT

    CREATE TABLE `kpi_%s_entries` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `kpi_id` INT NOT NULL ,
        `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `data` %s NOT NULL,

        PRIMARY KEY (`id`),
        FOREIGN KEY (`kpi_id`) REFERENCES kpis(`id`)
    )
EOT;

    protected static function createTable($name, $type, $db_handle)
    {
        $db_handle->query(sprintf(KpiEntry::CREATE_SQL, $name, $type));
    }

    public static function createTables($db_handle, $data_length = 65535)
    {
        KpiEntry::createTable(Kpi::TYPE_NUMBER, "Double", $db_handle);
        KpiEntry::createTable(Kpi::TYPE_STRING, "VARCHAR($data_length)", $db_handle);
    }
}
