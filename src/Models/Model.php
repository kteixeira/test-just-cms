<?php

/**
 * Model Base.
 *
 * @author     Kaio Teixeira
 */

namespace TestJustCms\Models;
use Configs\Connection;

class Model
{
    /**
     * @var \PDOStatement | Connection
     */
    private   $query;
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
     * Chamada para os métodos estáticos que irão executar funções na Model
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
        if($name == 'findByName')
            return (new static([]))->setWhere('name = :name', ['name' => $arguments[0]])->setLimit(1)->select(isset($arguments[1])? $arguments[1]: ['*']);
        if($name == 'findAll')
            return (new static([]))->select(isset($arguments[0])?$arguments[0]:['*']);
        if($name == 'delete')
            return (new static(['id' => $arguments[0]]))->destroy();
    }

    /**
     * Chamada dos métodos literais para execução de funções no Model
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
     * Método mágico para a leitura de propriedades inacessíveis
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * Método mágico executado ao escrever dados em propriedades inacessíveis
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if(in_array($name, $this->getFillable()))
            $this->data[$name] = $value;
    }

    /**
     * Método que responsável por carregar os atributos e valores do objeto
     * @param $data
     */
    protected function bindData($data)
    {
        foreach ($data as $key => $value)
            $this->$key = $value;
    }

    /**
     * Método responsável por fazer o incremento de valores para a query
     * @param array $data
     */
    protected function bindQuery($data = [])
    {
        //Os valores de $this->data são incrementados com o método bindValue do PDOStatement
        foreach ($this->data as $key => $value)
            $this->query->bindValue($key, $value);

        //Os valores de $data (recebido por parâmetro) são incrementados com o método bindValue do PDOStatement
        foreach ($data as $key => $value)
            $this->query->bindValue($key, $value);
    }

    /**
     * Método responsável por fazer execuções básicas para o instância de conexão com o banco de dados
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
     * Método responsável pela busca de valores
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

        //Se retorna o objeto atual
        foreach ($all as $values)
            $return[] = (new static($values));

        return $return;
    }

    /**
     * Método responsável por fazer a inserção de dados
     * @return $this
     */
    public function insert()
    {
        //O created_at é atribuido caso necessário
        if(in_array('created_at', $this->fillable))
            $this->created_at = date('Y-m-d H:i:s');

        //O updated_at é atribuido caso necessário
        if(in_array('updated_at', $this->fillable))
            $this->updated_at = date('Y-m-d H:i:s');

        $this->execute('INSERT INTO ' . $this->getTable() . '(' . $this->getCols(). ') VALUES ( '. $this->getBind() .')', [], $pdo);

        //Se busca o ultimo id através da abstração feita na classe Connection
        $this->id = $pdo->lastInsertId();

        return $this;
    }

    /**
     * Método responsável por fazer a atualização de dados
     * @param $data
     * @return $this
     */
    public function replace($data)
    {
        //O updated_at é atribuido caso necessário
        if(in_array('updated_at',$this->fillable))
            $this->updated_at = date('Y-m-d H:i:s');

        //Atualizando o objeto
        $this->bindData($data);

        $this->execute('UPDATE ' . $this->getTable() . ' SET ' . $this->getSetUpdate(). ' WHERE `id` = :id');

        return $this;
    }

    /**
     * Método responsável por deletar dados
     * @return bool
     */
    public function destroy()
    {
        return $this->execute('DELETE FROM ' . $this->getTable() . ' WHERE id = :id');
    }

    /**
     * Método responsável por fazer a adição de novas condições
     * (atualmente utilizado apenas para as buscas e com algumas restrições)
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
     * Método responsável por fazer a adição do limite de dados em execuções
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
     * Método responsável por carregar os fillables
     * @return array
     */
    protected function getFillable()
    {
        return array_merge($this->fillable, ['id']);
    }

    /**
     * Método responsável por retornar formatados os dados que serão setados no comando de Update
     * @return string
     */
    protected function getSetUpdate()
    {
        $setUpdate = [];

        foreach ($this->data as $key => $value)
        {
            if($key == 'id')
                continue;

            $setUpdate[] = '`' . $key . '`' . ' = :' . $key;
        }

        return implode(', ', $setUpdate);
    }

    /**
     * Método responsável por retornar o nome da tabela do Model carregado
     * (obrigatório que a classe tenha o mesmo nome da tabela)
     * @return null|string
     */
    protected function getTable()
    {
        $reflect = new \ReflectionClass($this);

        return (is_null($this->table))? strtolower($reflect->getShortName()) :$this->table;
    }

    /**
     * Método responsável por formatar as colunas que irão receber valores no comando Insert
     * @return string
     */
    protected function getCols()
    {
        if(array_key_exists('id', $this->data))
            unset($this->data['id']);

        return implode(', ', array_keys($this->data));
    }

    /**
     * Método responsável por formatar os valores que serão incrementados no comando Insert
     * @return string
     */
    protected function getBind()
    {
        return ':' . implode(', :', array_keys($this->data));
    }

    /**
     * Serialização do objeto para tratamento de retornos
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}