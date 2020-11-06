<?php
class Location
{ 
	private $conn;
	private $table_name = "locations";

	public $fe;
	public $accuracy;

	// constructor with $db as database connection
	public function __construct($db)
	{
		$this->conn = $db;
	}
	
	// create product
	function create()
	{ 
		// query to insert record
		$query = "INSERT INTO
					" . $this->table_name . "
				SET
					fe=:fe, accuracy=:accuracy";
	 
		// prepare query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->fe=htmlspecialchars(strip_tags($this->fe));
		$this->accuracy=htmlspecialchars(strip_tags($this->accuracy));
	 
		// bind values
		$stmt->bindParam(":fe", $this->fe);
		$stmt->bindParam(":accuracy", $this->accuracy);
	 
		// execute query
		if($stmt->execute())
			return true;
	 
		else
			return false;
	}
}