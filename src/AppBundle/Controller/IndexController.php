<?php
// src/AppBundle/Controller/IndexController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Model\Roles;
use AppBundle\Model\Database;

class IndexController extends Controller
{
	/**
	 * @Route("/index/index")
	 */
	public function index(Request $request)
	{
		$session = $request->getSession();
		if($session->get("username") == "")
		{
			$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
			return $this->render("index.twig", $data);
		}
		else
		{
			return $this->redirectUser($request);
		}
	}
	
	/**
	 * @Route("/index/setup")
	 */
	public function setup()
	{
		$db = new Database();	
		
		$db->createDb();
		$db->createTablePatient();
		$db->createTableMessages();
		$db->createTableAdministrators();
		$db->createTableDoctorOnDuty();
		$db->createTableQueueStatus();
		
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
	}
	private function redirectUser(Request $request)
	{
		$role = new Roles();
		if($role->isAdmin($request->getSession()))
			return $this->redirect("/admin/index");
		else
			return $this->redirect("/user/index");
	}
	
	/**
	 * @Route("/index/login")
	 */
	public function login(Request $request)
	{
		$session = $request->getSession();
		if($session->get("username", "") == "")
		{
			$username = $request->request->get("username");
			$password = $request->request->get("password");

			$displayName = $this->doLDAPAuth($username, $password); 
			if(strlen($displayName) == 0)
			{
				return $this->redirect('/index/index');
			}
			else
			{
				$session->set("displayname", $displayName);
				$session->set("username", $username);
                $role = new Roles();
                $session->set("role", $role->getRole($session));
                $session->start();
			}
		}
		
		return $this->redirectUser($request);
	}
	
	/**
	 * @Route("/index/logout") 
	 */
	public function logout(Request $request)
	{
		$session = $request->getSession();
		if($session->get("displayname") != "")
		{
			$session->clear();
			$session->invalidate();
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
