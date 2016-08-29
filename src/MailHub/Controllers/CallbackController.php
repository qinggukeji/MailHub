<?php
namespace MrVokia\MailHub\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use MrVokia\MailHub\Traits\MailHubLogTrait;
use MrVokia\MailHub\Contracts\MailHubLogInterface;

class CallbackController extends Controller implements MailHubLogInterface
{
    use MailHubLogTrait;

    /**
     * @var EnvironmentManager
     */
    protected $log;

    /**
     * @var http request object
     */
    protected $request;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function sendcloud()
    {
         $options = $this->request->input();
        
        // very signature
        $appkey = $this->getWebhookAppKey();
        if (!$appkey || !isset($options['signature'])) {
            return false;
        }

        // very signature fail
        if (!$this->verify($appkey, $options['token'], $options['timestamp'], $options['signature'])) {
            return false;
        }
        
        $handle = [];
        $data = ['event', 'message', 'apiUser', 'emailId' ,'recipient'];

        foreach ($data as $value) {
            if (isset($options[$value])) {
                $handle[$value] = $options[$value];
            }
        }
        if (isset($options['event'])) {
            switch ($options['event']) {
                case 'deliver':
                    $fileSystemName = 'sendcloud_deliver_email';
                    $logdir = 'getSendCloudLogDir';
                    break;
                case 'invalid':
                    $fileSystemName = 'sendcloud_invalid_email';
                    $logdir = 'getSendCloudFailLogDir';
                    break;         
                default:
                    $fileSystemName = 'sendcloud_invalid_email';
                    $logdir = 'getSendCloudFailLogDir';
                    break;
            }
            // writing log
            $logContent = $this->setLogContent($handle);
            $fileDir = $this->{$logdir}();
            $this->setLogName($fileSystemName);
            $this->logInfo($logContent, $fileDir);
        }
    }

    /**
     * very signature
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    private function verify($appkey, $token, $timestamp, $signature)
    {
        $hash="sha256";
        $result=hash_hmac($hash,$timestamp.$token,$appkey);
        return strcmp($result,$signature)==0?1:0;
    }

    /**
     * get sendcloud webhook app_key
     * @return [bool]          [log config]
     */
    public function getWebhookAppKey()
    {   
        $appkey = config('mailhub.gateways.sendcloud.webhook_app_key');
        if (!isset($appkey)) {
                return $this->throwException('webhook_app_key');
        }
        return $appkey;
    }

}