<?php
namespace Flywheel\Model;
use Flywheel\Db\Exception;
use Flywheel\Db\Type\DateTime;
use Flywheel\Event\Event as Event;
use Flywheel\Validator\Util as ValidatorUtil;
use Flywheel\Db\Expression;
use Flywheel\Util\Inflection;
use Flywheel\Db\Connection;
use Flywheel\Db\Manager;

abstract class ActiveRecord extends \Flywheel\Object {
    protected static $_tableName;
    protected static $_phpName;
    protected static $_pk;
    protected static $_dbConnectName;
    protected static $_validate = array();
    protected static $_cols = array();
    protected static $_schema = array();
    protected static $_alias;
    protected static $_instances = array();
    protected static $_validators;
    protected static $_init = false;
    protected static $_readMode = Manager::__SLAVE__;
    protected static $_writeMode = Manager::__MASTER__;

    /**
     * status of deleted om. If object had been delete from database
     * @var bool
     */
    private $_deleted = false;

    /**
     * status of new object. If object not store in database, this value is true
     * @var $_new boolean
     */
    private $_new = true;

    public $validate = array();

    protected $_data = array();
    protected $_modifiedCols = array();
    protected $_validationFailures = array();
    protected $_valid = false;

    public function __construct($data = null, $isNew = true) {
        $this->setTableDefinition();
        $this->_initDataValue();
        $this->init();

        if (!empty($data)) {
            $this->hydrate($data);
        }

        $this->setNew($isNew);

        if (!self::$_init) {
            self::$_validate = array_merge_recursive(self::$_validate, self::additionRule());
            self::$_init = true;
        }
    }

    public function setTableDefinition() {}

    public function additionRule() {
        return array();
    }

    protected function _initDataValue() {
        foreach (static::$_schema as $c => $config) {
            if (!isset($this->_data[$c])) {
                $this->_data[$c] = (isset($config['default']))? $config['default'] : null;
            } else {
                $this->_data[$c] = static::fixData($this->_data[$c], static::$_schema[$c]);
            }
        }
    }

    public function init() {}

    public static function setReadMode($mode) {
        if ($mode != Manager::__MASTER__ && $mode != Manager::__SLAVE__)
            throw new Exception("Read mode {$mode} is not allowed.");
        self::$_readMode = $mode;
    }

    public static function setWriteMode($mode) {
        if ($mode != Manager::__MASTER__ && $mode != Manager::__SLAVE__)
            throw new Exception("Write mode {$mode} is not allowed.");
        self::$_writeMode = $mode;
    }

    public static function getReadMode() {
        return self::$_readMode;
    }

    public static function getWriteMode() {
        return self::$_writeMode;
    }

    public static function create() {
        return new static();
    }

    public static function setTableName($tblName) {
        static::$_tableName = $tblName;
    }

    public static function getTableName() {
        return static::$_tableName;
    }

    public static function setPhpName($phpName) {
        static::$_phpName = $phpName;
    }

    public static function getPhpName() {
        return static::$_phpName;
    }

    public static function setTableAlias($alias) {
        static::$_alias = $alias;
    }

    public static function getTableAlias() {
        return static::$_alias;
    }

    public static function setPrimaryKeyField($field) {
        static::$_pk = $field;
    }

    public static function getPrimaryKeyField() {
        return static::$_pk;
    }

    public static function setDbConnectName($dbName) {
        static::$_dbConnectName = $dbName;
    }

    public static function getDbConnectName() {
        return static::$_dbConnectName;
    }

    public static function quote($name) {
        return self::getReadConnection()->getAdapter()->quoteIdentifier($name);
    }

    public static function getColumnsList($alias = null) {
        $db = self::getReadConnection();

        $list = array();
        for($i = 0, $size = sizeof(static::$_cols); $i < $size; ++$i) {
            $list[] = ((null != $alias)? $alias .'.' : '') .$db->getAdapter()->quoteIdentifier(static::$_cols[$i]);
        }
        return implode(',', $list);
    }

    /**
     * get write database connection (slave)
     * @return \Flywheel\Db\Connection
     */
    public static function getWriteConnection() {
        return Manager::getConnection(self::getDbConnectName(), self::getWriteMode());
    }

    /**
     * get read database connection (slave)
     * @return \Flywheel\Db\Connection
     */
    public static function getReadConnection() {
        return Manager::getConnection(self::getDbConnectName(), self::getReadMode());
    }

    /**
     * create read query
     * @return \Flywheel\Db\Query
     */
    public static function read() {
        return self::getReadConnection()->createQuery()->from(static::getTableName());
    }

    /**
     * create write query
     * @return \Flywheel\Db\Query
     */
    public static function write() {
        return self::getWriteConnection()->createQuery()->from(static::getTableName());
    }

    /**
     * return the named attribute value.
     * if this is a new record and the attribute is not set before, the default column value will be returned.
     * if this record is the result of a query and the attribute is not loaded, null will be returned.     *
     * @param string $name the attribute name
     * @return mixed
     */
    public function getAttribute($name) {
        if (property_exists($this, $name))
            return $this->$name;
        else if (isset($this->_data[$name]))
            return $this->_data[$name];

        return null;
    }

    /**
     * return all column attribute values.
     *
     * @param mixed $names names of attributes whose value need to be returned.
     * if this null (default), them all attribute values will be returned
     * if this is a array
     * @return array
     */
    public function getAttributes($names = null) {
        if (null == $names)
            return $this->_data;

        if (is_string($names)) {
            $names = explode(',', $names);
        }

        $attr = array();
        if (is_array($names)) {
            for ($i = 0, $size = sizeof($names); $i < $size; ++$i) {
                $names[$i] = trim($names[$i]);
                if (property_exists($this, $names[$i]))
                    $attr[$names[$i]] = $this->$names[$i];
                else
                    $attr[$names[$i]] = isset($this->_data[$names[$i]])? $this->_data[$names[$i]] : null;
            }
        }

        return $attr;
    }

    public function hasField($field) {
        return isset(static::$_schema[$field]);
    }

    /**
     * reload object data from db
     * @return bool
     * @throws Exception
     */
    public function reload() {
        $data = static::write()->where(static::getPrimaryKeyField() .'= :pk')
            ->setMaxResults(1)
            ->setParameter(':pk', $this->getPkValue())
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            $this->hydrate($data);
            return true;
        }
        throw new Exception('Reload fail!');
    }

    /**
     * get primary key field
     *
     * @return mixed
     */
    public function getPkValue() {
        return $this->{static::getPrimaryKeyField()};
    }

    public function setValidationFailure($column, $mess)
    {
        if (!isset($this->_validationFailures[$column])) {
            $this->_validationFailures[$column] = array();
        }
        $this->_validationFailures[$column][] = $mess;
    }

    /**
     * @return array
     */
    public function getValidationFailures()
    {
        return $this->_validationFailures;
    }

    /**
     * @param string $sep
     * @return null|string
     */
    public function getValidationFailuresMessage($sep = "<br />") {
        if (!empty($this->_validationFailures)) {
            $r = array();
            foreach ($this->_validationFailures as $col => $mess) {
                $r[] = $col .' : ' .implode($sep, $mess);
            }
            return implode($sep, $r);
        }
        return null;
    }

    protected function _beforeSave() {
        $this->getEventDispatcher()->dispatch('onBeforeSave', new Event($this));
    }

    protected function _afterSave() {
        $this->getEventDispatcher()->dispatch('onAfterSave', new Event($this));
    }

    protected function _beforeDelete() {
        $this->getEventDispatcher()->dispatch('onBeforeDelete', new Event($this));
    }

    protected function _afterDelete() {
        $this->getEventDispatcher()->dispatch('onAfterDelete', new Event($this));
    }

    protected function _beforeValidate() {
        $this->getEventDispatcher()->dispatch('onBeforeValidate', new Event($this));
    }

    protected function _afterValidate() {
        $this->getEventDispatcher()->dispatch('onAfterValidate', new Event($this));
    }

    /**
     * is object did not store in database
     *
     * @return boolean
     */
    public function isNew() {
        return $this->_new;
    }

    /**
     * Set New
     * @param boolean $isNew
     */
    public function setNew($isNew) {
        $this->_new = (boolean) $isNew;
    }

    public function getModifiedCols() {
        return array_keys($this->_modifiedCols);
    }

    public function isColumnModified($col) {
        return isset($this->_modifiedCols[$col]);
    }

    public function hasColumnsModified() {
        return (bool) sizeof($this->_modifiedCols);
    }

    /**
     * @return bool
     */
    public function isValid() {
        return $this->_valid;
    }

    /**
     * To Array
     *
     * @param bool $raw return array with all
     *          object's property neither only column field
     *
     * @return array
     */
    public function toArray($raw = false) {
        if (true === $raw) {
            return get_object_vars($this);
        }

        return $this->_data;
    }

    /**
     * To Json
     *
     * @param bool $raw
     *
     * @return string in JSON format
     */
    public function toJSon($raw = false) {
        if (true === $raw) {
            return json_encode($this);
        }

        return json_encode($this->_data);
    }

    /**
     * hydrate data to object
     *
     * @param object | array $data
     */
    public function hydrate($data) {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        foreach ($data as $p=>$value) {
            if (isset(static::$_schema[$p])) {
                $this->_modifiedCols[$p] = true;
                $this->_data[$p] = $this->fixData($value, static::$_schema[$p]);
            } else {
                $this->$p = $value;
            }
        }
    }

    /**
     * hydrate json to om
     * @param $json
     */
    public function hydrateJSON($json) {
        $data = json_decode($json, true);

        $this->hydrate($data);
    }

    public function isDeleted() {
        return $this->_deleted;
    }

    /**
     * fix data matche collumn data defined
     * @param $data
     * @param $config
     * @return bool|float|int|null|string|\Flywheel\Db\Expression
     */
    public function fixData($data, $config) {
        if ($data instanceof \Flywheel\Db\Expression) {
            return $data;
        }

        $type = $config['type'];
        if (null !== $data) {
            switch ($type) {
                case 'integer':
                    return (int) $data;
                case 'float':
                case 'number':
                case 'decimal':
                    return (float) $data;
                case 'double' :
                    return (double) $data;
                case 'time':
                case 'timestamp':
                case 'date':
                case 'datetime':
                    if ($data instanceof \DateTime) {
                        $data = new DateTime($data->format('Y-m-d H:i:s'));
                    }
                    if ($data instanceof DateTime) {
                        return $data;
                    }
                    return new DateTime($data);
                case 'blob':
                case 'string':
                    return (string) $data;
                case 'boolean':
                case 'bool':
                    return (bool) $data;
                case 'array':
                    if (is_scalar($data)) {
                        $data = json_decode($data);
                    }
                    return $data;
            }
        } else {
            if (!isset(self::$_validate[$config['name']])
                || !isset(self::$_validate[$config['name']]['require'])) {
                if ('bool' == $type)
                    return 0;

                if ('array' == $type)
                    return array();
            } else {
                switch ($type) {
                    case 'integer':
                    case 'int':
                    case 'float':
                    case 'decimal':
                    case 'double':
                    case 'number':
                        return 0;
                    case 'timestamp':
                        return new DateTime('0000-00-00 00:00:00');
                    case 'time':
                        return new DateTime('00:00:00');
                    case 'date':
                        return new DateTime('0000-00-00');
                    case 'datetime':
                        return new DateTime('0000-00-00 00:00:00');
                    case 'blob':
                    case 'string':
                        return '';
                    case 'boolean':
                    case 'bool':
                        return 0;
                    case 'array':
                        return array();
                }
            }
        }

        return null;
    }

    /**
     * Removes errors for all attributes or a single attribute.
     * @param string $attribute attribute name. Use null to remove errors for all attribute.
     */
    public function clearErrors($attribute=null) {
        if($attribute===null)
            $this->_validationFailures = array();
        else
            unset($this->_validationFailures[$attribute]);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function saveToDb() {
        if (!$this->validate()) {
            return false;
        }

        $data = $this->getAttributes($this->getModifiedCols());
        foreach($data as $c => &$v) {
            if (is_array($v)) {
                $v = json_encode($v);
            } else if ($v instanceof DateTime) {
                $v = $v->toString();
            } else {
                $v = $this->fixData($v, static::$_schema[$c]);
            }
        }

        $db = self::getWriteConnection();
        $databind = $this->_populateStmtValues($data);
        if ($this->isNew()) { //insert new record
            $status = $db->insert(static::getTableName(), $data, $databind);
            if (!$status) {
                throw new Exception('Insert record did not succeed!');
            }
            $this->{static::getPrimaryKeyField()} = $db->lastInsertId();
        } else {
            $db->update(static::getTableName(), $data, array(static::getPrimaryKeyField() => $this->getPkValue()), $databind);
        }

        $this->_modifiedCols = array();
        $this->setNew(false);
        return true;
    }

    protected function _populateStmtValues(&$data) {
        $databind = array();
        $c = $data;
        foreach ($c as $n => $v) {
            if (!($v instanceof Expression)) {
                if (null == $v && (!isset(static::$_validate[$n]) || !isset(static::$_validate[$n]['require']))) {
                    unset($data[$n]); // no thing
                } else {
                    $databind[] = self::getReadConnection()->getAdapter()->getPDOParam(static::$_schema[$n]['db_type']);
                }
            }
        }

        return $databind;
    }

    /**
     * delete object from database
     *
     * @return int
     * @throws Exception
     */
    public function deleteFromDb() {
        if ($this->isNew()) {
            throw new Exception('Record has been not saved in to database, cannot delete!');
        }

        $pkField = static::getPrimaryKeyField();
        $affectedRows = self::getWriteConnection()->delete(static::getTableName(), array($pkField, $this->getPkValue()));
        if ($affectedRows)
            $this->_deleted = true;
        return $affectedRows;
    }

    /**
     * begin transaction
     * @return bool
     */
    public function beginTransaction() {
        return self::getWriteConnection()->beginTransaction();
    }

    /**
     * commit transaction
     * @return bool
     */
    public function commit() {
        return self::getWriteConnection()->commit();
    }

    /**
     * rollBack
     * @return bool
     */
    public function rollBack() {
        return self::getWriteConnection()->rollBack();
    }

    abstract public function save();
    abstract public function delete();

    public function validate() {
        $this->clearErrors();
        $this->_beforeValidate();
        $unique = array();
        foreach (static::$_validate as $name => $rules) {
            $isNull = false;
            //check not null
            if (isset($rules['require']) && ValidatorUtil::isEmpty($this->$name)) {
                if (isset(static::$_schema[$name]['default']) && null != static::$_schema[$name]['default']) {
                    $this->$name = static::$_schema[$name]['default'];
                } else {
                    $isNull = true;
                    $this->setValidationFailure($name, $rules['require']); //$rules['require'] store message
                }
            }

            //check allow value for enum type
            if (!$isNull && isset($rules['filter']) && !ValidatorUtil::isEmpty($this->$name)
                && !in_array($this->$name, $rules['filter']['allow'])) {
                $this->setValidationFailure($name, $rules['filter']['message']);
            }

            if (!$isNull && isset($rules['length']) && 'string' == static::$_schema[$name]['type']
                && !ValidatorUtil::isEmpty($this->$name) && mb_strlen($this->$name) > $rules['length']['max']) {
                $this->setValidationFailure($name, $rules['length']['message']);
            }

            //check unique
            if (isset($rules['unique']))
                $unique[$name] = $rules;
			
			//check type
            if(isset($rules['type'])){
                //is_numeric()
                switch ($rules['type']){
                    case 'number':
                        if(!is_numeric($this->$name))
                            $this->setValidationFailure($name,$name.' must be a number');
                        break;
                    case 'email':
                        if(false === ValidatorUtil::isValidEmail($this->$name)){
                            $this->setValidationFailure($name,$name.' must be a email');
                        }
                        break;
                }
            }

            //check patent
            if(isset($rules['pattern'])){
                if(!preg_match($rules['pattern'], $this->$name)){
                    $this->setValidationFailure($name, $name.' does not matched pattern');
                }
            }
        }

        if (!empty($unique)) {
            $where = array();
            $params = array();
            foreach ($unique as $name => $mess) {
                $where[] = static::getTableName().".{$name} = ?";
                $params[] = $this->$name;
            }

            if (!$this->isNew()) {
                $where[] = static::getTableName().'.' .static::getPrimaryKeyField() .' != ?';
                $params[] = static::getPkValue();
            }
            $where = implode(' AND ', $where);

            $fields = array_keys($unique);

            foreach ($fields as &$field) {
                $field = $this->quote($field);
            }

            $data = static::read()->select(implode(',', $fields))
                ->where($where)
                ->setMaxResults(1)
                ->setParameters($params)
                ->execute()
                ->fetch(\PDO::FETCH_ASSOC);

            if ($data) {
                foreach ($data as $field => $value) {
                    if($this->$field == $value)
                        $this->setValidationFailure($field, static::$_validate[$field]['unique']);
                }
            }
        }

        $this->_afterValidate();
        $this->_valid = empty($this->_validationFailures);
        return $this->isValid();
    }

    /**
     * add instance to pool
     * @static
     * @param $obj
     * @param null $key
     * @return bool
     */
    public static function addInstanceToPool($obj, $key = null) {
        $lbClass = get_called_class();
        if (!$obj instanceof $lbClass) {
            return false;
        }

        /* @var ActiveRecord $obj */
        if (null == $key) {
            $key = $obj->getPkValue();
        }

        static::$_instances[$key] = $obj;
        return true;
    }

    /**
     * get instance from pool by key
     * @static
     * @param $key
     * @return get_called_class() | null
     */
    public static function getInstanceFromPool($key) {
        return isset(static::$_instances[$key])? static::$_instances[$key] : null;
    }

    /**
     * get all instances from pool by key
     * @static
     * @return get_called_class()[] | null
     */
    public static function getInstancesFromPool() {
        return static::$_instances;
    }

    /**
     * remove instance in pool by $key
     * @static
     * @param $key
     */
    public static function removeInstanceFromPool($key) {
        unset(static::$_instances[$key]);
    }

    /**
     * clear pool
     * @static
     */
    public static function clearPool() {
        static::$_instances = array();
    }

    /**
     * Resolves the passed find by field name inflecting the parameter.
     *
     * @param string $name
     * @return string $fieldName
     */
    protected static function _resolveFindByFieldName($name) {
        $fieldName = Inflection::camelCaseToHungary($name);
        if (isset(static::$_schema[$fieldName])) {
            return (static::getTableAlias()? static::getTableAlias() .'.' :'')
                .self::getReadConnection()->getAdapter()->quoteIdentifier($fieldName);
        }

        return false;
    }

    public static function buildFindByWhere($fieldName) {
        if ('' == $fieldName || 1 == $fieldName || '*' == $fieldName || 'all' == strtolower($fieldName))
            return '1';

        $ands = array();
        $e = explode('And', $fieldName);
        foreach ($e as $k => $v) {
            $and = '';
            $e2 = explode('Or', $v);
            $ors = array();
            foreach ($e2 as $k2 => $v2) {
                if ($v2 = static::_resolveFindByFieldName($v2)) {
                    $ors[] = $v2 . ' = ?';
                } else {
                    throw new Exception('Invalid field name to find by: ' . $v2);
                }
            }
            $and .= implode(' OR ', $ors);
            $and = count($ors) > 1 ? '(' . $and . ')':$and;
            $ands[] = $and;
        }
        $where = implode(' AND ', $ands);
        return $where;
    }

    public static function findAll() {
        static::create();
        return self::getReadConnection()->createQuery()
            ->select(static::getColumnsList(static::getTableAlias()))
            ->from(static::getTableName(), static::getTableAlias())
            ->execute()
            ->fetchAll(\PDO::FETCH_CLASS, static::getPhpName(), array(null, false));
    }

    public static function findBy($by, $param = null, $first = false) {
        static::create();
        $q = self::getReadConnection()->createQuery()
            ->select(static::getColumnsList(static::getTableAlias()))
            ->from(static::getTableName(), static::getTableAlias())
            ->where(static::buildFindByWhere($by));
        if ($first)
            $q->setMaxResults(1);

        foreach ($param as &$p) {//toString datetime object
            if ($p instanceof DateTime) {
                $p = $p->format('Y-m-d H:i:s');
            }
        }

        if (null != $param)
            $q->setParameters($param);

        $stmt = $q->execute();

        $result = array();
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $om = new static($row, false);
            if ($first) {
                return $om;
            }

            $result[] = $om;
        }

        return (!empty($result))? $result : null;
    }

    public static function retrieveBy($by, $param) {
        static::create();
        $field = Inflection::camelCaseToHungary($by);
        if ($by == static::getPrimaryKeyField()) {
            if (null != ($obj = static::getInstanceFromPool($param[0]))) {
                return $obj;
            }
        } else {
            $objs = static::getInstancesFromPool();
            foreach($objs as $obj) {
                if ($obj->{$by} == $param[0]) {
                    return $obj;
                }
            }
        }

        $obj = self::findBy($by, $param, true);
        if ($obj) {
            self::addInstanceToPool($obj, static::getPrimaryKeyField());
            return $obj;
        }

        return false;
    }

    public function __call($method, $params) {
        if (strrpos($method, 'set') === 0
            && isset($params[0]) && null !== $params[0]) {
            $name = Inflection::camelCaseToHungary(substr($method, 3, strlen($method)));

            if (isset(static::$_cols[$name])) {
                $this->_data[$name] = $this->fixData($params[0], static::$_schema[$name]);
                $this->_modifiedCols[$name] = true;
            } else {
                $this->$name = $params[0];
            }

            return true;
        }

        if (strpos($method, 'get') === 0) {
            $name = Inflection::camelCaseToHungary(substr($method, 3, strlen($method)));
            if (isset(static::$_cols[$name])) {
                return isset($this->_data[$name])? $this->_data[$name]: null ;
            }

            return $this->$name;
        }

        $lcMethod = strtolower($method);
        if (substr($lcMethod, 0, 6) == 'findby') {
            $by = substr($method, 6, strlen($method));
            $method = 'findBy';
            $one = false;
        } else if(substr($lcMethod, 0, 9) == 'findoneby') {
            $by = substr($method, 9, strlen($method));
            $method = 'findOneBy';
            $one = true;
        }

        if ($method == 'findBy' || $method == 'findOneBy') {
            if (isset($by)) {
                if (!isset($params[0])) {
                    throw new Exception('You must specify the value to ' . $method);
                }

                /*if ($one) {
                    $fieldName = static::_resolveFindByFieldsName($by);
                    if(false == $fieldName) {
                        throw new Exception('Column ' .$fieldName .' not found!');
                    }
                }*/

                return static::findBy($by, $params, $one);
            }
        }

        foreach ($this->_behaviors as $behavior) {
        }
    }

    public function __set($name, $value) {
        if (isset(static::$_schema[$name])) {
            $v = $this->fixData($value, static::$_schema[$name]);
            $this->_data[$name] = $v;
            if (null != $v)
                $this->_modifiedCols[$name] = true;
        } else {
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if (isset(static::$_schema[$name])) {
            return $this->_data[$name];
        }

        return $this->$name;
    }

    public function __isset($name) {
        return (isset($this->_data[$name]))? true : isset($this->$name);
    }

    public function __unset($name) {
        if (isset(static::$_schema[$name])) {
            unset($this->_data[$name]);
        } else {
            unset($this->$name);
        }
    }

    public static function __callStatic($method, $params) {
        $lcMethod = strtolower($method);
        if (substr($lcMethod, 0, 6) == 'findby') {
            $by = substr($method, 6, strlen($method));
            $method = 'findBy';
            if (isset($by)) {
                if (!isset($params[0])) {
                    throw new Exception('You must specify the value to ' . $method);
                }

                return static::findBy($by, $params);
            }
        }

        $lcMethod = strtolower($method);
        if (substr($lcMethod, 0, 9) == 'findoneby') {
            $by = substr($method, 9, strlen($method));
            $method = 'findOneBy';

            if (isset($by)) {
                if (!isset($params[0])) {
                    throw new Exception('You must specify the value to ' . $method);
                }

                /*$fieldName = static::_resolveFindByFieldName($by);
                if(false == $fieldName) {
                    throw new Exception('Column ' .$fieldName .' not found!');
                }*/

                return static::findBy($by, $params, true);
            }
        }

        if(substr($lcMethod, 0, 10) == 'retrieveby') {
            $by = substr($method, 10, strlen($method));
            $method = 'retrieveBy';

            if (isset($by)) {
                if (!isset($params[0])) {
                    throw new Exception('You must specify the value to ' . $method);
                }

                return static::retrieveBy($by, $params);
            }
        }
    }
}