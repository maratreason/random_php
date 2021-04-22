<?
class mod_ublock extends Module{

	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_ublock"; // получим класс
        //скрипты фронтальной части
        if(!Engine::$admin){

        }

        // стандартная инициализация модуля
        $this->Init();
	}
	
    public function main($param){
		
		$template = Engine::$root.Config::$front_template."/views/{$param['template_list']}.html";
		$field = explode(",",$param['page_field']);
		// брать страницы по умолчанию
		//$flag = false;
        $block = array();
        $page = new Page();
        // проверим все поля на заполненость
        foreach($field as $fd){
            $fd = trim($fd);
            $block[$fd] = Engine::$page[$fd];
            // поля нет, ищем у родителя
            if(trim($block[$fd])===""){
                $p = $page->get(Engine::$page['parent'])->assoc();
                $block[$fd]= $p[$fd];
            }
            // берем со страницы по умолчанию
            if(trim($block[$fd])===""){
                $p = $page->get($param['cur_page_id'])->assoc();
                $block[$fd]= $p[$fd];
            }
        }
		if(file_exists($template)){
            $out = Engine::templater($template,$block);
        } else $out = "";
        return $out;
	}
}
?>