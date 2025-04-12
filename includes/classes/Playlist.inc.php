<?php



class Playlist extends BaseObject
{

	static $idField = 'id'; //id field
	static $dataTable = 'playlist'; // table implemented

	/**
	 * Constructor 
	 */
	public function __construct()
	{
		;
	}

	/**
	 * Function used to get the data table name which is implemented by class
	 *
	 * @return String 
	 */
	protected static function GetDataTable()
	{
		return self::$dataTable;
	}

	/**
	 * Returns the fields of table.
	 *
	 * @return Array
	 */
	protected static function GetDataTableFields()
	{
		return array(
			self::$idField,
			'dj',
			'buyer',
			'timestamp_begin',
			'timestamp_end'

		);
	}
	static function clean_old()
	{
		$actual_time = time();
		$sql = "delete from playlist where timestamp_end>" . $actual_time;
		SQL::XQuery($sql);
	}
	static function buy(Transactions $transaction, $finish)
	{
		if ($transaction->buyer->id == $transaction->owner->id)
			throw new Exception(YOU_CANNOT_BUY_YOUR_OWN_PLAYLIST);
		$sql = "select * from " . self::$dataTable . " where buyer=" . $transaction->buyer->id . " and dj=" . $transaction->owner->id;
		$res = DBi::$conn->query($sql);
		if (mysqli_num_rows($res) > 0)
			throw new Exception(ALREADY_BUYED_IT);
		try {
			$transaction->finishIt();
			$sql = "insert into " . self::$dataTable . " (dj,buyer, timestamp_begin,timestamp_end) 
					  values('" . $transaction->owner->id . "','" . $transaction->buyer->id . "','" . time() . "','" . $finish . "')";
			DBi::$conn->query($sql);
		} catch (Exception $e) {
			throw $e;
		}
	}
	static function getMusic($files, $id)
	{
		$list = MediaFile::GetUserFiles($id);
		foreach ($list as $fp)
			$files[] = array("id" => $fp->id, "path" => $id . "/" . $fp->path, "name" => $fp->name, "owner" => $id);


	}
	static function getFile($user)
	{
		$sql = "select dj from " . self::$dataTable . " where buyer=" . $user->id;
		$res = DBi::$conn->query($sql);
		$files = array();
		Playlist::getMusic($files, $user->id);
		return $files[0];
	}
	static function getFiles($user, $lvl = "")
	{
		$sql = "select dj from " . self::$dataTable . " where buyer=" . $user->id;
		$res = DBi::$conn->query($sql);
		$files = array();

		if ($lvl != "profile" && mysqli_num_rows($res) != 0)
			while ($obj = mysqli_fetch_object($res))
				Playlist::getMusic($files, $obj->dj);

		Playlist::getMusic($files, $user->id);
		return $files;
	}
	/**
	 * Returns the identifier field name
	 *
	 * @return Mixed
	 */
	protected function GetIdentifierFieldName()
	{
		return self::$idField;
	}

	/**
	 * Function returns the class name
	 *
	 * @return String
	 */
	protected function GetClassName()
	{
		return __CLASS__;
	}

}

?>