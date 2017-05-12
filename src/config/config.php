<?php

	/**
	 *	This contains MailHub all the configuration
	 */

	return [

		/**
		 * Set up Mailhub default gateway
		 */
		'default' => 'sendcloud',

		/**
		 * Set up mail sender
		 */
	    'sender_mail' => 'you_sender_mail@domain.com',

		/**
		 * Set up mail sender name
		 */
	    'sender_name' => trans('you_sender_name'),

	    /**
	     * Set up mail reply
	     */
	    'reply_mail' => 'you_reply_mail@domain.com',

		/**
		 * Add in each mail gateway here
		 */
		'gateways' => [

			/**
			 * you api gateways mame config
			 */
			'sendcloud' => [

				/**
				 * Set up api mail url
				 */
				'api_uri' => 'http://api.sendcloud.net/apiv2/',

				/**
				 * This includes the various options for the mail configuration
				 */
				'options' => [
					'host' => '',
	                'api_user' => [
	                	'trigger' => '',
	                	'batch' => '',
	                ],
	                'api_key' => '',

	                /**
	                 * Set up api mail method
	                 * [send, template]
	                 */
	                'method' => [
	                	'send' => 'mail/send',
	                	'send_template' => 'mail/sendtemplate',
	                ]
				],

				/**
	             * webHook app key
	             */
	            'webhook_app_key'      => '',
			],

			/**
			 * Swiftmail config
			 * if you don't need swiftmai, then you can remove this configuration item
			 */
			'swiftmail' => [
			]
		],


		/**
		 * Set your third party template tag
		 */
		'template_tag' => env('MAIL_TEMPLATE_TAG', ''),


		/**
		 * Mail distribution filter configuration
		 * Example: @domain.com => gateway name
		 */
		'fifter' => [
			'@domain.com' => 'swiftmail'
		]
	];
