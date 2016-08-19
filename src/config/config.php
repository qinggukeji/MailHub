<?php

/**
 *    This contains MailHub all the configuration
 */

return [

    /**
     * Set up Mailhub default gateway
     */
    'default'     => 'sendcloud',

    /**
     * Set up mail sender
     */
    'sender_mail' => 'manager@send.qinggukeji.com',

    /**
     * Set up mail sender name
     */
    'sender_name' => '清谷科技',

    /**
     * Set up mail reply
     */
    'reply_mail'  => 'manager@qinggukeji.com',

    /**
     * Set test mail config
     */
    'pretend'  => env('PRETEND', false),

    /**
     * Set test mail config
     */
    'mail_testname'  => env('MAIL_TESTNAME', 'php_test@qinggukeji.com'),

    /**
     * Add in each mail gateway here
     */
    'gateways'    => [

        /**
         * you api gateways mame config
         */
        'sendcloud' => [

            /**
             * Set send email async
             */
            'async' => false,

            /**
             * Set up api mail url
             */
            'api_uri' => 'http://api.sendcloud.net/apiv2/',

            /**
             * This includes the various options for the mail configuration
             */
            'options' => [
                'host'     => 'send.qinggukeji.com',
                'api_user' => [
                    'trigger' => 'qinggukeji',
                    'batch'   => '',
                ],
                'api_key'  => 'vGpIRbvISVGQkuDZ',

                /**
                 * Set up api mail method
                 * [send, template]
                 */
                'method'   => [
                    'send'          => 'mail/send',
                    'send_template' => 'mail/sendtemplate',
                ],
            ],
        ],

        /**
         * Swiftmail config
         * if you don't need swiftmai, then you can remove this configuration item
         */
        'swiftmail' => [
        ],
    ],

    /**
     * Mail distribution filter configuration
     * Example: @domain.com => gateway name
     */
    'fifter'      => [
        '@qinggukeji.com' => 'swiftmail',
    ],

    /**
     * webHook app key
     * Example: @domain.com => gateway name
     */
    'app_key'      => 'vllk6ucz-qyqw-fy1n-8b47-2w2jollt7e',

    /**
     * set switch log config
     */
    'mail_log'     => true,
];
