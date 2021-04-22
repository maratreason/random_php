<?
class mod_popular_count extends Module{

	public function main($param){

		//$page = new Page();
		$pop = Engine::$page["popular"]+$param['count'];
		$page = new Page();
		$page->set(Engine::$page['id'],array("popular"=>$pop));
	}
}


?>