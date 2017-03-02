<?php

namespace MrVokia\MailHub\Jobs;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MrVokia\MailHub\Traits\MailHubLogTrait;
use Carbon\Carbon;

class MailSender implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, MailHubLogTrait;

    /**
     * Send type
     */
    protected $type;

    /**
     * Send params
     */
    protected $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $params)
    {
        $this->type = $type;
        $this->params = $params;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $params = $this->params;

        if( $this->type == 'Normal' ) {

            // Text send
            Mail::raw($params['html'], function ($message) use ($params) {
                if( $params['attach'] ) {
                    $message->attach($params['attach']);
                }
                $message->to($params['to'])
                        ->from(env('MAIL_USERNAME'), $params['fromName'])
                        ->subject($params['subject']);
            });
        } elseif ( $this->type == 'Template' ) {

            // Template send
            Mail::send($params['templateInvokeName'], $params['xsmtpapi'], function ($message) use ($params) {
                if( $params['attach'] ) {
                    $message->attach($params['attach']);
                }
                $message->to($params['to'])
                        ->from(env('MAIL_USERNAME'), $params['fromName'])
                        ->subject($params['subject']);
            });
        }

        $fileDir = $this->getSwiftmailLogDir();
        $this->logInfo('['.Carbon::now($this->local)->format('Y-m-d H:i:s').']'.json_encode($params, JSON_UNESCAPED_UNICODE).chr(10), $fileDir);
    }


    /**
     * Job failed to exec
     * @return [type] [description]
     */
    public function failed()
    {
        $fileDir = $this->getSwiftmailFailLogDir();
        $this->logInfo('['.Carbon::now($this->local)->format('Y-m-d H:i:s').']'.json_encode($this->params, JSON_UNESCAPED_UNICODE).chr(10), $fileDir);
    }
}
