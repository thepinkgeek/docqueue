<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\Database;
use AppBundle\Model\Roles;

class UserController extends Controller
{
	/**
	 * @Route("/user/index")
	 */
	public function usersIndex()
	{
		$role = new Roles();
		if(!$role->isUser($this->get("session")->get("username")))
		{
			$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
			return $this->render("index.twig", $data);
		}
		
		$sidebarData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/user_sidebar.json'), true);
		$navigationData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		$tableData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/appointment_table.json'), true);
		$modalData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/cancelappointment.json'), true);
		$data = array_merge($sidebarData, $navigationData, $tableData, $modalData); return $this->render("user_index.twig", $data);
		
		return $this->render('user_index.twig', $data);
	}
		
	/**
	 * @Route("/admin/index")
	 * */
	public function adminIndex()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
			return $this->render("index.twig", $data);
		}

		$sidebardata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/admin_sidebar.json'), true);
		$navigationData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		$modalData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/cancelallappointments.json'), true);
		$tabledata = $this->populatePatientData();
		$template_data = array_merge($sidebardata, $navigationData, $tabledata, $modalData);
		return $this->render("admin_index.twig", $template_data);
	}
	
	private function populatePatientData()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/patient_table_template.json'), true);
		$db = new Database();
		$db->queryAll('formatPatientData', $tabledata, "\AppBundle\Controller\UserController");
		return $tabledata;
	}
	
	public static function formatPatientData(&$tableData, $rowData)
	{
		$id = $rowData["id"];
		$email = $rowData["email"];
		$name = $rowData["name"];
		$cancelHref = "/user/cancelappointment?name=$name&email=$email";
		$finishHref = "/admin/finishappointment?name=$name&email=$email";
		
		$data = array(array("name"=>$id, "isHeader"=>true),
					  array("name"=>$name),
					  array("name"=>$email),
					  array("name"=>"Finish", "href"=>$finishHref),
					  array("name"=>"Cancel", "href"=>$cancelHref)
					  );
		
		array_push($tableData["table"]["rows"], $data);
	}
	
	/**
	 * @Route("/admin/viewqueue")
	 */
	public function viewPatientQueue()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		$tabledata = $this->populatePatientData();
		return $this->render("templates/table.twig", $tabledata);
	}
	
	/**
	 * @Route("/admin/addpatient")
	 */
	public function addPatient()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		return $this->render("templates/addpatient.twig");
	}

	/**
	 * @Route("/user/query")
	 */
	public function queryAppointment()
	{
		$role = new Roles();
		if(!$role->isUser($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		$data = json_decode(file_get_contents('json/user_sidebar.json'), true);
		return $this->render("user_index.twig", $data);
	}
	
	/**
	 * @Route("/user/addappointment")
	 */
	public function addAppointment()
	{
		$role = new Roles();
		if(!$role->isUser($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		return $this->render("templates/addappointment.twig");
	}
	
	/**
	 * @Route("/user/addappointmentsubmit")
	 */
	public function addAppointmentSubmit()
	{
		$role = new Roles();
		if(!$role->isUser($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}

		$request = Request::createFromGlobals();
		$email = $this->generateUserEmail();
		$name = $request->request->get("name");
		$db = new Database();
		$db->insert($name, $email);
		return $this->viewAppointment();
	}
	
	private function generateUserEmail()
	{
		return $this->get("session")->get("username") + "@lexmark.com";
	}

	/**
	 * @Route("/user/cancelappointment")
	 */
	public function cancelAppointment()
	{
		$role = new Roles();
		$username = $this->get("session")->get("username");
		if(!$role->isUser($username))
		{
			return $this->redirect("/index/index");
		}

		$request = Request::createFromGlobals();
		$email = $request->query->get("email");
		$name = $request->query->get("name");

		if($username == $email)
		{
			$db = new Database();
			$db->delete($name, $email);
			$data = $this->populatePatientData();
			$this->sendCancelEmail($email);
			return $this->render("templates/table.twig", $data);
		}
		else {
			return $this->redirect("/user/index");
		}
	}
	
	private function sendCancelEmail($email)
	{
		
	}
	
	private function sendFinishEmail($email)
	{
		
	}
	
	/**
	 * @Route("/admin/finishappointment")
	 */
	public function finishAppointment()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}

		$request = Request::createFromGlobals();
		$email = $request->query->get("email");
		$name = $request->query->get("name");

		$db = new Database();
		$db->delete($name, $email);
		$data = $this->populatePatientData();
		$this->sendFinishEmail($email);
		return $this->render("templates/table.twig", $data);

	}

	/**
	 * @Route("/user/viewappointment")
	 */
	public function viewAppointment()
	{
		$role = new Roles();
		if(!$role->isUser($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}

		$tableData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/appointment_table.json'), true);
		return $this->render("viewappointment.twig", $tableData);
	}

	/**
	 * @Route("/admin/addaddministrator")
	 */
	public function addAdmin()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		$data = json_decode(file_get_contents('json/user_sidebar.json'), true);
		return $this->render("user_index.twig", $data);
	}
	
	/**
	 * @Route("/admin/openqueue")
	 */
	public function openQueue()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		return $this->viewPatientQueue();
	}
	
	/**
	 * @Route("/admin/closequeue")
	 */
	public function closeQueue()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		return $this->viewPatientQueue();
	}
	
	/**
	 * @Route("/admin/resetqueue")
	 */
	public function resetQueue()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		return $this->viewPatientQueue();
	}
	
	/**
	 * @Route("/admin/thedoctorisin")
	 */
	public function doctorIsIn()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		return $this->viewPatientQueue();
	}
	
	/**
	 * @Route("/admin/thedoctorisout")
	 */
	public function doctorIsOut()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		return $this->viewPatientQueue();
	}
	
	/**
	 * @Route("/admin/historylog")
	 */
	public function viewHistoryLog()
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")->get("username")))
		{
			return $this->redirect("/index/index");
		}
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
}

?>
