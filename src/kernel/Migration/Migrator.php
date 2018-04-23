<?php

namespace Lxh\Migration;

use Lxh\Migration\Database\Table;
use Lxh\Migration\Exceptions\InvalidArgumentException;
use Phinx\Db\Adapter\AdapterFactory;
use Phinx\Migration\AbstractMigration;

class Migrator extends AbstractMigration
{
    /**
     * 切换库
     *
     * @var string
     */
    protected $adapterConfigKeyName;

    /**
     * @param string $tableName
     * @param \Closure $callback
     * @return $this
     */
    public function tableHelper($tableName, $callback = null)
    {
        $table = new Table($this->table($tableName));

        $callback($table);

        $table->done();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function table($tableName, $options = [])
    {
        return new Table($tableName, $options, $this->getAdapter());
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        if ($this->adapterConfigKeyName) {
            $this->adapterConfigKeyName = false;

            $options = $this->getDbConfig();

            $adapter = AdapterFactory::instance()->getAdapter($options['adapter'], $options);

            if ($adapter->hasOption('table_prefix') || $adapter->hasOption('table_suffix')) {
                $adapter = AdapterFactory::instance()->getWrapper('prefix', $adapter);
            }

            $this->adapter = $adapter;
        }

        return $this->adapter;
    }

    /**
     * 获取数据库配置
     *
     * @return array
     */
    protected function getDbConfig()
    {
        $config = config('db.'.$this->adapterConfigKeyName);

        if ($config) {
            throw new InvalidArgumentException;
        }
        $dbConfig = [
            'adapter'      => get_value($config, 'type', 'mysql'),
            'host'         => get_value($config, 'host', 'localhost'),
            'name'         => get_value($config, 'name'),
            'user'         => get_value($config, 'user', 'root'),
            'pass'         => get_value($config, 'pwd', ''),
            'port'         => get_value($config, 'port', 3306),
            'charset'      => get_value($config, 'charset', 'utf8'),
            'table_prefix' => get_value($config, 'prefix'),
        ];

        $dbConfig['default_migration_table'] = 'phinxlog';

        return $dbConfig;
    }

}
