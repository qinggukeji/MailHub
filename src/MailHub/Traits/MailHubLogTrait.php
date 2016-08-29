<?php
namespace MrVokia\MailHub\Traits;

use Storage,
	Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
/**
 * Here is the mail log module features trait
 * 
 * @license MIT
 * @package MrVokia\MailHub
 */

use MrVokia\MailHub\Exception\MailHubException;

trait MailHubLogTrait
{
	/**
     * Log name
     * @var string
     */
    private $logName = 'mail_log.log';

    /**
     * Log name
     * @var string
     */
    private $local = 'Asia/Shanghai';

    /**
	 * set log content
	 */
	public function setLogContent($handle = array())
	{

		// local is shanghai
		$time = Carbon::now($this->local);
		$logHandle = '['.$time.']';
		foreach($handle as $key => $val) {
			$logHandle .= $key.':'.$val.',';
		}
		return rtrim($logHandle, ',');
	}

	/**
	 * write log
	 * @param  string $logContent [logContent]
	 * @param  string $logDirPath [filepath]
	 */
	public function logInfo($logContent, $logDirPath)
	{
		$filesystem = new Filesystem();
		if (!$logContent || !$logDirPath) return false;
		if($this->getMailLog())
		{
			// log file all path
			$logPath = $logDirPath.$this->getLogName();
			if($filesystem->exists($logPath))
			{
				// everyDay new a file
				$content = $filesystem->get($logPath);
				if($logTime = substr($content,1,10))
				{
					if(Carbon::now($this->local)->toDateString() == $logTime)
					{
						$filesystem->append($logPath,$logContent.PHP_EOL);
					}
					else
					{
						$new_log_path = $logDirPath.$logTime.$this->getLogName();
						if (!$filesystem->exists($new_log_path)) {
							$filesystem->move($logPath, $new_log_path);
						}
							$filesystem->put($logPath,$logContent);
					}
				}
        	}
        	else
        	{
            	$filesystem->put($logPath,$logContent);
        	}
        }
    	
	}

	/**
	 * get log config
	 * @return [bool]          [log config]
	 */
	public function getMailLog()
	{	
		$mailLog = config('mailhub.mail_log');
		if (!isset($mailLog)) {
                return $this->throwException('mail_log');
        }
		return $mailLog;
	}

	/**
	 * get sendcloud log dir
	 * @return [string]  $dir 	    [logpath]
	 */
	public function getSendCloudLogDir()
	{	
		$dir = config('mailhub.sendcloud_log_dir');
		if (!isset($dir)) {
                return $this->throwException('sendcloud_log_dir');
        }
		return $dir;
	}

	/**
	 * get sendcloud fail log dir
	 * @return [string]  $dir 	    [logpath]
	 */
	public function getSendCloudFailLogDir()
	{	
		$dir = config('mailhub.sendcloud_fail_log_dir');
		if (!isset($dir)) {
                return $this->throwException('sendcloud_fail_log_dir');
        }
		return $dir;
	}

	/**
	 * get Swiftmail log dir
	 * @return [string]  $dir 	    [logpath]
	 */
	public function getSwiftmailLogDir()
	{	
		$dir = config('mailhub.swiftmail_log_dir');
		if (!isset($dir)) {
                return $this->throwException('swiftmail_log_dir');
        }
		return $dir;
	}

	/**
	 * get Swiftmail fail log dir
	 * @return [string]   $dir	    [logpath]
	 */
	public function getSwiftmailFailLogDir()
	{	
		$dir = config('mailhub.swiftmail_fail_log_dir');
		if (!isset($dir)) {
                return $this->throwException('swiftmail_fail_log_dir');
        }
		return $dir;
	}

	/**
	 * set log name
	 * @param [string]  $logName    [log name]
	 */
	public function setLogName($logName = 'mail_log')
	{	
		$this->logName = $logName.'.log';
	}

	/**
	 * get log name
	 * @param [string]  $logName    [log name]
	 */
	public function getLogName()
	{	
		return $this->logName;
	}

	/**
     * Throw an exception
     * @param  string $msg Exception message
     * @return MrVokia\MailHub\Exception\MailHubException
     */
    public function throwException($msg)
    {
        $msg = 'Not Found Config From ' . $msg;
        throw new MailHubException($msg);
    }

}
