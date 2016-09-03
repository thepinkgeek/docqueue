<?php

namespace AppBundle\Model;
use AppBundle\Model\Database;

class Roles
{
	public function __construct()
	{
		
	}
	
	public function getRole($session)
	{
        $role = $session->get("role");
        $username = $session->get("username");

        if($role === null || $role == "")
        {
            if($username == "")
            {
                return "Guest";
            }
            else
            {
                $db = new Database();
                if($db->queryAdmin($username))
                    return "Admin";
                else
                    return "User";
            }
        }

        return $role;
	}
	
	public function isAdmin($session)
	{
		return $this->getRole($session) == "Admin";
	}
	
	public function isUser($session)
	{
		$role = $this->getRole($session);
		return  $role == "User" || $role == "Admin";
	}
}
?>
