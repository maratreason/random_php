<?
/*
	Выводит соайдер эелементов
*/
class mod_slider_item extends Module{
	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_slider_item"; // получим класс
		if(!Engine::$admin){
			
			// стандартная инициализация модуля
			$this->Init();
		} else {
			// скрипты админской части

		}
	}
    
	public function main($param){
        $this->setFile("jed-itemslider.js"); 
        $this->setFile("jed-itemslider.css"); 
        $page = new Page();
        $items = $page->select("WHERE {$param['sql_text']}")->all();
        $data['_block'] = $items;
        $out = Engine::templater(dirname(__FILE__)."/slider.html",$data);
		return $out;		
	}
}


?>