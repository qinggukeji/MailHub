<?php
namespace MrVokia\MailHub\Contracts;

/**
 * This defines a standardized interface to send
 *
 * @license MIT
 * @package MrVokia\MailHub
 */

interface MailHubSendInterface
{

    public function from($mail);

    public function type($val);

    public function to($mails = '');

    public function cc($mails = '');

    public function bcc($mails = '');

    public function subject($val);

    public function html($val);

    public function xsmtpapi($data = []);

    public function templateInvokeName($blade = '');

    public function fifter($mails, $type);
}
