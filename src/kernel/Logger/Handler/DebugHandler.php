<?php
namespace Neta\Utils\Log\Handler;

use \Neta\Utils\Log\Formatter\DebugFormatter;
use \Monolog\Logger;

class DebugHandler extends \Monolog\Handler\StreamHandler 
{
	
	public function __construct($stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false) 
	{
		$this->formatter = new DebugFormatter();
		
		parent::__construct($stream, $level, $bubble);
	}
}
