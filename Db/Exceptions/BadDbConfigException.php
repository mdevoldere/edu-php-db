<?php 

namespace Md\Db\Exceptions;


class BadDbConfigException extends \Exception
{
    public function __construct(string $msg = '', ?\Throwable $ex = null)
    {
        parent::__construct('Bad DbConfig : ' . $msg . ' ' . ($ex?->getMessage() ?? ''), 500);
    }
}
