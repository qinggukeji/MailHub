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

---
	
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

/**
 *	更多的配置
 */
public function exampleOptions(MailHub $mailhub)
{
    $mailhub->send([
    
    				// 设置邮件通道
    				'gateway' => 'swiftmail',
    				
    				// 开启第三方邮件的异步发送
    				'async' => true,
    				
    				// 是否在邮件模板展示APP_ENV
    				'env' => true,

					// 开启邮件拦截功能
    				'pretend' => true,
    				
    				// 开启SMTP邮件队列
    				'queue' => true,
    				
    				// 指定SMTP邮件队列管道名称
    				'queueTarget' => 'test',
    		  ])
    		  ...
 
```

---

##参数说明：

| 参数  | 默认值  | 其他可选值 | 说明 |
|:------------- |:--------------- |:------------- |:------------- |
| ```gateway``` | ```''``` | ```swiftmail / sendcloud``` | 用于指定通道的强制发送 |
| ```async``` | ```false``` | ```true``` | 指定第三方邮件(SendCloud)是否需要使用异步发送，开启后减少客户端响应时间
| ```env``` | ```false``` | ```true``` | 指定是否在邮件模板底部展示APP_ENV，开启后在邮件模板加入```{{ $env or '' }}``展示当前APP_ENV名称(为保持生产环境整洁性，APP_ENV = product时将不给予展示)
| ```pretend ``` | ```false``` | ```true``` | 指定是否在对发信进行拦截，使邮件发送到指定邮箱, 邮箱地址在mailhub配置文件的```mail_testname```配置 |
| ```queue ``` | ```false``` | ```true``` | 指定是否开启SMTP邮件队列，队列配置参考Laravel文档 |
| ```queueTarget ``` | ```mailer``` | ```''``` | 指定SMTP邮件队列管道名称 |

---

##感谢阅读

