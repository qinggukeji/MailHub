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
use MrVokia\MailHub\Jobs\MailSender;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise;
use Event;

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
        $xsmtpapi = $this->getXsmtpapi();
        $templateInvokeName = $this->getTemplateInvokeName();
        if (!empty($xsmtpapi) && !empty($templateInvokeName)) {
            $classSuffix = 'send_template';
        }

        // check method is exist
        if (!in_array($classSuffix, array_keys($methods))) {
            $this->throwException($classSuffix);
        }

        // get class name && verify method defined
        $className = camel_case('start_' . $classSuffix);

        if (method_exists($this, $className)) {
            return $this->$className($this->getApiUri() . $methods[$classSuffix]);
        }
        return $this->throwException($className);
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
            'id'       => $this->message,
            'to'       => '',
            'cc'       => '',
            'bcc'      => '',
            'replyTo'  => $this->replyTo,
            'subject'  => $this->subject,
            'attach'     => $this->attach,
            'html'     => $this->html,
        ];

        // Set the gateway corresponding method
        foreach ($this->to as $gateway => $mails) {
            $params['to']  = isset($this->to[$gateway]) ? $this->to[$gateway] : null;
            $params['cc']  = isset($this->cc[$gateway]) ? $this->cc[$gateway] : null;
            $params['bcc'] = isset($this->bcc[$gateway]) ? $this->bcc[$gateway] : null;

            if ('swiftmail' == $gateway) {
                if( $this->getQueue() ) {
                    dispatch((new MailSender('Normal', $params))->onQueue($this->getQueueTarget()));
                    break;
                }
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
            'id'                 => $this->message,
            'to'                 => '',
            'cc'                 => $this->cc,
            'bcc'                => $this->bcc,
            'replyTo'            => $this->replyTo,
            'xsmtpapi'           => '',
            'subject'            => $this->subject,
            'attach'     => $this->attach,
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
                if( $this->getQueue() ) {
                    dispatch((new MailSender('Template', $params))->onQueue($this->getQueueTarget()));
                    break;
                }
                $this->swiftMailTemplateSend($params);
            } else {
                $params['to']  = isset($params['to']) ? implode(';', $params['to']) : null;
                $params['cc']  = isset($params['cc']) ? implode(';', $params['cc']) : null;
                $params['bcc'] = isset($params['bcc']) ? implode(';', $params['bcc']) : null;
                $this->apiMailSend($uri, $gateway, $params);
            }
        }
    }

    /**
     * Api mail send
     * @return [json] Api return
     */
    private function apiMailSend($uri, $gateway, $params)
    {
        // change mail gateway
        $this->setGateways($gateway);

        // get Async config
        $async = $this->getAsync();
        if (!$async) {

            $id = isset($params['id']) ? $params['id'] : '';

            // send
            $client = new \GuzzleHttp\Client();

            // sync send
            $res = $client->request('post', $uri, [
                'form_params' => $params,
            ]);

            $body = (string) $res->getBody();
            Event::fire('mailhub.api.result', compact('id', 'body'));

            return (string) $body;
        } else {

            // async send
            $curl = new CurlMultiHandler();
            $handler = HandlerStack::create($curl);

            // send
            $client = new \GuzzleHttp\Client(['handler' => $handler]);
            $promise = $client->requestAsync('post', $uri, [
                'form_params' => $params,
            ]);
            if(isset($params['xsmtpapi']))
            {
                $params['xsmtpapi'] = json_encode(json_decode($params['xsmtpapi']),JSON_UNESCAPED_UNICODE);
            }

            $promise->then(
                function (ResponseInterface $res) use ($params){
                    $id = isset($params['id']) ? $params['id'] : '';
                    $body = (string) $res->getBody();
                    Event::fire('mailhub.api.result', compact('id', 'body'));
                },
                function (RequestException $e) use ($params){
                    $id = isset($params['id']) ? $params['id'] : '';
                    $status= 'Failed';
                    Event::fire('mailhub.sent', compact('id', 'status'));
                }
            );
            $aggregate = Promise\all([$promise]);
            while (!Promise\is_settled($aggregate)) {
                $curl->tick();
            }
            return true;
        }
    }

    /**
     * Swift mail send
     * @return [null]
     */
    private function swiftMailSend($params)
    {
        Mail::raw($params['html'], function ($message) use ($params) {
            if( $params['attach'] ) {
                $message->attach($params['attach']);
            }
            $message->to($params['to']);

            if( ! empty($params['cc']) ) {
                $message->cc($params['cc']);
            }

            if( ! empty($params['bcc']) ) {
                $message->cc($params['cc']);
            }

            $message->from(env('MAIL_USERNAME'), $params['fromName'])
                    ->subject($params['subject']);
        });

        $id = isset($params['id']) ? $params['id'] : '';
        if(count(Mail::failures()) > 0){
            $status= 'Failed';
        } else {
            $status= 'Succeeded';
        }
        Event::fire('mailhub.sent', compact('id', 'status'));
    }

    /**
     * Api mail template send
     *
     * In API SendCloud, the API of the ordinary send and send the template is consistent,
     * the follow-up will be required to open
     *
     * @return [json] Api return
     */
    // private function apiMailTemplateSend($uri, $gateway, $params)
    // {
    //     // change mail gateway
    //     $this->setGateways($gateway);
    //
    //     // get Async config
    //     $async = $this->getAsync();
    //     if (!$async) {
    //
    //         // send
    //         $client = new \GuzzleHttp\Client();
    //
    //         // sync send
    //         $res = $client->request('post', $uri, [
    //             'form_params' => $params,
    //         ]);
    //
    //         return (string) $res->getBody();
    //     } else {
    //
    //         // async send
    //         $curl = new CurlMultiHandler();
    //         $handler = HandlerStack::create($curl);
    //
    //         // send
    //         $client = new \GuzzleHttp\Client(['handler' => $handler]);
    //         $promise = $client->requestAsync('post', $uri, [
    //             'form_params' => $params,
    //         ]);
    //
    //         $promise->then(
    //             function (ResponseInterface $res) {
    //                 // to do(log)
    //             },
    //             function (RequestException $e) {
    //                 // to do(log)
    //             }
    //         );
    //         $aggregate = Promise\all([$promise]);
    //         while (!Promise\is_settled($aggregate)) {
    //             $curl->tick();
    //         }
    //         return true;
    //     }
    // }

    /**
     * swift mail template send
     * @return [null]
     */
    private function swiftMailTemplateSend($params)
    {
        Mail::send($params['templateInvokeName'], $params['xsmtpapi'], function ($message) use ($params) {
            if( $params['attach'] ) {
                $message->attach($params['attach']);
            }
            $message->to($params['to']);

            if( ! empty($params['cc']) ) {
                $message->cc($params['cc']);
            }

            if( ! empty($params['bcc']) ) {
                $message->cc($params['cc']);
            }

            $message->from(env('MAIL_USERNAME'), $params['fromName'])
                    ->subject($params['subject']);
        });

        $id = isset($params['id']) ? $params['id'] : '';
        if(count(Mail::failures()) > 0){
            $status= 'Failed';
        } else {
            $status= 'Succeeded';
        }
        Event::fire('mailhub.sent', compact('id', 'status'));
    }
}
