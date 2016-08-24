<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\Finder;

class UserController extends Controller
{
	/**
	 * @Route("/user/index")
	 */
	public function usersIndex()
	{
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
		$sidebardata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/admin_sidebar.json'), true);
		$navigationData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		$modalData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/cancelallappointments.json'), true);
		$template_data = array_merge($sidebardata, $navigationData, $tabledata, $modalData);

		return $this->render("admin_index.twig", $template_data);
	}
	
	/**
	 * @Route("/admin/viewqueue")
	 */
	public function viewPatientQueue()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
	
	/**
	 * @Route("/admin/addpatient")
	 */
	public function addPatient()
	{
		return $this->render("templates/addpatient.twig");
	}

	/**
	 * @Route("/user/query")
	 */
	public function queryAppointment()
	{
		$data = json_decode(file_get_contents('json/user_sidebar.json'), true);
		return $this->render("user_index.twig", $data);
	}
	
	/**
	 * @Route("/user/addappointment")
	 */
	public function addAppointment()
	{
		return $this->render("templates/addappointment.twig");
	}
	
	/**
	 * @Route("/user/addappointmentsubmit")
	 */
	public function addAppointmentSubmit()
	{
		return $this->viewAppointment();
	}

	/**
	 * @Route("/user/cancelappointment")
	 */
	public function cancelAppointment()
	{
		$sidebarData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/user_sidebar.json'), true);
		$navigationData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/home_default.json'), true);
		$data = array_merge($sidebarData, $navigationData);
		return $this->render("user_index.twig", $data);
	}

	/**
	 * @Route("/user/viewappointment")
	 */
	public function viewAppointment()
	{
		$tableData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/appointment_table.json'), true);
		return $this->render("viewappointment.twig", $tableData);
	}

	/**
	 * @Route("/admin/addaddministrator")
	 */
	public function addAdmin()
	{
		$data = json_decode(file_get_contents('json/user_sidebar.json'), true);
		return $this->render("user_index.twig", $data);
	}
	
	/**
	 * @Route("/admin/openqueue")
	 */
	public function openQueue()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
	
	/**
	 * @Route("/admin/closequeue")
	 */
	public function closeQueue()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
	
	/**
	 * @Route("/admin/resetqueue")
	 */
	public function resetQueue()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
	
	/**
	 * @Route("/admin/thedoctorisin")
	 */
	public function doctorIsIn()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
	
	/**
	 * @Route("/admin/thedoctorisout")
	 */
	public function doctorIsOut()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
	
	/**
	 * @Route("/admin/historylog")
	 */
	public function viewHistoryLog()
	{
		$tabledata = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/sample_table.json'), true); // this is sample data. generate data from database query.
		return $this->render("templates/table.twig", $tabledata);
	}
}

?>
