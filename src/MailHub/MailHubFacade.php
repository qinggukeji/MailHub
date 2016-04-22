<?php
namespace MrVokia\MailHub;

/**
 * Here is laravel for MailHub facade
 *
 * @license MIT
 * @package MrVokia\MailHub
 */

use Illuminate\Support\Facades\Facade;

class MailHubFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mailhub';
    }
}
