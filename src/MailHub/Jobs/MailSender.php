<?php

namespace MrVokia\MailHub\Jobs;

use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use Event;

class MailSender implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
        } elseif ( $this->type == 'Template' ) {

            // Template send
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
        }

        $id = $params['id'];
        $status = 'Succeeded';
        Event::fire('mailhub.job.sent', compact('id', 'status'));

    }


    /**
     * Job failed to exec
     * @return [type] [description]
     */
    public function failed()
    {
        $id = $this->params['id'];
        $status = 'Failed';
        Event::fire('mailhub.job.sent', compact('id', 'status'));
    }
}
