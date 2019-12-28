<?php

namespace Simona\KpiService\Model;

abstract class SqlEntity
{
    public abstract function getId();
    protected abstract function insert($db_handle);
    public abstract function update($db_handle);
    public abstract function delete($db_handle);
}
