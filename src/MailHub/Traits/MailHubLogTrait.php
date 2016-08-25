<?php
namespace MrVokia\MailHub\Traits;

/**
 * Here is the mail log module features trait
 * 
 * @license MIT
 * @package MrVokia\MailHub
 */

use MrVokia\MailHub\Exception\MailHubException;

trait MailHubLogTrait
{


    public function test($d)
    {
        if ( !isset($d) ) {
            throw new MailHubException('test');
        }
        return $d . ' - log';
    }

}
