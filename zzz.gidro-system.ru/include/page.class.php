<?
// класс страницы страница
class Page extends DataBase{
	
	function __construct(){
		$this->setTable("pages");
	}
	
	public function template(){
		 
		if(($this->row)){
			if($this->row['type']==1000) $tmp = Config::$back_template; else $tmp = Config::$front_template;
			/*$unitxml = Engine::$root."/units/page/{$this->row['unit']}.xml";*/
			if(($this->row['unic_template'])){
				$template = $this->row['unic_template'];
			} else {
				$template = $this->row['template'];
			}

			$path = Engine::$root."{$tmp}/{$template}.html";
			if(file_exists($path)){
				$this->row['_template_path'] = $tmp;
				return Engine::templater($path,$this->row);
			} else {
				return "Page: Шаблон [{$tmp}/{$template}.html] не найден";
			}
			
		} else {
			return "Page: Пустая строка выборки";
		}
		
	}
}



?>