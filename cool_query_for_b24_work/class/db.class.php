<?php 
class DB
{
	private static $db_host = 'localhost';
	private static $db_name = 'demo_demo';
	private static $db_user = 'demo_demo';
	private static $db_pass = 'X9La3F4eGx';

	private static $_instance = null;

	private function __construct() {}
	private function __clone() {}

	public static function run()
	{
		if(!isset(self::$_instance))
		{
			try {
				self::$_instance = new PDO('mysql:host='.self::$db_host.';dbname=' . self::$db_name, self::$db_user, self::$db_pass,
					array(
						PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
						));
			} catch(PDOExcetion $e) {
				throw new Exception("Ошибка соеденения с БД". $e->getMessage());
			}
		}

		return self::$_instance;
	}

	/*
	use --
	DB::insert('tbl_name', array(
		'username' => $data['username']
	));

	*/

	public static function insert($table, $data)
	{
		ksort($data);

		$fieldName = implode('`, `', array_keys($data));
		$fieldValue = ':' . implode(', :', array_keys($data));

		$query = DB::run()->prepare("INSERT INTO $table (`$fieldName`) VALUES ($fieldValue)");

		foreach($data as $key => $value):
			$query->bindValue(":$key", $value);
		endforeach;

		$query->execute();
	}

	/*
	use --

	$postData = array(
		'login'    => $data['login'],
		'password' => $data['password'],
		'role'     => $data['role']
	);

	DB::update('tbl_users', $postData, "`id` = {$data['id']}");

	*/


	public static function update($table, $data, $where)
	{
		ksort($data);

		$fieldDetails = null;

		foreach($data as $key => $value) {
			$fieldDetails .= "`$key`=:$key,";
		}

		$fieldDetails = rtrim($fieldDetails, ",");

		$query = DB::run()->prepare("UPDATE $table SET $fieldDetails WHERE $where");

		foreach($data as $key => $value):
			$query->bindValue(":$key", $value);
		endforeach;

		$query->execute();
	}

	/*
	@param $sql - an SQL string
	@param $array - Array paremetrs to bild
	@return mixed

	USE
		$data = DB::select('SELECT * FROM tbl_users');
		
		return $data fetch all with us Query 

		if need Select 1 query use 
		DB::select('SELECT * FROM tbl_users WHERE id = :id', array("id" => $id), PDO::FETCH_OBJ OR NOTHINK);  

	*/

	public static function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
	{
		$db = DB::run();

		$sth = $db->prepare($sql);

		foreach($array as $key => $value):
			$sth->bindValue(":$key", $value);
		endforeach;

		$sth->execute();

		return $sth->fetchAll($fetchMode);
	}

	/*
	use -- 
	DB::delete('tbl_users', $data['id'])
	*/
	public static function delete($table, $where, $limit = 1)
	{
		$db = DB::run();

		return $db->exec("DELETE FROM $table WHERE $where LIMIT $limit");


	}
}
