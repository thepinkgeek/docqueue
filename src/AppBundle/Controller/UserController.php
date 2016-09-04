<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\Database;
use AppBundle\Model\Roles;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\VarDumper\Cloner\Data;

class UserController extends Controller
{
	/**
	 * @Route("/user/index")
	 */
	public function usersIndex(Request $request)
	{
		$role = new Roles();
		if(!$role->isUser($request->getSession()))
		{
			$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
			return $this->render("index.twig", $data);
		}
		
		$email = $this->generateUserEmail($request->getSession()->get("username"));
		$sidebarData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/user_sidebar.json'), true);
		$navigationData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		$modalData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/cancelappointment.json'), true);
		$tableData = $this->populateUserPatientData($email);
		$cancelappointmentsModal = 0;
		$this->getJsonFile("cancelappointmentadmin.json", $cancelappointmentsModal);
		$data = array_merge($sidebarData, $navigationData, $tableData, $modalData, $cancelappointmentsModal);
		
		return $this->render('user_index.twig', $data);
	}

	public static function formatUserPatientData(&$tableData, $rowData)
	{
		$id = $rowData["id"];
		$email = $rowData["email"];
		$name = $rowData["name"];
		$cancelHref = "/user/cancelappointment?name=$name&email=$email";
		
		$data = array(array("name"=>$id, "isHeader"=>true),
					  array("name"=>$name),
					  array("name"=>$email));
		
		array_push($data, array("name"=>"Cancel", "href"=>$cancelHref, "class"=>"cancel", "id"=>"cancelpatient-".$id, "toggle"=>"modal", "popupId"=>"#cancelappointmentconfirm"));
		array_push($tableData["table"]["rows"], $data);
	}
		
	/**
	 * @Route("/admin/index")
	 * */
	public function adminIndex(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			$data = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
			return $this->render("index.twig", $data);
		}

		$sidebardata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/admin_sidebar.json'), true);
		$navigationData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		$modalData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/cancelallappointments.json'), true);
		$finishappointmentsModal = 0;
		$cancelappointmentsModal = 0;
		$this->getJsonFile("cancelappointmentadmin.json", $cancelappointmentsModal);
		$this->getJsonFile("finishappointment.json", $finishappointmentsModal);
		$tabledata = $this->populatePatientData();
		$template_data = array_merge($sidebardata, $navigationData, $tabledata, $this->concatenateModals(array($finishappointmentsModal, $cancelappointmentsModal, $modalData)));
		return $this->render("admin_index.twig", $template_data);
	}
	
	private function populateUserPatientData($email)
	{
		$tableData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/patient_table_template.json'), true);
		$db = new Database();
		$db->queryPatientAppointments('formatUserPatientData', $tableData, "\AppBundle\Controller\UserController", $email);
		return $tableData;
	}
	
	private function populatePatientData()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/patient_table_template.json'), true);
		$db = new Database();
		$db->queryAllPatient('formatPatientData', $tabledata, "\AppBundle\Controller\UserController");
		return $tabledata;
	}
	
	public static function formatPatientData(&$tableData, $rowData)
	{
		$id = $rowData["id"];
		$email = $rowData["email"];
		$name = $rowData["name"];
		$cancelHref = "/admin/cancelappointment?name=$name&email=$email";
		$finishHref = "/admin/finishappointment?name=$name&email=$email";
		
		$data = array(array("name"=>$id, "isHeader"=>true),
					  array("name"=>$name),
					  array("name"=>$email));
		
		if($rowData["isFirst"])
		{
			array_push($data, array("name"=>"Finish", "href"=>$finishHref, "class"=>'finish', "id"=>"finishpatient-".$id, "toggle"=>"modal", "popupId"=>"#finishappointmentconfirm"));
		}
		else
		{
			array_push($data, array("name"=>""));
		}
		array_push($data, array("name"=>"Cancel", "href"=>$cancelHref, "class"=>"cancel", "id"=>"cancelpatient-".$id, "toggle"=>"modal", "popupId"=>"#cancelappointmentconfirm"));
		array_push($tableData["table"]["rows"], $data);
	}
	
	/**
	 * @Route("/admin/viewqueue")
	 */
	public function viewPatientQueue(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		$cancelappointmentsModal = 0;
		$finishappointmentsModal = 0;
		$this->getJsonFile("cancelappointmentadmin.json", $cancelappointmentsModal);
		$this->getJsonFile("finishappointment.json", $finishappointmentsModal);

		$tabledata = $this->populatePatientData();
		$data = array_merge($tabledata, $this->concatenateModals(array($finishappointmentsModal, $cancelappointmentsModal)));
		return $this->render("patient_table.twig", $data);
	}
	
	private function getJsonFile($fileName, &$container)
	{
		$jsonFileName = $this->get('kernel')->getRootDir().'/Resources/json/'.$fileName;
		$container = json_decode(file_get_contents($jsonFileName), true);
	}
	
	private function concatenateModals($modalgroups)
	{
		$final = array("modals"=>array());
		foreach($modalgroups as $modalgroup)
		{
			foreach($modalgroup["modals"] as $modal)
			{
				array_push($final["modals"], $modal);
			}
		}
		return $final;
	}
	/**
	 * @Route("/admin/addpatient")
	 */
	public function addPatient(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		return $this->render("templates/addpatient.twig");
	}

	/**
	 * @Route("/user/query")
	 */
	public function queryAppointment(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isUser($session))
		{
			return $this->redirect("/index/index");
		}
		$data = json_decode(file_get_contents('json/user_sidebar.json'), true);
		return $this->render("user_index.twig", $data);
	}
	
	/**
	 * @Route("/user/addappointment")
	 */
	public function addAppointment(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isUser($session))
		{
			return $this->redirect("/index/index");
		}
		return $this->render("templates/addappointment.twig");
	}
	
	/**
	 * @Route("/user/addappointmentsubmit")
	 */
	public function addAppointmentSubmit(Request $request)
	{
		$role = new Roles();
        $session = $request->getSession();
		$username = $session->get("username");
		$displayname = $session->get("displayname");

		if(!$role->isUser($session))
		{
			return $this->redirect("/index/index");
		}

		$email = $this->generateUserEmail($username);
		if($request->request->get("isDependent") == "yes")
		{
			$name = $request->request->get("dependentName");
		}
		else
		{
			$name = $displayname;
		}
		$db = new Database();
		$db->insertPatient($name, $email);
		return $this->viewAppointment($request);
	}
	
	/**
	 * @Route("/admin/addappointmentsubmit")
	 */
	public function adminAddAppointmentSubmit(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isUser($session))
		{
			return $this->redirect("/index/index");
		}

		$email = $this->generateUserEmail($request->request->get("username"));
		$name = $request->request->get("name");
		$db = new Database();
		$db->insertPatient($name, $email);
		return $this->viewPatientQueue($request);
	}
	
	private function generateUserEmail($username)
	{
		return $username."@lexmark.com";
	}

	/**
	 * @Route("/user/cancelappointment")
	 */
	public function cancelAppointmentUser(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		$username = $session->get("username");

		if(!$role->isUser($session))
		{
			return $this->redirect("/index/index");
		}

		$email = $request->query->get("email");
		$name = $request->query->get("name");
		
		if($email == $username || $role->isAdmin($session))
		{
			$db = new Database();
        	$db->deletePatient($name, $email);
        	$this->sendCancelEmail($email);
		}

		$tableData = $this->populateUserPatientData($email);
		$cancelappointmentsModal = 0;
		$this->getJsonFile("cancelappointmentadmin.json", $cancelappointmentsModal);
		$data = array_merge($tableData, $cancelappointmentsModal);
		return $this->render("viewappointment.twig", $data);
	}
	
	/**
	 * @Route("/admin/cancelappointment")
	 */
	public function cancelAppointmentAdmin(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		$username = $session->get("username");

		if(!$role->isUser($session))
		{
			return $this->redirect("/index/index");
		}

		$email = $request->query->get("email");
		$name = $request->query->get("name");
		
		if($email == $username || $role->isAdmin($session))
		{
			$db = new Database();
        	$db->deletePatient($name, $email);
        	$this->sendCancelEmail($email);
		}

		$data = $this->populatePatientData();
		return $this->render("templates/table.twig", $data);
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
	public function finishAppointment(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}

		$email = $request->query->get("email");
		$name = $request->query->get("name");

		$db = new Database();
		$db->deletePatient($name, $email);
		$data = $this->populatePatientData();
		$this->sendFinishEmail($email);
		return $this->render("templates/table.twig", $data);

	}

	/**
	 * @Route("/user/viewappointment")
	 */
	public function viewAppointment(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isUser($session))
		{
			return $this->redirect("/index/index");
		}

		$email = $this->generateUserEmail($session->get("username"));
		$tableData = $this->populateUserPatientData($email);
		$cancelappointmentsModal = 0;
		$this->getJsonFile("cancelappointmentadmin.json", $cancelappointmentsModal);
		$data = array_merge($tableData, $cancelappointmentsModal);
		return $this->render("viewappointment.twig", $data);
	}

	/**
	 * @Route("/admin/openqueue")
	 */
	public function openQueue(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		
		$db = new Database();
		$db->addQueueEntry();
		return $this->viewPatientQueue($request);
	}
	
	/**
	 * @Route("/admin/closequeue")
	 */
	public function closeQueue(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		
		$db = new Database();
		$db->deleteQueueEntry();
		return $this->viewPatientQueue($request);
	}
	
	/**
	 * @Route("/admin/resetqueue")
	 */
	public function resetQueue(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		
		$db = new Database();
		$db->resetPatientQueue();
        $data = $this->populatePatientData();
        return $this->render("templates/table.twig", $data);
	}
	
	/**
	 * @Route("/admin/thedoctorisin")
	 */
	public function doctorIsIn(Request $request)
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")))
		{
			return $this->redirect("/index/index");
		}
		
		return $this->render('templates/thedoctorisin.twig');
	}

    /**
     * @Route("/admin/filldoctordetails")
     */
    public function fillDoctorDetails(Request $request)
    {
        $role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		
        $db = new Database();
		$name = $request->request->get("doctorname");
		$timeFrom = $request->request->get("doctortimefrom");
        $timeTo = $request->request->get("doctortimeto");
        
        $db->addDoctorEntry($name, $timeFrom, $timeTo);
		return $this->viewPatientQueue($request);
    }

	
	/**
	 * @Route("/admin/thedoctorisout")
	 */
	public function doctorIsOut(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		return $this->viewPatientQueue($request);
	}
	
	/**
	 * @Route("/admin/historylog")
	 */
	public function viewHistoryLog(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}

	/**
	 * @Route("/admin/addadmin")
	 */
	public function addAdmin(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}
		return $this->render("templates/addadmin.twig");
	}

    /**
	 * @Route("/admin/insertadmin")
	 */
	public function insertAdmin(Request $request)
	{
		$role = new Roles();
		$session = $request->getSession();
		if(!$role->isAdmin($session))
		{
			return $this->redirect("/index/index");
		}

		$username = $request->request->get("username");

        $db = new Database();
        $db->addAdmin($username);

        return $this->viewPatientQueue($request);
	}

	
	/**
	 * @Route("/admin/loadComponentsToHide")
	 */
	public function loadHiddenComponents(Request $request)
	{
		$components = array();
		
		$db = new Database();
		
		if($db->hasEntryDoctor())
		{
			array_push($components, "thedoctorisin");
		}
		else
		{
			array_push($components, "thedoctorisout");
		}
		
		if($db->hasEntryQueue())
		{
			array_push($components, "openqueue");
		}
		else
		{
			array_push($components, "closequeue");
		}

		return new JsonResponse(array("components"=>$components));
	}
	
	/**
	 * @Route("/admin/removeadmin")
	 */
	public function removeAdmin(Request $request)
	{
		$username = $request->query->get("username");
		
		$db = new Database();
		$db->removeAdmin($username);
		
		return $this->viewAdminList();
	}
	
	/**
	 * @Route("/admin/viewadminlist")
	 */
	public function viewAdminList(Request $request)
	{
		$role = new Roles();
		if(!$role->isAdmin($this->get("session")))
		{
			return $this->redirect("/index/index");
		}
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/admin_table_template.json'), true);
		$db = new Database();
		$db->queryAllAdmin('formatAdminData', $tabledata, "\AppBundle\Controller\UserController");

		return $this->render("admin_table.twig", $tabledata);
	}
	
	public static function formatAdminData(&$tableData, $rowData)
	{
		$id = $rowData["id"];
		$username = $rowData["username"];
		$removeHref = "/admin/removeadmin?username=$username";
		
		$data = array(array("name"=>$id, "isHeader"=>true),
					  array("name"=>$username),
					  array("name"=>"Remove", "href"=>$removeHref, "class"=>'remove', "id"=>"removepatient-".$id));
		
		array_push($tableData["table"]["rows"], $data);
	}
}
?>
