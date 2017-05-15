<?php
namespace MrVokia\MailHub\Traits;

/**
 *
 * @license MIT
 * @package MrVokia\MailHub
 */

use MrVokia\MailHub\Exception\MailHubException;

trait MailHubTrait
{

    /**
     * Email gateway
     * @var string
     */
    private $gateway;

    /**
     * Email type [trigger or batch]
     * @var string
     */
    private $type;

    /**
     * Email sender
     * @var string
     */
    private $from;

    /**
     * Email sender name
     * @var string
     */
    private $fromName;

    /**
     * Send mail message
     * @var array
     */
    private $message;

    /**
     * Send mail objects
     * @var array
     */
    private $to;

    /**
     * Email carbon copy
     * @var [type]
     */
    private $cc;

    /**
     * Email Bcc
     * @var [type]
     */
    private $bcc;

    /**
     * [$replyTo description]
     * @var [type]
     */
    private $replyTo;
    /**
     * Mail sender subject
     * @var string
     */
    private $subject;

    /**
     * Mail sender subject content
     * @var string
     */
    private $html;

    /**
     * Send mail template name
     * @var array
     */
    private $templateInvokeName;

    /**
     * Send e-mail template variables
     * @var array
     */
    private $xsmtpapi;

    /**
     * Attach for e-mail
     * @var string
     */
    private $attach;

    /**
     * Forcibly gateway
     * @var string
     */
    private $forcibly;

    /**
     * Set test sender target
     * @var bool
     */
    private $pretend;

    /**
     * Set env tag
     * @var bool
     */
    private $env;

    /**
     * Set async send
     * @var bool
     */
    private $async;

    /**
     * Dispatch set target queue
     * @var bool
     */
    private $queue;

    /**
     * Set target queue (default: mailer)
     * @var string
     */
    private $queueTarget;

    /**
     * Construction of initialization method
     */
    public function __construct()
    {

        $this->setGateways();
        $this->setApiUser();
        $this->setFrom();
        $this->setFromName();
        $this->setReplyTo();
    }

    /**
     * Set gateways to config
     * @param  string $gateway Mail gateway
     */
    public function setGateways($gateway = '')
    {
        if (empty($gateway)) {
            $gateway = config('mailhub.default');
        }
        $this->gateway = $gateway;
    }

    /**
     * Get gateways from config
     * @return  string  Mail gateway
     */
    public function getGateways()
    {
        return $this->gateway;
    }

    /**
     * Get all gateways from config
     * @return  string  Mail gateway
     */
    public function getAllGateways()
    {
        if (!empty($this->forcibly)) {
            return [$this->forcibly];
        }
        return array_keys(config('mailhub.gateways'));
    }

    /**
     * Get option from config
     * @return array Mail gateway config array
     */
    public function getConfig()
    {
        $config = config('mailhub.gateways.' . $this->gateway);

        if (empty($config)) {
            return $this->throwException('gateways');
        }
        return $config;
    }

    /**
     * Get option from config
     * @return array Mail gateway option array
     */
    public function getOption()
    {
        $options = array_get($this->getConfig(), 'options');

        if (empty($options)) {
            return $this->throwException('options');
        }
        return $options;
    }

    /**
     * Get api url from config
     * @return string Api url
     */
    public function getApiUri()
    {
        $apiUri = array_get($this->getConfig(), 'api_uri');

        if (empty($apiUri)) {
            return $this->throwException('api_uri');
        }
        return $apiUri;
    }

    /**
     * Set api user to config[default:trigger]
     * @param string $type Email type [trigger or batch]
     */
    public function setApiUser($type = '')
    {
        if (empty($type)) {
            $type = 'trigger';
        }
        $this->type = $type;
    }

    /**
     * Get api user from config
     * @return string Email type [trigger or batch]
     */
    public function getApiUser()
    {
        $apiUser = array_get($this->getOption(), 'api_user.' . $this->type);

        if (empty($apiUser)) {
            return $this->throwException('api_user.' . $this->type);
        }
        return $apiUser;
    }

    /**
     * Get api key from config
     * @return string Email api key
     */
    public function getApiKey()
    {
        $apiKey = array_get($this->getOption(), 'api_key');

        if (empty($apiKey)) {
            return $this->throwException('api_key');
        }
        return $apiKey;
    }

    /**
     * Get method from config
     * @return string Email api method
     */
    public function getMethod()
    {
        $method = array_get($this->getOption(), 'method');

        if (empty($method)) {
            return $this->throwException('method');
        }
        return $method;
    }

    /**
     * Being defined filters
     * @return array Fifters
     */
    public function getFifter()
    {
        return config('mailhub.fifter');
    }

    /**
     * Get template tag
     * @return string
     */
    public function getTemplateTag()
    {
        return config('mailhub.template_tag');
    }

    /**
     * Get sender from config
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set sender from config
     */
    public function setFrom($from = '')
    {
        if (empty($from)) {
            $from = config('mailhub.sender_mail');
        }
        $this->from = $from;
    }

    /**
     *  Get sender name
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Sender's name from the default setting
     * to obtain from a configuration file
     */
    public function setFromName($fromName = '')
    {
        if (empty($fromName)) {
            $fromName = config('mailhub.sender_name');
        }
        $this->fromName = $fromName;
    }

    /**
     * Set mail id
     */
    public function setMessage($message = '')
    {
        $this->message = $message;
    }

    /**
     * Configure mail receipt side,
     * the default configuration is available from
     */
    public function setReplyTo($mail = '')
    {
        if (empty($mail)) {
            $mail = config('mailhub.reply_mail');
        }
        $this->replyTo = $mail;
    }

    /**
     * Setup Mail recipient
     */
    public function setTo($to = [])
    {
        $this->to = $to;
    }

    /**
     * Set up mail Cc party
     */
    public function setCC($to = [])
    {
        $this->cc = $to;
    }

    /**
     * Set up mail sender dark
     */
    public function setBCC($to = [])
    {
        $this->bcc = $to;
    }

    /**
     * Setting the mail subject
     */
    public function setSubject($subject = '')
    {
        $this->subject = $subject;
    }

    /**
     * Setting the mail subject content
     */
    public function setHtml($html = '')
    {
        $this->html = $html;
    }

    /**
     * Setting the mail template name
     */
    public function getTemplateInvokeName()
    {
        return $this->templateInvokeName;
    }

    /**
     * Setting the mail template variables
     */
    public function getXsmtpapi()
    {
        return $this->xsmtpapi;
    }

    /**
     * Setting the mail forcibly gateway
     * @param string $gateway gateway name
     */
    public function setForcibly($gateway)
    {
        $this->forcibly = $gateway;
    }

    /**
     * Setting the mail async config
     * @param bool $async true|false(default)
     */
    public function setAsync($async)
    {
        $this->async = $async;
    }

    /**
     * Get the mail async config
     */
    public function getAsync()
    {
        return $this->async;
    }

    /**
     * Setting the mail env tag config
     * @param bool $async true|false(default)
     */
    public function setEnvTag($env)
    {
        $this->env = $env;
    }

    /**
     * Get the mail env tag config
     */
    public function getEnvTag()
    {
        return $this->env;
    }

    /**
     * Setting the test mail config
     * @param bool $pretend true|false(default)
     */
    public function setPretend($pretend)
    {
        $this->pretend = $pretend;
    }

    /**
     * Get the test mail config
     */
    public function getPretend()
    {
        return $this->pretend;
    }

    /**
     * Setting the queue
     * @param bool $queue queue status
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * Get queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Setting the queue target
     * @param string $queueTarget queue gateway name
     */
    public function setQueueTarget($queueTarget)
    {
        $this->queueTarget = $queueTarget;
    }

    /**
     * Get queue target
     */
    public function getQueueTarget()
    {
        return $this->queueTarget;
    }

    /**
     * get the test name mail group
     * @param string $gateway gateway name
     */
    public function getMailTestName()
    {
        $mailTestName = config('mailhub.mail_testname');

        if (!$mailTestName) {
                return $this->throwException('mail_testname');
        }

        return $mailTestName;
    }

    /**
     * Throw an exception
     * @param  string $msg Exception message
     * @return MrVokia\MailHub\Exception\MailHubException
     */
    public function throwException($msg)
    {
        $msg = 'Not Found Config From ' . $msg;
        throw new MailHubException($msg);
    }

}
