<?php
namespace AppBundle\Model;

class Sidebar
{
	private $sidebarData;
	
	public function __construct()
	{
		$this->sidebarData = json_decode(file_get_contents($this->get('kernel')->getRootDir().'/Resources/json/admin_sidebar.json'), true);
	}
	
	public function generateSidebar()
	{
		$finalsidebar = 
		return $this->sidebarData;
	}
}

?>