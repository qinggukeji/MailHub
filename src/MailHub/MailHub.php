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
        if (!empty($options['gateway'])) {
            $this->send->setForcibly($options['gateway']);
        }

        //设置异步请求配置
        if (!empty($options['async'])) {
            $this->send->setAsync($options['async']);
        }

        //测试邮件配置
        if (!empty($options['pretend'])) {
            $this->send->setPretend($options['pretend']);
        }

        return $this->send;
    }

}
