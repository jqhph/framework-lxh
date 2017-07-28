<?php
namespace Lxh\ORM\Driver\Mysql;

use Lxh\ORM\Connect\PDO;

class Base 
{
    protected $defaultConnectionType = 'pdo';
    
    protected $connectionType = 'pdo';
    
    protected $connections = [];
    
	
    protected function getOrderBySql()
    {
        return $this->orderBy;
    }
    
    protected function getLeftJoinSql()
    {
        if (count($this->leftJoin) > 0) {
            return implode(' ', $this->leftJoin);
        }
    }
	
	/**
	 * 获取where字符串
	 * */
    public function getWhereSql($isHaving = false, $isOrWhere = false)
    {
        $where  = '';
        $data   = [];
        $orData = [];
        
        $t = ' WHERE ';
        
        if ($isHaving) {
            $data   = & $this->having;
            $orData = & $this->orHaving;
            
            $t = ' HAVING ';
        } else {
            $data   = & $this->wheres;
            $orData = & $this->orWheres;
        }
        
        if (count($data) > 0) {
            $where .= implode(' AND ', $data);
        }
        
        if (count($orData) > 0) {
            if ($where) {
            	$where .= ' OR ';
            }
            $where .= '(' . implode(' AND ', $orData) . ')';
        		
        }
        
        if ($where) {
            $where = $t . $where;
        }

        return $where;
    
    }
	
	
    protected function getFieldsSql()
    {
        return $this->field ? rtrim($this->field, ', ') : '* ';
    }
	
    protected function getLimitSql()
    {
        return $this->limit;
    }
	
    protected function clear()
    {
        $this->tableName = null;
        $this->field     = null;
        $this->limit 	 = null;
        $this->orderBy	 = null;
        $this->groupBy	 = null;
        
        $this->whereData  = [];
        $this->havingData = [];
        $this->leftJoin   = [];
        $this->wheres     = [];
        $this->orWheres   = [];
        $this->having	  = [];
        $this->orHaving   = [];
    }
	
    protected function getGroupBySql()
    {
        return $this->groupBy;
    }
	
	/**
	 * 设置连接数据库类型方法
	 *
	 * @date   2016-11-8 上午10:11:26
	 * @author jqh
	 * @param  string $type
	 * @return
	 */
	public function setConnectionType($type = 'pdo')
	{
		$this->connectionType = $type;
		return $this;
	}
	
    public function getConnection() 
    {
	    if (isset($this->connections[$this->connectionType])) {
	    	return $this->connections[$this->connectionType];
	    }
	    switch ($this->connectionType) {
	    	case $this->defaultConnectionType:
		        $this->connections[$this->defaultConnectionType] = $this->container->make('pdo');
		        break;
	    	default:
	    	    $this->connections[$this->connectionType] = new PDO(config($this->connectionType));
	    	    break;  
	    }
	    
	    return $this->connections[$this->connectionType];
    }
}
