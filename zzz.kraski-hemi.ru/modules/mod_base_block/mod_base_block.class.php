<?
class mod_base_block extends Module{

	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_base_block"; // получим класс
        //скрипты фронтальной части
        if(!Engine::$admin){

        }

        // стандартная инициализация модуля
        $this->Init();
	}
	
    public function main($param){
		
		$template = Engine::$root.Config::$front_template."/views/{$param['template_list']}.html";
		$base = new DataBase();
		$base->setTable($param['base_name']);
		if($param['base_random']==0){
			$rec = $base->select("WHERE status = 1 ORDER BY {$param['base_sort']} LIMIT {$param['base_count']} ",array())->all();
		} elseif($param['base_random']==1){
			$rec = $base->select("WHERE status = 1 ORDER BY rand() LIMIT {$param['base_count']}",array())->all();
		}
		
		$data = $param;
		$data["_block"] = $rec;
		$out = Engine::templater($template,$data);
		


		
        return $out;
	}
}
?>