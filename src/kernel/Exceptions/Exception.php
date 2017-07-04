<?php
namespace Lxh\Exceptions;

use \Monolog\Logger;

class Exception extends \Exception 
{
	/**
	 * 错误级别
	 * */
	protected $level;
	
	const WARNING 	= Logger::WARNING;//出现非错误的异常
	const ERROR 	= Logger::ERROR;//运行时错误，但是不需要立刻处理。
	const CRITICA   = Logger::CRITICAL;//严重错误。
	const EMERGENCY = Logger::EMERGENCY;//系统不可用。
	
	protected $code = 500;
	
	/**
	 * 异常类（抛出此异常时会中断程序运行并返回相关错误编码和错误信息）
	 * @param $message string 错误信息
	 * @param $level string 错误级别(当错误信息为空时不生效)，当设置为false或者NULL时不保存日志
	 * @param $code string or int 错误编码 
	 * */
	public function __construct($message = null, $code = null, $level = self::ERROR)
	{
		if ($code !== null && $code !== false) {
			$this->code = $code;
		}
		
		if ($level !== null && $level !== false) {
			$this->checkLevel($level);
			$this->level = $level;
		}
		
		parent::__construct($message);
	}

	//获取错误级别
	public function getLevel() 
	{
		return $this->level;
	}
	
	private function checkLevel(& $level) {
		if ($level != self::ERROR && $level != self::WARNING && $level != self::CRITICA && $level != self::EMERGENCY && $level != Logger::NOTICE) {
			$level = self::ERROR;
		}
	}

}
