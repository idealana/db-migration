<?php

require_once(__DIR__ . '/../env.php');
require_once(__DIR__ . '/Blueprint.php');

class Schema {

	/**
     * @var object $conn
     */
	private static $conn;

	public function __construct() {}

	private static function connection() {
		self::$conn = mysqli_connect(ENV['server_name'], ENV['username'], ENV['password'], ENV['db_name']);

		if (! self::$conn) {
			die("Connection failed: " . mysqli_connect_error());
		}
	}

	/**
     * @param string $tableName
     * @return boolean
     */
	private static function isTableExist($tableName)
	{
		$query = mysqli_query(self::$conn, "SHOW TABLES LIKE '$tableName'");
		return $query && mysqli_num_rows($query) === 1;
	}

	/**
     * @param string $tableName
     * @param callable $closure
     */
	public static function create($tableName, $closure)
	{
		self::connection();

		if(! self::isTableExist($tableName)) {
			$blueprint = new Blueprint;
			$closure($blueprint);

			$createTable = mysqli_query(
				self::$conn,
				"CREATE TABLE {$tableName} ({$blueprint->createColumn()})"
			);

			if($createTable) {
				echo "{$tableName}: Table has been created!";
				echo "\n";
			}
		}
	}

	/**
     * @param string $tableName
     * @param callable $closure
     */
	public static function table($tableName, $closure)
	{
		self::connection();

		if(self::isTableExist($tableName)) {
			$blueprint = new Blueprint;
			$closure($blueprint);

			$addColumns = mysqli_query(
				self::$conn,
				"ALTER TABLE {$tableName} {$blueprint->addColumn()}"
			);

			if($addColumns) {
				echo "{$tableName}: Columns has been added!";
				echo "\n";
			}
		}
	}
}