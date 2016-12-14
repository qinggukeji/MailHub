<?php
namespace MrVokia\MailHub;

/**
 * This class is the main entry point of MailHub.
 *
 * @license MIT
 * @package MrVokia\Entrust
 */

use MrVokia\MailHub\MailHubSend as Send;

class MailHub
{

    /**
     * Create a new confide instance.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($reflectionClass)
    {
        foreach ($reflectionClass as $name => $class) {
            // if( $class->isInstance( $this->checkClass( $name ) ) )
            // {
            $this->$name = $class->newInstanceArgs();
            // }
        }
    }

    /**
     * Instantiated class detection
     *
     * @param  string $val Module name
     * @return object      Instantiated class
     */
    public function checkClass($val)
    {
        $class = 'MrVokia\MailHub' . camel_case('\MailHub_' . $val);
        return new $class($val);
    }

    /**
     * Trigger a mail module
     *
     * @return object
     */
    public function send($options = [])
    {
        isset($options['gateway']) ? $gateway = $options['gateway'] : $gateway = 'swiftmail';
        $this->send->setForcibly($gateway);

        // set async request
        isset($options['async']) ? $async = $options['async'] : $async = false;
        $this->send->setAsync($async);

        // set env tag
        isset($options['env']) ? $env = $options['env'] : $env = false;
        $this->send->setEnvTag($env);

        // set test mail config
        isset($options['pretend']) ? $pretend = $options['pretend'] : $pretend = false;
        $this->send->setPretend($pretend);

        // setting queue
        isset($options['queue']) ? $queue = $options['queue'] : $queue = false;
        $this->send->setQueue($queue);

        // setting queue target
        isset($options['queueTarget']) ? $queueTarget = $options['queueTarget'] : $queueTarget = 'mailer';
        $this->send->setQueueTarget($queueTarget);

        return $this->send;
    }

}
