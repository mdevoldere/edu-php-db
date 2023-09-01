<?php 

namespace Md\Db\Exceptions;


class BadQueryException extends \Exception
{
    public function __construct(string $msg = '', ?\Throwable $ex = null)
    {
        parent::__construct('Query Error : ' . $msg . ' ' . ($ex?->getMessage() ?? ''), 500);
    }
}
