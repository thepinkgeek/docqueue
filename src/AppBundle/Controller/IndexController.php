<?php
// src/AppBundle/Controller/IndexController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
	/**
	 * @Route("/index/index")
	 */
	public function index()
	{
		$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		return $this->render("index.twig", $data);
	}
	
	/**
	 * @Route("/index/login")
	 */
	public function login()
	{
		$request = Request::createFromGlobals();
		$username = $request->request->get("username");
		$password = $request->request->get("password");

		$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		
		if(!$this->doLDAPAuth($username, $password))
		{
			return $this->redirect('/index/index');
		}
		else
		{
			$isAdmin = false;
		
			if($isAdmin)
				return $this->redirect("/admin/index");
			else
				return $this->redirect("/user/index");

		}
	}
	
	private function doLDAPAuth($username, $password)
	{
		// using ldap bind
		$ldapusr  = 'uid=lrdcldap,ou=Employees,o=lexmark'; // bind user
		$ldaprdn  = 'ou=employees,o=lexmark';              // ldap rdn or dn
		$ldappass = 'P@ssw0rd1';                           // associated password
		$port     = '389';                                 // port
		
		try
		{
				// connect to ldap server
			$ldapconn = ldap_connect("dirservices.lexmark.com", $port) or die("Could not connect to LDAP server.");
			// Set some ldap options for talking to
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
		
			if ($ldapconn) {
				// binding to ldap server
				$ldapbind = ldap_bind($ldapconn, $ldapusr, $ldappass);
		
				// verify binding
				if ($ldapbind) {
					if (!($search=@ldap_search($ldapconn, $ldaprdn, $filter))) {
						echo("Unable to search ldap server<br>");
						echo("msg:'".ldap_error($ldapconn)."'</br>");#check the message again
					} else {
						$number_returned = ldap_count_entries($ldapconn, $search);
						$info = ldap_get_entries($ldapconn, $search);
						echo "The number of entries returned is ". $number_returned."<p>";
						for ($i=0; $i<$info["count"]; $i++) {
							dump($info[$i]);
						}
					}
					ldap_unbind($ldapconn);
					return true;
				} else {
					echo "LDAP bind failed...";
					return false;
				}
			}
		}
		catch(Exception $e)
		{
			return false;		
		}	
	}
}

?>
