<?php

namespace Lxh\Debug\Records;

use Exception;

class Database
{
    /**
     * @var string
     */
    protected $command;

    /**
     * @var array
     */
    protected $prepareData = [];

    /**
     * @var float
     */
    protected $usetime;

    /**
     * @var Exception
     */
    protected $exception;

    public function __construct($command = '', array $data = [], $usetime = 0.00, Exception $e = null)
    {
        $this->setCommand($command);
        $this->setData($data);
        $this->setUsetime($usetime);
        if ($e) {
            $this->setException($e);
        }
    }

    public function setCommand($command)
    {
        $this->command = &$command;
        return $this;
    }

    public function setData(array $data)
    {
        $this->prepareData = &$data;
        return $this;
    }

    public function setUsetime($usetime)
    {
        $this->usetime = &$usetime;
        return $this;
    }

    public function setException(Exception $e)
    {
        $this->exception = $e;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getData()
    {
        return $this->prepareData;
    }

    public function getUsetime()
    {
        return $this->usetime;
    }

    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'useage-time' => $this->usetime,
            'command'     => &$this->command,
            'params'      => &$this->prepareData,
            'exception'   => $this->exception ? $this->exception->getTraceAsString() : '',
        ];
    }

    public function __toString()
    {
        $data = json_encode($this->prepareData);

        $msg = '';
        if ($this->exception) {
            $msg = "\n".$this->exception->getTraceAsString();
        }

        return "[{$this->usetime}] [{$this->command}] {$data} {$msg}";
    }

}
