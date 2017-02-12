#MailHub

[![Build Status](https://travis-ci.org/MrVokia/MailHub.svg?branch=master)](https://travis-ci.org/MrVokia/MailHub)
[![Latest Stable Version](https://poser.pugx.org/mrvokia/mailhub/v/stable)](https://packagist.org/packages/mrvokia/mailhub) [![Total Downloads](https://poser.pugx.org/mrvokia/mailhub/downloads)](https://packagist.org/packages/mrvokia/mailhub) [![Latest Unstable Version](https://poser.pugx.org/mrvokia/mailhub/v/unstable)](https://packagist.org/packages/mrvokia/mailhub) [![License](https://poser.pugx.org/mrvokia/mailhub/license)](https://packagist.org/packages/mrvokia/mailhub)

---
Language: [Chinese](https://github.com/MrVokia/MailHub/edit/master/README_CN.MD)
---

#### MailHub is an email package for Laravel.

*It integrates third-party email services with Laravel's SwiftMail, providing a unified protocol for email routines.
A rule engine is employed to post emails to different domains by corresponding senders.*

+ Provides elegant email interface instead of SwiftMail
+ Connects to third-party email services beyond SwiftMail
+ Currently implements email sending by SendCloud
+ Tags and queues planned in next versions
+ Discussions and PRs are welcome



##How to install:

	# The introduction of dependent libraries
	composer require mrvokia/mailhub:"dev-master@dev" -vvv
	
	# Added service provider in the providers
	MrVokia\MailHub\MailHubServiceProvider::class
	
	# Add the aliases facade
	'MailHub' => MrVokia\MailHub\MailHubFacade::class
	
	# Composer auto load
	composer dump

	# Push Profile mailhub
	php artisan vendor:publish

---

##How to use:

```php
use MrVokia\MailHub\MailHub;

/**
 * Send mail
 */
public function example(MailHub $mailhub)
{
	$mailhub->send()
        	->to(['1@test.com', '2@test.com'])
        	->cc(['3@test.com'])
        	->subject('example')
        	->html('example content')
        	->start()
	//or
	MailHub::send()
			->to(['1@test.com', '2@test.com'])
			->cc(['3@test.com'])
			->subject('example')
			->html('example content')
			->start()
}

/**
 * Send template mail
 */
public function exampleTemplate(MailHub $mailhub)
{
	$mailhub->send()
        	->to(['1@test.com', '2@test.com'])
        	->subject('example')
        	->xsmtpapi([
				'active' => ['test', 'test2']
			])
        	->templateInvokeName('mail.register')
        	->start()
	//or
	MailHub::send()
       		->to(['1@test.com', '2@test.com'])
       		->cc(['3@test.com'])
       		->subject('example')
        	->xsmtpapi([
				'active' => ['test', 'test2']
			])
        	->templateInvokeName('mail.register')
       		->start()
}
```	

---

##Params：

| params  | defaule  | other | description |
|:------------- |:--------------- |:------------- |:------------- |
| ```gateway``` | ```''``` | ```swiftmail / sendcloud``` | Specifies the gateway to send |
| ```async``` | ```false``` | ```true``` | Specify whether third-party mail (SendCloud) needs to use async send, reduce client response time after opening
| ```env``` | ```false``` | ```true``` | Specifies whether to display APP_ENV at the bottom of the message template. When enabled, add the `{` {{$ env or ''}} `` to the current APP_ENV name in the mail template (to keep the production environment clean, APP_ENV = product will not be shown)
| ```pretend ``` | ```false``` | ```true``` | Specify whether to intercept the message, so that the message sent to the specified mailbox, mail address in the mailhub configuration file `` `mail_testname`` configuration |
| ```queue ``` | ```false``` | ```true``` | Specify whether to enable SMTP mail queues. Refer to the Laravel documentation for queue configuration |
| ```queueTarget ``` | ```mailer``` | ```''``` | Specifies the SMTP mail queue pipe name |

---

##Thx
