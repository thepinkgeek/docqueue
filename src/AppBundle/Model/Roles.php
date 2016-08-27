<?php

namespace AppBundle\Model;
class Roles
{
	public function __construct()
	{
		
	}
	
	public function getRole($username)
	{
		if($username == "")
			return "";
		else
		{
			return "Admin";
		}
	}
	
	public function isAdmin($username)
	{
		return $this->getRole($username) == "Admin";
	}
	
	public function isUser($username)
	{
		$role = $this->getRole($username);
		return  $role == "User" || $role == "Admin";
	}
}
?>