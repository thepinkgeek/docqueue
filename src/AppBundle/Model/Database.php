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

	public function createDb() {
		$conn = new \mysqli($this->servername, $this->username, $this->password);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// Create database
		$sql = "CREATE DATABASE IF NOT EXISTS " . $this->dbname;
		if ($conn->query($sql) === FALSE) {
			echo "Error creating/checking database: " . $conn->error;
		}
	
		$conn->close();
	}
	
	public function createTablePatient() {
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
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
	
	public function insertPatient($name, $email) {
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
	
	
	public function deletePatient($name, $email) {
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
	
	public function queryAllPatient($callBack, &$context, $className) {
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
	
	public function queryPatientAppointments($callBack, &$context, $className, $email) {
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT id, name, email FROM Patient WHERE email=\"$email\"";
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
	
	public function queryPatient($name, $email) {
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
	
	public function queryTopPatient() {
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
	
	public function addDoctorEntry($name, $timeFrom, $timeTo)
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "INSERT INTO DoctorOnDuty (name, timeFrom, timeTo)
          VALUES ('". $name . "', '" . $timeFrom. "', '" . $timeTo. "')";

		$result = $conn->query($sql);

        var_dump($result);
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
		$sql = "DELETE FROM QueueStatus";
	
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
		$sql = "DELETE FROM DoctorOnDuty";
	
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
	
		$sql = "INSERT INTO Administrators (username) VALUES ('". $username . "')";

		$result = $conn->query($sql);
		$conn->close();
		
		return $result;

	}
	
	public function removeAdmin($username)
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "DELETE FROM Administrators WHERE username=\"$username\"";

		$result = $conn->query($sql);
		$conn->close();
		
		return $result;
	}
	
	public function createTableMessages() {
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// create table
		$sql = "CREATE TABLE IF NOT EXISTS Messages ( id INT(6) UNSIGNED PRIMARY KEY,
							      subject VARCHAR(512),
							      message VARCHAR(1024)
							    )";
	
		if ($conn->query($sql) === FALSE) {
			echo "Error creating table: " . $conn->error;
		}
	
		$conn->close();
	}
	
	public function insertMessage($messageId, $subject, $customMessage) {

		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		 
		$sql = "INSERT INTO Messages (id, subject, message)
          VALUES ('". $messageId . "', '" . $subject . "', '" . $customMessage . "')
		  ON DUPLICATE KEY UPDATE subject='". $subject . "', message='" . $customMessage ."' ";
	
		if ($conn->query($sql) === TRUE) {
			echo "New custom message created successfully";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	
		$conn->close();
	}
	
	public function deleteMessage($messageId) {
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
		 
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// delete a record
		$sql = "DELETE FROM Messages WHERE id=" . $messageId;
	
		if ($conn->query($sql) === TRUE) {
			echo "Record deleted successfully";
		} else {
			echo "Error deleting record: " . $conn->error;
		}
	
		$conn->close();
	}
	
	public function queryMessage($messageId) {
		$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT * from Messages WHERE id=". $messageId;
	
		$result = $conn->query($sql);
	
		if ($result->num_rows > 0) {
			echo "Query successful";
		} else {
			echo "Query failed";
		}
		 
		$conn->close();
		 
		return $result;
	}
	
	public function createTableAdministrators()
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		// create table
		$sql = "CREATE TABLE IF NOT EXISTS Administrators (
  			    id int(11) NOT NULL AUTO_INCREMENT,
  				`username` varchar(30) NOT NULL,
				 PRIMARY KEY (id)) AUTO_INCREMENT=1;";
	
		if ($conn->query($sql) === FALSE) {
			echo "Error creating table: " . $conn->error;
		}
		$conn->close();
	}
	
	public function createTableDoctorOnDuty()
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "CREATE TABLE IF NOT EXISTS DoctorOnDuty (
			    id int(11) NOT NULL AUTO_INCREMENT,
				name varchar(100) NOT NULL,
				timeFrom varchar(50) NOT NULL,
				timeTo varchar(50) NOT NULL,
				PRIMARY KEY (id) ) AUTO_INCREMENT=1;";
		
		if ($conn->query($sql) === FALSE) {
			echo "Error creating table: " . $conn->error;
		}
		$conn->close();
	}
	
	public function createTableQueueStatus()
	{
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS QueueStatus (
				id int(11) NOT NULL AUTO_INCREMENT,
				status varchar(20) NOT NULL,
				timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  				PRIMARY KEY (id)) AUTO_INCREMENT=1;";
		
		if ($conn->query($sql) === FALSE) {
			echo "Error creating table: " . $conn->error;
		}
		$conn->close();

	}

    public function queryAdmin($username) 
    {
        $conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

        $sql = "SELECT username FROM Administrators WHERE username = \"$username\"";
		$result = $conn->query($sql);

        $isAdmin = false;
        if($result == true)
        {
            $isAdmin = $result ->num_rows > 0;
        }
        $conn->close();

        return $isAdmin;
    }
    
    public function queryAllAdmin($callBack, &$context, $className) {
		$conn = new \mysqli($this->servername, $this->username, $this->password, $this->dbname);
	
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
	
		$sql = "SELECT id, username FROM Administrators";
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
}
?>
