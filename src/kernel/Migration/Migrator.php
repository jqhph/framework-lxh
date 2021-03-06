<?php

namespace Lxh\Migration;

use Lxh\Migration\Database\TableHelper;
use Lxh\Migration\Exceptions\InvalidArgumentException;
use Phinx\Db\Adapter\AdapterFactory;
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Table as PhinxTable;

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
        $table = new TableHelper($this->table($tableName));

        $callback($table);

        $table->done();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function table($tableName, $options = [])
    {
        return new PhinxTable($tableName, $options, $this->getAdapter());
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
            'adapter'      => getvalue($config, 'type', 'mysql'),
            'host'         => getvalue($config, 'host', 'localhost'),
            'name'         => getvalue($config, 'name'),
            'user'         => getvalue($config, 'user', 'root'),
            'pass'         => getvalue($config, 'pwd', ''),
            'port'         => getvalue($config, 'port', 3306),
            'charset'      => getvalue($config, 'charset', 'utf8'),
            'table_prefix' => getvalue($config, 'prefix'),
        ];

        $dbConfig['default_migration_table'] = 'phinxlog';

        return $dbConfig;
    }

}
