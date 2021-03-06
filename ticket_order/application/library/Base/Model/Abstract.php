<?php

/**
 * 数据模型基类
 * @author  mosen
 */
abstract class Base_Model_Abstract
{
    protected $dbname;
    protected $tblname = '';
    protected $pkKey;
    protected $memSrvKey = 'default';
    protected $rdsSrvKey = 'default';
    protected $preCacheKey = 'cache|';
    protected $cacheKey;
    protected $cd = 3600;
    protected $autoShare = 1;
    protected static $instances = array();
    
    public static function model() {
        $className = get_called_class();
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className();
        }
        return self::$instances[$className];
    }

    public function setAutoShare($val=1) {
        $this->autoShare = $val;
    }
    
    public function getTable() {
        return $this->tblname;
    }

    public function setTable($id = 0) {
        return $this;
    }
    
    /**
     * [__get description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function __get($name) {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) return $this->$getter();
        else if (Yaf_Registry::has($name)) return Yaf_Registry::get($name);
        
        throw new Exception("Property " . get_called_class() . ".{$name} is not defined.");
    }
    
    /**
     * [__set description]
     * @param [type] $name  [description]
     * @param [type] $value [description]
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) return $this->$setter($value);
        else Yaf_Registry::set($name, $value);
    }
    
    /**
     * [__isset description]
     * @param  [type]  $name [description]
     * @return boolean       [description]
     */
    public function __isset($name) {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) return $this->$getter() !== null;
        
        return Yaf_Registry::has($name);
    }
    
    /**
     * [__unset description]
     * @param [type] $name [description]
     */
    public function __unset($name) {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) $this->$setter(null);
        else Yaf_Registry::del($name);
    }
    
    /**
     * [__call description]
     * @param  [type] $name      [description]
     * @param  [type] $arguments [description]
     * @return [type]            [description]
     */
    public function __call($name, $arguments) {
        if (method_exists($this, $name)) return call_user_method_array($name, $this, $arguments);
        else if (method_exists($this->getRequest(), $name)) return call_user_method_array($name, $this->getRequest(), $arguments);
        
        throw new Exception("Method " . get_called_class() . ".{$name} is not defined.");
    }
    
    /**
     * [getDb description]
     * @return [type] [description]
     */
    public function getDb() {
        $key = 'db|' . $this->dbname;
        if (!Yaf_Registry::has($key)) Yaf_Registry::set($key, Db_Mysql::factory($this->dbname));
        
        return Yaf_Registry::get($key);
    }
    
    /**
     * [getRedis description]
     * @return [type] [description]
     */
    public function getRedis() {
        $key = 'redis|' . $this->rdsSrvKey;
        if (!Yaf_Registry::has($key)) Yaf_Registry::set($key, Cache_Redis::factory($this->rdsSrvKey));
        
        return Yaf_Registry::get($key);
    }
    
    /**
     * [getMemcache description]
     * @return [type] [description]
     */
    public function getMemcache() {
        $key = 'memcache|' . $this->memSrvKey;
        if (!Yaf_Registry::has($key)) Yaf_Registry::set($key, Cache_Memcache::factory($this->memSrvKey));
        
        return Yaf_Registry::get($key);
    }
    
    /**
     * [getCacheKey description]
     * @param  [type] $key         [description]
     * @param  [type] $preCacheKey [description]
     * @return [type]              [description]
     */
    protected function getCacheKey($cacheKey, $preCacheKey = null) {
        if ($preCacheKey === null) $preCacheKey = $this->preCacheKey;
        
        if (is_array($cacheKey)) {
            return $preCacheKey . md5(serialize($cacheKey));
        }
        // echo $preCacheKey . $cacheKey." ";
        return $preCacheKey . $cacheKey;
    }
    
    public function setCacheKey($key = null) {
        if ($this->preCacheKey)
            $this->cacheKey = $key;
        return $this;
    }
    
    public function getCache($cacheKey, $preCacheKey = null) {
        return $this->memcache->get($this->getCacheKey($cacheKey, $preCacheKey));
    }
    
    public function getCacheList($cacheKeys) {
        return $this->memcache->mget($cacheKeys);
    }
    
    public function addCache($cacheKey, $data, $cd, $preCacheKey = null) {
        return $this->memcache->set($this->getCacheKey($cacheKey, $preCacheKey), $data, $cd);
    }
    
    public function delCache($cacheKey, $preCacheKey = null) {
        $this->memcache->delete($this->getCacheKey($cacheKey, $preCacheKey));
        return true;
    }

    public function delCacheList($ids, $preCacheKey = null) {
        foreach($ids as $id)
            $this->memcache->delete($this->getCacheKey(array($this->pkKey=>$id), $preCacheKey));
        return true;
    }
    
    public function getCacheNS($preCacheKey = null) {
        $ns = $this->memcache->get($this->getCacheKey('NS', $preCacheKey));
        if (!$ns) {
            $ns = microtime(true);
            $this->setCacheNS($ns);
        }
        return $ns;
    }
    
    private function setCacheNS($ns = null, $preCacheKey = null) {
        if (!$ns) $ns = microtime(true);
        return $this->memcache->set($this->getCacheKey('NS', $preCacheKey), $ns, $this->cd);
    }
    
    public function setListKey($key = null) {
        $this->db->setListKey($key);
        return $this;
    }

    public function setGroupBy($groupBy = null){
        $this->db->setGroupBy($groupBy);
        return $this;
    }

    public function get($where, $fields = '*', $preCacheKey = null) {
        $data = false;
        if ($this->cacheKey) {
            $data = $this->getCache($this->cacheKey, $preCacheKey);
        }
        if ($data === false) {
            $data = $this->db->get($this->getTable(), $where, $fields);
            if ($this->cacheKey) {
                $this->addCache($this->cacheKey, $data, $this->cd, $preCacheKey);
            }
        }
        $this->cacheKey = null;
        $this->db->setGroupBy(null);
        return $data;
    }
    
    public function getList($where, $fields = '*', $preCacheKey = null) {
        if ($this->cacheKey) {
            $data = array();
            
            $cacheKeys = array();
            foreach ($this->cacheKey as $value) {
                $cacheKeys[$value] = $this->getCacheKey(array($this->pkKey => $value), $preCacheKey);
            }
            $cacheData = $this->getCacheList($cacheKeys);
            foreach ($cacheKeys as $key => $cacheKey) {
                
                //有缓存数据 则去除
                if (isset($cacheData[$cacheKey])) {
                    $data[$key] = $cacheData[$cacheKey];
                    unset($cacheKeys[$key]);
                }
            }
            unset($cacheData);
            
            //cacheKeys存在 有key未命中 查询数据库
            if (!empty($cacheKeys)) {
                $column = reset(array_keys($where));
                $where[$column] = array_keys($cacheKeys);
                $this->setListKey($this->pkKey);
                $list = $this->db->select($this->getTable(), $where, $fields);
                if ($list) {
                    foreach ($list as $key => $val) {
                        $data[$key] = $val;
                    }
                }
                foreach ($cacheKeys as $key => $cacheKey) {
                    if (isset($data[$key])) {
                        $this->addCache($cacheKey, $data[$key], $this->cd, '');
                    }
                }
            }
            
            $this->cacheKey = null;
            return $data;
        }
        return $this->db->select($this->getTable(), $where, $fields);
    }

    public function select($where, $fields = '*', $order = null, $limit = null) {
        $data = false;
        if ($this->cacheKey) {
            $preCacheKey = $this->preCacheKey . $this->getCacheNS() . '|';
            $data = $this->getCache($this->cacheKey, $preCacheKey);
        }
        if ($data === false) {
            $data = $this->db->select($this->getTable(), $where, $fields, $order, $limit);
            
            if ($this->cacheKey && $data) {
                $this->addCache($this->cacheKey, $data, $this->cd, $preCacheKey);
            }
        }
        $this->cacheKey = null;
        $this->db->setGroupBy(null);
        return $data;
    }
    
    public function insert($data) {
        $rt = false;
        if(!empty($data)) {
            $mult = false;
            foreach ($data as $key => $val) {
                if (is_numeric($key)) {
                    $mult = true;
                }
                break;
            }
            $total = count($data);
            if($mult===true && $total>301) { //超过300条数据就分页插入
                $pageRow = 300;
                $fields = array_shift($data);
                $totalPage = ceil($total/$pageRow);
                for($page=1;$page<=$totalPage;$page++) {
                    $offset = ($page-1)*$pageRow;
                    $splitData = array_slice($data,$offset,$pageRow);
                    if(!empty($splitData)) {
                        array_unshift($splitData,$fields);
                        $rt = $this->db->insert($this->getTable(), $splitData);
                        if(!$rt) return false;
                    }
                }

            } else {
                $rt = $this->db->insert($this->getTable(), $data);
            }
        }

        //$rt = $this->db->insert($this->getTable(), $data);
        if ($rt) {
            return $this->setCacheNS();
        }
        return $rt;
    }
    
    protected function replace($data) {
        $rt = $this->db->replace($this->getTable(), $data);
        if ($rt) {
            return $this->setCacheNS();
        }
        return $rt;
    }
    
    public function getInsertId() {
        return $this->db->getInsertId();
    }
    
    public function update($data, $where = null) {
        $rt = $this->db->update($this->getTable(), $data, $where);
        if ($rt && $this->cacheKey) {
            $this->delCache($this->cacheKey);
            $this->cacheKey = null;
        }
        return $rt && $this->setCacheNS();
    }

    public function updateByAttr($data, $where) {
        $list = $this->setListKey($this->pkKey)->select($where, $this->pkKey);
        $rt = $this->update($data, $where);
        if ($rt && $list) {
            $this->delCacheList(array_keys($list));
        }
        return $rt && $this->setCacheNS();
    }
    
    public function delete($where) {
        $rt = $this->db->delete($this->getTable(), $where);
        if ($rt && $this->cacheKey) {
            $this->delCache($this->cacheKey);
            $this->cacheKey = null;
        }
        return $rt && $this->setCacheNS();
    }
    
    public function begin() {
        return $this->db->begin();
    }
    
    public function commit() {
        $this->memcache->commit();
        $this->redis->commit();
        Cache_Redis::factory('queue')->commit();
        return $this->db->commit();
    }
    
    public function rollback() {
        $this->memcache->rollback();
        $this->redis->cleanAll();
        return $this->db->rollback();
    }

    public function getById($id) {
        if($this->autoShare) $this->setTable($id);
        $this->setCacheKey(array($this->pkKey => $id));
        return $this->get(array($this->pkKey=>$id));
    }
    
    public function getByIds($ids) {
        if($this->autoShare) $this->setTable(reset($ids));
        $this->setCacheKey($ids);
        return $this->getList(array($this->pkKey.'|in'=>$ids));
    }
    
    public function search($where, $fields = '*', $order = null, $limit = null, $listKey = '') {
        $this->setCacheKey(array($where, $fields, $order, $limit, $listKey));
        $listKey || $listKey = $this->pkKey;
        return $this->setListKey($listKey)->select($where, $fields, $order, $limit);
    }
    
    public function add($data) {
        if($this->autoShare) $this->setTable();
        return $this->insert($data);
    }
    
    public function updateById($id, $data) {
        if($this->autoShare) $this->setTable($id);
        $this->setCacheKey(array($this->pkKey => $id));
        return $this->update($data, array($this->pkKey=>$id));
    }
    
    public function deleteById($id) {
        if($this->autoShare) $this->setTable($id);
        $this->setCacheKey(array($this->pkKey => $id));
        return $this->delete(array($this->pkKey=>$id));
    }

    public function countResult($where){
        $r = $this->search($where,"count(*) as count");
        if(is_array($r) && count($r)>0) {
            $r=reset($r);
        } else {
            return 0;
        }
        if(is_array($r) && count($r)>0) {
            $r=reset($r);
            return intval($r);
        } else {
            return 0;
        }
    }

    public function exec($sql) {
        $rt = $this->db->exec($sql);
        if ($rt > 0) {
            $this->setCacheNS();
        }
        return $rt;
    }

    public function setCd($sec = 3600){
        $this->cd = $sec;
        return $this;
    }
}
