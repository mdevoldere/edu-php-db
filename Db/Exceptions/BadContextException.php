<?php 

namespace Md\Db\Exceptions;


class BadContextException extends \Exception
{
    public function __construct(string $msg = '', ?\Throwable $ex = null)
    {
        parent::__construct('Bad DbContext : ' . $msg . ' ' . ($ex?->getMessage() ?? ''), 500);
    }
}
