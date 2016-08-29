<?php

namespace AppBundle\Model;

use \mysqli;

class Database
{
	private $servername;
	private $username;
	private $password;
	private $dbname;
	
	public function __construct()
	{
		$this->servername = "127.0.0.1";	
		$this->username = "root"; 
		$this->password = "";
		$this->dbname = "docqDB";
	}

	private function createDb() {
		$conn = new \mysqli($GLOBALS['servername'], $GLOBALS['username'], $GLOBALS['password']);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// Create database
		$sql = "CREATE DATABASE IF NOT EXISTS " . $GLOBALS['dbname'];
		if ($conn->query($sql) === FALSE) {
			echo "Error creating/checking database: " . $conn->error;
		}
	
		$conn->close();
	}
	
	private function createTable() {
		$conn = new \mysqli($GLOBALS['servername'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['dbname']);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// create table
		$sql = "CREATE TABLE IF NOT EXISTS Patient (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(50) NOT NULL,
				email VARCHAR(50),
				reg_date TIMESTAMP)";
	
		if ($conn->query($sql) === FALSE) {
			echo "Error creating table: " . $conn->error;
		}
	
		$conn->close();
	}
	
	public function insert($name, $email) {
		$rc = true;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		 
		$sql = "INSERT INTO Patient (name, email)
          VALUES ('". $name . "', '" . $email . "')";

		$rc = $conn->query($sql);
		$conn->close();
		
		return $rc;
	}
	
	
	public function delete($name, $email) {
		$rc = true;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// delete a record
		$sql = "DELETE FROM Patient WHERE name='" . $name . "' AND email='".$email."'";
	
		$rc = $conn->query($sql);
		$conn->close();
		
		return $rc;
	}
	
	public function queryAll($callBack, &$context, $className) {
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT id, name, email FROM Patient";
		$result = $conn->query($sql);
		if ($result ->num_rows > 0) {
			$index = 0;
			while($row = $result->fetch_assoc())
			{
				$row["isFirst"] = $index == 0;
				call_user_func_array($className."::$callBack", array(&$context, $row));
				$index++;
			}
		}

		$conn->close();
	}
	
	public function query($name, $email) {
		$rc = null;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT id, name, email FROM Patient WHERE name = $name AND email = $email";
		$result = $conn->query($sql);
	
		if ($result->num_rows > 0) {
			$rc = $result->fetch_assoc();
		}
	
		$conn->close();
		return $rc;
	}
	
	public function queryTop() {
		$rc = null;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT id FROM Patient LIMIT 1";
		$result = $conn->query($sql);
	
		$row = $result->fetch_row();
		$rc = $row[0];
		$conn->close();
		
		return $rc;
	}
	
	public function hasEntryDoctor()
	{
		$rc = false;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT id FROM DoctorOnDuty LIMIT 1";
		$result = $conn->query($sql);
		$rc = $result->num_rows > 0;
		$conn->close();
		
		return $rc;
	}
	
	public function hasEntryQueue()
	{
		$rc = false;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT id FROM QueueStatus LIMIT 1";
		$result = $conn->query($sql);
		$rc = $result->num_rows > 0;
		$conn->close();
		
		return $rc;
	}
	
	public function addQueueEntry()
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "INSERT INTO QueueStatus (status)
          VALUES ('open')";

		$result = $conn->query($sql);
		$conn->close();
		
		return $result;
	}
	
	public function addDoctorEntry($name, $time)
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "INSERT INTO Doctoronduty (name, time)
          VALUES ('". $name . "', '" . $time. "')";

		$result = $conn->query($sql);
		$conn->close();
		
		return $result;
	}
	
	public function deleteQueueEntry()
	{
		$rc = true;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// delete a record
		$sql = "DELETE FROM Queuestatus WHERE id in (SELECT id FROM Queuestatus LIMIT 1)";
	
		$rc = $conn->query($sql);
		$conn->close();
		
		return $rc;
	}
	
	public function deleteDoctorEntry()
	{
		$rc = true;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// delete a record
		$sql = "DELETE FROM Doctoronduty WHERE id in (SELECT id FROM Doctoronduty LIMIT 1)";
	
		$rc = $conn->query($sql);
		$conn->close();
		
		return $rc;
	}
	
	public function resetPatientQueue()
	{
		$rc = true;
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "DELETE FROM Patient";
		$rc = $conn->query($sql);

		$sql = "ALTER TABLE Patient AUTO_INCREMENT = 1";
		$rc = $conn->query($sql);
		
		$conn->close();
		
		return $rc;
	}
	
	public function addAdmin($username)
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "INSERT INTO Administrators (username)
          VALUES ('". $username . "')";

		$result = $conn->query($sql);
		$conn->close();
		
		return $result;

	}
}
?>