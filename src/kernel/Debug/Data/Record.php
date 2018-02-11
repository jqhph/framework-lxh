<?php
/**
 * Created by PhpStorm.
 * User: Jqh
 * Date: 2017/6/24
 * Time: 12:35
 */

namespace Lxh\Debug\Data;

use Lxh\Helper\Entity;

class Record
{
    const WRITE = 'w';
    const READ = 'r';
    const CONNECT = 'c';

    /**
     * @var Entity
     */
    protected $data;

    /**
     * 所有记录数组
     *
     * @var array
     */
    protected $records = [];

    public function __construct()
    {
        $this->data = new Entity();
    }

    /**
     * 保存命令
     *
     * @param  string $command
     * @return static
     */
    public function command($command)
    {
        $this->data->append('command', $command);
        return $this;
    }

    /**
     * 保存执行时间
     *
     * @param int $time
     * @return static
     */
    public function time($time)
    {
        if ($time) {
            $this->data->append('useageTime', microtime(true) - $time);
        }

        return $this;
    }

    /**
     * 保存命令类型
     *
     * @param  string $type
     * @return static
     */
    public function type($type)
    {
        $this->data->append('type', $type);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function params(array $params)
    {
        $this->data->append('params', $params);
        return $this;
    }

    /**
     * 获取类型次数
     *
     * @param $type
     * @return int|mixed
     */
    public function typesCount($type)
    {
        if (empty($this->records)) {
            $this->compute();
        }
        $k = $type . '-times';

        return ! empty($this->records[$k]) ? $this->records[$k] : 0;
    }

    /**
     * @return $this
     */
    public function compute()
    {
        $this->records['all-useage-time'] = 0;

        foreach ((array) $this->data->get('command') as $k => & $command) {
            $useageTime = $this->data->get("useageTime.$k", 0);
            $type = $this->data->get("type.$k");
            $this->records['info'][] = [
                'useage-time' => $useageTime,
                'command' => $command,
                'type' => $type,
                'params' => $this->data->get("params.$k"),
            ];

            $this->records['all-useage-time'] += $useageTime;

            if ($type) {
                $typeTimesKey = $type . '-times';

                $this->records[$typeTimesKey] = empty($this->records[$typeTimesKey]) ? 1 : ($this->records[$typeTimesKey] + 1);
            }
        }
        return $this;
    }

    /**
     * @return array|mixed
     */
    public function all()
    {
        if (empty($this->records)) {
            $this->compute();
        }
        return ! empty($this->records['info']) ? $this->records['info'] : [];
    }

    /**
     * @return array
     */
    public function computes()
    {
        if (empty($this->records)) {
            $this->compute();
        }
        return $this->records;
    }

}

