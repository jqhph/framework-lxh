<?php
namespace Lxh\Logger\Handler;

use Monolog\Logger;
use Lxh\Contracts\Container\Container;

/**
 * 按日期分日志文件
 */
class DaysFileHandler extends \Monolog\Handler\StreamHandler 
{
	/**
	 * @var string
	 */
	protected $dateFormat = 'Y-m-d';

	/**
	 * @var string
	 */
	protected $filenameFormat = '{filename}-{date}';

	/**
	 * 项目根目录
	 *
	 * @var string
	 */
	private $root = __ROOT__;

	/**
	 * @var string
	 */
	private $separator = DIRECTORY_SEPARATOR;

	/**
	 * @var int
	 */
	private $maxFiles = 180;//目录下日志文件最大数量。0为不限制

	/**
	 * @var \Lxh\File\FileManager
	 */
	private $files;
	
	public function __construct($stream, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $useLocking = false) 
	{
		$this->files = files();
		$this->filename = $this->normalizePath($stream);
		
		$this->removeExcessFile();//删除超出的文件
		$this->formatFilename($this->filename);
		
		parent::__construct($this->filename, $level, $bubble, $filePermission);
	}

	/**
	 * @param $path
	 * @return string
	 */
	protected function normalizePath(&$path)
	{
		if (strpos($path, '/') === 0 || strpos($path, ':')) {
			return $path;
		}
		return $this->root . $path;
	}

	/**
	 *
	 * @param $stream
	 * @return bool
	 */
	private function formatFilename(& $stream) 
	{
		if (! is_string($stream)) {
			return false;
		}
		$fileInfo = pathinfo($stream);
		$glob = str_replace(
				array('{filename}', '{date}'),
				array($fileInfo['filename'], date($this->dateFormat)),
				$this->filenameFormat
		);
		
		$stream = $fileInfo['dirname'] . $this->separator . $glob . '.' . $fileInfo['extension'];
	}
	
	public function setMaxFiles($maxFiles) 
	{
		$this->maxFiles = $maxFiles;
	}
	
	public function setDateFormat($format) 
	{
		$this->dateFormat = $format;
	}
	
	/**
	 * 删除多余的文件 
	 * */
    protected function removeExcessFile() 
    {
        if (0 === $this->maxFiles) {
            return; //unlimited number of files for 0
        }

        $filePattern = $this->getFilePattern();
        $dirPath = $this->files->getDirName($this->filename);
        $logFiles = $this->files->getFileList($dirPath, false, $filePattern, true);

        if (! empty( $logFiles) && count($logFiles) > $this->maxFiles) {

            usort($logFiles, function($a, $b) {
                return strcmp($b, $a);
            });

            $logFilesToBeRemoved = array_slice($logFiles, $this->maxFiles);

            $this->files->removeFile($logFilesToBeRemoved, $dirPath);
        }
    }

	/**
	 * @return mixed|string
	 */
    protected function getFilePattern() 
    {
        $fileInfo = pathinfo($this->filename);
        $glob = str_replace(
            array('{filename}', '{date}'),
            array($fileInfo['filename'], '.*'),
            $this->filenameFormat
        );

        if (! empty($fileInfo['extension'])) {
            $glob .= '\.' . $fileInfo['extension'];
        }

        $glob = '^' . $glob . '$';

        return $glob;
    }
}
