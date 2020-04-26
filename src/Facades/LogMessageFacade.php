<?php
namespace IzuSoft\Logger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class LogMessageFacade
 * @package IzuSoft\Logger\Facades
 */
class LogMessageFacade extends Facade
{
    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected static function getFacadeAccessor(): string
    {
        return 'LogMessage';
    }
}
