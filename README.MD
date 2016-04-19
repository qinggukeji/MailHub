#MailHub

---
Language: [Chinese](https://github.com/MrVokia/MailHub/edit/master/README_CN.MD)
---

#### ** MailHub is an email package for Laravel. **

*It integrates third-party email services with Laravel's SwiftMail, providing a unified protocol for email routines.
A rule engine is employed to post emails to different domains by corresponding senders.*

+ ** Provides elegant email interface instead of SwiftMail **
+ ** Connects to third-party email services beyond SwiftMail **
+ ** Currently implements email sending by SendCloud **
+ ** Tags and queues planned in next versions **
+ ** Discussions and PRs are welcome **



##How to install:

	// The introduction of dependent libraries
	composer require MrVokid\MailHub
	
	// Added service provider in the providers
	MrVokia\MailHub\MailHubServiceProvider::class
	
	// Add the aliases facade
	'MailHub' => MrVokia\MailHub\MailHubFacade::class
	
	// Push Profile mailhub
	php artisan publish


##How to use:


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

	