<?php
// src/AppBundle/Controller/IndexController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Model\Roles;

class IndexController extends Controller
{
	/**
	 * @Route("/index/index")
	 */
	public function index()
	{
		if($this->get("session")->get("displayname") == "")
		{
			$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
			return $this->render("index.twig", $data);
		}
		else
		{
			return $this->redirectUser();
		}
	}
	
	private function redirectUser()
	{
		$role = new Roles();
		if($role->isAdmin($this->get("session")->get("username")))
			return $this->redirect("/admin/index");
		else
			return $this->redirect("/user/index");
	}
	
	/**
	 * @Route("/index/login")
	 */
	public function login()
	{
		if($this->get("session")->get("username") == "")
		{
			$request = Request::createFromGlobals();
			$username = $request->request->get("username");
			$password = $request->request->get("password");

			$displayName = $this->doLDAPAuth($username, $password); 
			if(strlen($displayName) == 0)
			{
				return $this->redirect('/index/index');
			}
			else
			{
				$this->get("session")->set("displayname", $displayName);
				$this->get("session")->set("username", $username);
			}
		}
		
		return $this->redirectUser();
	}
	
	/**
	 * @Route("/index/logout") 
	 */
	public function logout()
	{
		if($this->get("session")->get("displayname") != "")
		{
			echo "invalidating session";
			$this->get("session")->clear();
			$this->get("session")->invalidate();
		}
		
		return $this->redirect("/index/index");
	}
	
	private function doLDAPAuth($username, $password)
	{
		$displayname = "";
		// using ldap bind
		$basedn   = 'ou=Employees,o=lexmark';
		$ldapusr  = "uid=lrdcldap,$basedn";
		$ldappass = 'P@ssw0rd1';                           // associated password
		$port     = '389';                                 // port
		
		// connect to ldap server
		$ldapconn = ldap_connect("dirservices.lexmark.com", $port)
		or die("Could not connect to LDAP server.");
		
		// Set some ldap options for talking to
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		
		if ($ldapconn)
		{
			// binding to ldap server
			$ldapbind = @ldap_bind($ldapconn, $ldapusr, $ldappass);
		
			// verify binding
			if ($ldapbind)
			{
				//$filter = '(&(objectCategory=employees)(samaccountname=*))';
				$filter = "(uid=$username)";
		
				if (!($search=@ldap_search($ldapconn, $basedn, $filter)))
				{
					echo("Unable to search ldap server<br>");
					echo("msg:'".ldap_error($ldapconn)."'</br>");
				}
				else
				{
					$info = ldap_get_entries($ldapconn, $search);
		
					if ($info["count"] > 0)
					{
						$displayname = $info[0]['cn'][0];
					}
				}
				ldap_unbind($ldapconn);
			}
			else
			{
				echo "LDAP bind failed...";
			}
		}
		return $displayname;
	}
}

?>
