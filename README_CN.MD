#MailHub

[![Build Status](https://travis-ci.org/MrVokia/MailHub.svg?branch=master)](https://travis-ci.org/MrVokia/MailHub)
[![Latest Stable Version](https://poser.pugx.org/mrvokia/mailhub/v/stable)](https://packagist.org/packages/mrvokia/mailhub) [![Total Downloads](https://poser.pugx.org/mrvokia/mailhub/downloads)](https://packagist.org/packages/mrvokia/mailhub) [![Latest Unstable Version](https://poser.pugx.org/mrvokia/mailhub/v/unstable)](https://packagist.org/packages/mrvokia/mailhub) [![License](https://poser.pugx.org/mrvokia/mailhub/license)](https://packagist.org/packages/mrvokia/mailhub)

---

####MailHub是一款用于Laravel的第三方邮件API集成接口。

*它能将你Laravel集成的SwiftMail整合进来形成一套完整优雅的接口，形成第三方邮件和Laravel集成邮件系统的统一规范，加入规则引擎系统检验你的邮件组中的域名使其通过不同的邮件发送器发送。*

+ 取消SwiftMail的闭包调用，提供统一优雅的邮件入口
+ 可直接使用第三方邮件服务，不完全依赖于SwiftMail
+ 目前已接入SendCloud的邮件发送功能，我们将在后续的版本加入标签和队列相关的第三方方法
+ 有问题欢迎交流，接受优化及其他第三方邮件代码调整的PR

##使用流程:

	# 引入依赖库
	composer require mrvokia/mailhub:"dev-master@dev" -vvv
	
	# 在providers添加服务提供者
	MrVokia\MailHub\MailHubServiceProvider::class
	
	# 在aliases添加门面
	'MailHub' => MrVokia\MailHub\MailHubFacade::class
	
	# 关联自动加载
	composer dump

	# 推送配置文件mailhub到config
	php artisan vendor:publish
	
##使用方法:

```php
use MrVokia\MailHub\MailHub;

/**
 * 发送普通邮件
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
 * 发送模板邮件
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
