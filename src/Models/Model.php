<?php

namespace TestJustCms\Models;
use Configs\Connection;

class Model
{
    /**
     * @var \PDOStatement | Connection
     */
    private   $query;
    protected $getters;
    protected $table = null;
    protected $fillable = [];
    private   $data = [];
    protected $where = null;
    protected $orderBy = null;
    protected $limit = null;

    /**
     * Model constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->bindData($data);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if($name == 'create')
            return (new static($arguments[0]))->insert();
        if($name == 'update')
            return (new static(['id' => $arguments[0]]))->replace($arguments[1]);
        if($name == 'find')
            return (new static([]))->setWhere('id = :idwhere', ['idwhere' => $arguments[0]])->setLimit(1)->select(isset($arguments[1])? $arguments[1]: ['*']);
        if($name == 'findAll')
            return (new static([]))->select(isset($arguments[0])?$arguments[0]:['*']);
        if($name == 'delete')
            return (new static(['id' => $arguments[0]]))->destroy();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|Model
     */
    public function __call($name, $arguments)
    {
        if($name == 'create')
            return $this->insert();
        if($name == 'update')
            return $this->replace($arguments[0]);
        if($name == 'delete')
            return $this->destroy();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if(in_array($name, $this->getFillable()))
            $this->data[$name] = $value;
    }

    /**
     * @param $data
     */
    protected function bindData($data)
    {
        foreach ($data as $key => $value)
            $this->$key = $value;
    }

    /**
     * @param array $data
     */
    protected function bindQuery($data = [])
    {
        foreach ($this->data as $key => $value)
            $this->query->bindValue($key, $value);

        foreach ($data as $key => $value)
            $this->query->bindValue($key, $value);
    }

    /**
     * @param $command
     * @param array $whereBind
     * @param null $pdo
     * @return bool
     */
    public function execute($command, $whereBind = [], &$pdo = null)
    {
        $pdo = new Connection();

        $this->query = $pdo->prepare($command);

        $this->bindQuery($whereBind);

        return $this->query->execute();
    }

    /**
     * @param array $cols
     * @return array
     */
    public function select($cols = ['*'])
    {
        $where = '';
        $whereBind = [];

        if(is_array($this->where))
        {
            $where = ' WHERE ' . $this->where[0];
            $whereBind = $this->where[1];
            $this->where = null;
        }

        $this->execute('SELECT ' . implode(', ', $cols) .
            ' FROM ' . $this->getTable() . $where . $this->limit . $this->orderBy, $whereBind);

        $all = $this->query->fetchAll();

        $return = [];

        foreach ($all as $values)
            $return[] = (new static($values));

        return $return;
    }

    /**
     * @return $this
     */
    public function insert()
    {
        if(in_array('created_at', $this->fillable))
            $this->created_at = date('Y-m-d H:i:s');

        if(in_array('updated_at', $this->fillable))
            $this->updated_at = date('Y-m-d H:i:s');

        $this->execute('INSERT INTO ' . $this->getTable() . '(' . $this->getCols(). ') VALUES ( '. $this->getBind() .')', [], $pdo);

        $this->id = $pdo->lastInsertId();

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function replace($data)
    {
        if(in_array('updated_at',$this->fillable))
            $this->updated_at = date('Y-m-d H:i:s');

        $this->bindData($data);

        $this->execute('UPDATE ' . $this->getTable() . ' SET ' . $this->getSetUpdate(). ' WHERE id = :id');

        return $this;
    }

    /**
     * @return bool
     */
    public function destroy()
    {
        return $this->execute('DELETE FROM ' . $this->getTable() . ' WHERE id = :id');
    }

    /**
     * @param $where
     * @param $bind
     * @return $this
     */
    public function setWhere($where, $bind)
    {
        $this->where = [$where, $bind];

        return $this;
    }

    /**
     * @param $max
     * @param null $start
     * @return $this
     */
    public function setLimit($max, $start = null )
    {
        $this->limit = ' LIMIT ' . $max . ($start?', '. $start: '');

        return $this;
    }

    /**
     * @return array
     */
    protected function getFillable()
    {
        return array_merge($this->fillable, ['id']);
    }

    /**
     * @return string
     */
    protected function getSetUpdate()
    {
        $setUpdate = [];

        foreach ($this->data as $key => $value)
        {
            if($key == 'id')
                continue;

            $setUpdate[] = $key. ' = :' . $key;
        }

        return implode(', ', $setUpdate);
    }

    /**
     * @return null|string
     */
    protected function getTable()
    {
        $reflect = new \ReflectionClass($this);

        return (is_null($this->table))? strtolower($reflect->getShortName()) :$this->table;
    }

    /**
     * @return string
     */
    protected function getCols()
    {
        if(array_key_exists('id', $this->data))
            unset($this->data['id']);

        return implode(', ', array_keys($this->data));
    }

    /**
     * @return string
     */
    protected function getBind()
    {
        return ':' . implode(', :', array_keys($this->data));
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}