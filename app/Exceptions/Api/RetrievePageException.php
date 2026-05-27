<?php

namespace App\Exceptions\Api;

use Exception;

class RetrievePageException extends Exception
{
    public function __construct(int $pageNum)
    {
        parent::__construct('Failed get data from page ' . $pageNum);
    }
}
