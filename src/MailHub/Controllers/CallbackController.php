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
     * @param EnvironmentManager $environmentManager
     */
    public function __construct()
    {
        dd($this->test('jump'));
    }

    public function sendcloud()
    {

    }
}