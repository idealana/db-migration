<?php

class Blueprint {

	/**
     * @var array $columns
     */
	private $columns = [];

	/**
     * @var array $dataTypes
     */
	private $dataTypes = [];

	/**
     * @var array $defaults
     */
	private $defaults = [];

	/**
     * @var array $afters
     */
	private $afters = [];

	/**
     * @var string $query
     */
	private $query = "";

	/**
     * @var string $currentColumn
     */
	private $currentColumn = "";

	public function __construct() {}

	/**
     * @param string $value
     * @return Blueprint object
     */
	private function setCurrentColumn($value)
	{
		$this->currentColumn = $value;
		return $this;
	}

	/**
     * @return Blueprint object
     */
	private function setColumns()
	{
		$this->columns[] = $this->currentColumn;
		return $this;
	}

	/**
     * @param string $value
     * @return Blueprint object
     */
	private function setDataTypes($value)
	{
		$this->dataTypes[ $this->currentColumn ] = $value;
		return $this;
	}

	/**
     * @param string $name
     * @param integer $length
     */
	public function id($name = 'id', $length = 20)
	{
		$length = $length > 20 ? 20 : $length;
		$this->query .= "{$name} BIGINT({$length}) UNSIGNED AUTO_INCREMENT PRIMARY KEY";
	}

	/**
     * @param string $name
     * @param integer $length
     * @return Blueprint object
     */
	public function string($name, $length = 255)
	{
		return $this->setCurrentColumn($name)
			->setColumns()
			->setDataTypes("VARCHAR({$length})");
	}

	/**
     * @param string $name
     * @return Blueprint object
     */
	public function text($name)
	{
		return $this->setCurrentColumn($name)
			->setColumns()
			->setDataTypes("TEXT");
	}

	/**
     * @param string $name
     * @param integer $length
     * @return Blueprint object
     */
	public function int($name, $length = 11)
	{
		return $this->setCurrentColumn($name)
			->setColumns()
			->setDataTypes("INT({$length})");
	}

	/**
     * @param string $name
     * @return Blueprint object
     */
	public function date($name)
	{
		return $this->setCurrentColumn($name)
			->setColumns()
			->setDataTypes("DATE");
	}

	/**
     * @param string $name
     * @return Blueprint object
     */
	public function dateTime($name)
	{
		return $this->setCurrentColumn($name)
			->setColumns()
			->setDataTypes("DATETIME");
	}

	/**
     * @return Blueprint object
     */
	public function nullable()
	{
		$this->default('NULL');
		return $this;
	}

	/**
     * @param string $value
     * @return Blueprint object
     */
	public function default($value)
	{
		$this->defaults[ $this->currentColumn ] = $value;
		return $this;
	}

	/**
     * @param string $value
     * @return Blueprint object
     */
	public function after($value)
	{
		$this->afters[ $this->currentColumn ] = $value;
		return $this;
	}

	public function createdAt()
	{
		$this->dateTime('created_at')->default('NULL');
	}

	public function updatedAt()
	{
		$this->dateTime('updated_at')->default('NULL');
	}

	public function deletedAt()
	{
		$this->dateTime('deleted_at')->default('NULL');
	}

	/**
     * @return boolean
     */
	private function isQueryEmpty()
	{
		return empty($this->query);
	}

	/**
     * @return string
     */
	private function getQuery()
	{
		foreach ($this->columns as $column) {

			if(! $this->isQueryEmpty()) {
				$this->query .= ", ";
			}

			// set column
			$this->query .= "{$column}";

			// set data type
			$this->query .= " {$this->dataTypes[ $column ]}";
			
			// set default
			$default = 'NOT NULL';

			if(array_key_exists($column, $this->defaults)) {
				$default = "DEFAULT {$this->defaults[ $column ]}";
			}

			$this->query .= " {$default}";

			// set after
			if(array_key_exists($column, $this->afters)) {
				$this->query .= " after {$this->afters[ $column ]}";
			}
		}

		return $this->query;
	}

	/**
     * @return string
     */
	public function createColumn()
	{
		return $this->getQuery();
	}

	/**
     * @return string
     */
	public function addColumn()
	{
		$result  = "";
		$queries = explode(",", $this->getQuery());
		$length  = count($queries);
		$count   = 1;

		foreach ($queries as $query) {
			$delimiter = $count === $length ? '' : ', ';
			$result   .= "ADD {$query}{$delimiter}";

			$count++;
		}

		return $result;
	}
}
