<?php
namespace MrVokia\MailHub\Traits;

/**
 * Here is the mail send module features trait
 *
 * @license MIT
 * @package MrVokia\MailHub
 */

use MrVokia\MailHub\Traits\MailHubTrait;

trait MailHubSendTrait
{
    use MailHubTrait;

    /**
     * Set up mail sender
     * @param string $mail
     */
    public function from($mail = '')
    {
        if (!empty($mail)) {
            $this->setFrom($mail);
        }
        if (empty($this->getFrom())) {
            return $this->_throwException('gateways.' . $this->getGateways() . '.options.username');
        }
        return $this;
    }

    /**
     * Set up mail sender name
     * @param string sender name
     */
    public function fromName($name = '')
    {
        if (!empty($name)) {
            $this->setFromName($name);
        }

        return $this;
    }

    /**
     * Set api user to config[default:trigger]
     * @param string $val trigger or batch
     */
    public function type($val = '')
    {
        if (!empty($val)) {
            $this->setApiUser(trim($val));
        }
        return $this;
    }

    /**
     * Setting mail message id
     * @param string|array $mails mail address
     */
    public function id($message = '')
    {
        if (!empty($message)) {
            $this->setMessage($message);
        }

        return $this;
    }

    /**
     * Setting send regular mail address
     * @param string|array $mails mail address
     */
    public function to($mails = '')
    {
        if (empty($mails)) {
            return false;
        }

        $this->fifter($mails, 'To');

        return $this;
    }

    /**
     * Setting CC mail address
     * @param string|array $mails mail address
     */
    public function cc($mails = '')
    {
        if (empty($mails)) {
            return $this;
        }

        $this->fifter($mails, 'CC');

        return $this;
    }

    /**
     * Setting BCC mail address
     * @param string|array $mails mail address
     */
    public function bcc($mails = '')
    {
        if (empty($mails)) {
            return $this;
        }

        $this->fifter($mails, 'BCC');

        return $this;
    }

    /**
     * Set up to accept receipt of the mail address
     * @param  string $mail mail address
     */
    public function replyTo($mail = '')
    {
        if (!empty($mail)) {
            $this->setReplyTo($mail);
        }
        return $this;
    }

    /**
     * Setting the mail subject
     * @param string $val subject
     */
    public function subject($val = '')
    {
        if (!empty($val)) {
            $this->setSubject(trim($val));
        }
        return $this;
    }

    /**
     * Set up mail subject content
     * @param string $val subject content
     */
    public function html($val = '')
    {
        if (!empty($val)) {
            $this->setHtml(trim($val));
        }
        return $this;
    }

    /**
     * Set the variable mail template
     * @param  array  $data variable
     */
    public function xsmtpapi($data = [])
    {
        if ( $this->getEnvTag() ) {
            $data['env'] = '';
            if( env('APP_ENV') != 'product' ) {
                $data['env'] = '[' . env('APP_ENV') . ']';
            }
        }

        array_map(function ($val) use ($data) {
            switch ($val) {
                case 'swiftmail':
                    // Xsmtpapi array to string
                    foreach ($data as $k => $field) {
                        if (is_array($field)) {
                            $data[$k] = current($field);
                        }
                    }
                    return $this->xsmtpapi['swiftmail'] = $data;
                default:
                    $datas     = [];
                    $toAddress = $this->to[$val];
                    foreach ($data as $k => $field) {
                        if (is_array($field)) {
                            $newField = [];
                            foreach (array_keys($toAddress) as $id) {
                                $newField[] = $field[$id];
                            }
                            $datas['%' . $k . '%'] = $newField;
                        } else {
                            $newField = [];
                            for ($i = 0; $i < count($toAddress); $i++) {
                                $newField[] = $field;
                            }
                            $datas['%' . $k . '%'] = $newField;
                        }
                    }
                    return $this->xsmtpapi[$val] = json_encode(['to' => array_values($this->to[$val]), 'sub' => $datas]);
            }
        }, $this->getAllGateways());

        return $this;
    }

    /**
     * Sets the attach of the mail
     * @param  string $blade template name
     */
    public function attach($file = '')
    {
        $this->attach = $file;
        return $this;
    }

    /**
     * Sets the name of the mail template
     * @param  string $blade template name
     */
    public function templateInvokeName($blade = '')
    {
        array_map(function ($val) use ($blade) {
            switch ($val) {
                case 'swiftmail':
                    return $this->templateInvokeName['swiftmail'] = $blade;
                default:
                    return $this->templateInvokeName[$val] = str_replace('.', '_', $blade) . $this->getTemplateTag();
            }
        }, $this->getAllGateways());

        return $this;
    }

    /**
     * Mail gateway filters
     * @param  array $mails mail address
     * @param  string $type  to|cc|bcc
     */
    public function fifter($mails, $type)
    {
        $fifter = $this->getFifter();

        $fifterMail = [];

        if (!is_array($mails)) {
            $mails = [$mails];
        }

        // get test email config
        if ($this->getPretend()) {

            // get test email name group
            $mailTestName = $this->getMailTestName();
            $mails = [$mailTestName];
        }

        // Check fifter
        if (empty($fifter)) {
            $fifterMail[$this->getGateways()] = $mails;
            return $this->{'set' . $type}($fifterMail);
        }

        if (!empty($this->forcibly)) {
            $fifterMail[$this->forcibly] = $mails;
            return $this->{'set' . $type}($fifterMail);
        }

        foreach ($mails as $k => $mail) {
            foreach ($fifter as $doman => $gateway) {
                if (strpos($mail, $doman)) {
                    $fifterMail[$gateway][] = array_pull($mails, $k);
                }
            }
        }
        $fifterMail[$this->getGateways()] = $mails;

        $this->{'set' . $type}($fifterMail);
    }

}
