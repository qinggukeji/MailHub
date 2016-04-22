<?php
namespace MrVokia\MailHub;

/**
 * This class is the main send entry point of MailHub.
 *
 * @license MIT
 * @package MrVokia\MailHub
 */

use Mail;
use MrVokia\MailHub\Contracts\MailHubSendInterface;
use MrVokia\MailHub\Traits\MailHubSendTrait;

class MailHubSend implements MailHubSendInterface
{
    use MailHubSendTrait;

    /**
     * Start mail send
     */
    public function start()
    {

        // set methods by config
        $methods = $this->getMethod();

        // normal send
        $classSuffix = 'send';

        // template send
        $xsmtpapi           = $this->getXsmtpapi();
        $templateInvokeName = $this->getTemplateInvokeName();
        if (!empty($xsmtpapi) && !empty($templateInvokeName)) {
            $classSuffix = 'send_template';
        }

        // check method is exist
        if (!in_array($classSuffix, array_keys($methods))) {
            $this->_throwException($classSuffix);
        }

        // get class name && verify method defined
        $className = camel_case('start_' . $classSuffix);

        if (method_exists($this, $className)) {
            return $this->$className($this->getApiUri() . $methods[$classSuffix]);
        }
        return $this->_throwException($className);
    }

    /**
     * It's you can send normal mail
     * @param  string $uri Api Url
     * @return json
     */
    private function startSend($uri)
    {
        // Def send params
        $params = [
            'apiUser'  => $this->getApiUser(),
            'apiKey'   => $this->getApiKey(),
            'from'     => $this->getFrom(),
            'fromName' => $this->getFromName(),
            'to'       => '',
            'cc'       => '',
            'bcc'      => '',
            'replyTo'  => $this->replyTo,
            'subject'  => $this->subject,
            'html'     => $this->html,
        ];

        // Set the gateway corresponding method
        foreach ($this->to as $gateway => $mails) {
            $params['to']  = isset($this->to[$gateway]) ? $this->to[$gateway] : null;
            $params['cc']  = isset($this->cc[$gateway]) ? $this->cc[$gateway] : null;
            $params['bcc'] = isset($this->bcc[$gateway]) ? $this->bcc[$gateway] : null;

            if ('swiftmail' == $gateway) {
                $this->swiftMailSend($params);
            } else {
                $params['to']  = isset($params['to']) ? implode(';', $params['to']) : null;
                $params['cc']  = isset($params['cc']) ? implode(';', $params['cc']) : null;
                $params['bcc'] = isset($params['bcc']) ? implode(';', $params['bcc']) : null;
                $this->apiMailSend($uri, $gateway, $params);
            }
        }

    }

    /**
     * It's you can send template mail
     * @param  string $uri Api Url
     * @return json
     */
    private function startSendTemplate($uri)
    {
        // Def send params
        $params = [
            'apiUser'            => $this->getApiUser(),
            'apiKey'             => $this->getApiKey(),
            'from'               => $this->getFrom(),
            'fromName'           => $this->getFromName(),
            'to'                 => '',
            'cc'                 => $this->cc,
            'bcc'                => $this->bcc,
            'replyTo'            => $this->replyTo,
            'xsmtpapi'           => '',
            'subject'            => $this->subject,
            'templateInvokeName' => '',
        ];

        // Set the gateway corresponding method
        foreach ($this->to as $gateway => $mails) {
            $params['to']                 = isset($this->to[$gateway]) ? $this->to[$gateway] : null;
            $params['cc']                 = isset($this->cc[$gateway]) ? $this->cc[$gateway] : null;
            $params['bcc']                = isset($this->bcc[$gateway]) ? $this->bcc[$gateway] : null;
            $params['xsmtpapi']           = $this->xsmtpapi[$gateway];
            $params['templateInvokeName'] = $this->templateInvokeName[$gateway];

            if ('swiftmail' == $gateway) {
                $this->swiftMailTemplateSend($params);
            } else {
                $params['to']  = isset($params['to']) ? implode(';', $params['to']) : null;
                $params['cc']  = isset($params['cc']) ? implode(';', $params['cc']) : null;
                $params['bcc'] = isset($params['bcc']) ? implode(';', $params['bcc']) : null;
                $this->apiMailTemplateSend($uri, $gateway, $params);
            }
        }
    }

    /**
     * Api mail send
     * @return [json] Api return
     */
    private function apiMailSend($uri, $gateway, $params)
    {
        //change mail gateway
        $this->setGateways($gateway);

        //send
        $client = new \GuzzleHttp\Client();
        $res    = $client->request('post', $uri, [
            'form_params' => $params,
        ]);

        return (string) $res->getBody();
    }

    /**
     * Swift mail send
     * @return [null]
     */
    private function swiftMailSend($params)
    {
        Mail::raw($params['html'], function ($message) use ($params) {
            $message->to($params['to'])->subject($params['subject']);
        });
    }

    /**
     * Api mail template send
     * @return [json] Api return
     */
    private function apiMailTemplateSend($uri, $gateway, $params)
    {
        //change mail gateway
        $this->setGateways($gateway);

        //send
        $client = new \GuzzleHttp\Client();
        $res    = $client->request('post', $uri, [
            'form_params' => $params,
        ]);

        return (string) $res->getBody();
    }

    /**
     * Api mail template send
     * @return [null]
     */
    private function swiftMailTemplateSend($params)
    {
        Mail::send($params['templateInvokeName'], $params['xsmtpapi'], function ($message) use ($params) {
            $message->to($params['to'])->subject($params['subject']);
        });
    }
}
