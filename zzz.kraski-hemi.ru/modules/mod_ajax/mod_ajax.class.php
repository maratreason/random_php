<?
class mod_ajax extends Module{

	function __construct(){
		$this->mod_path = dirname(__FILE__);
		// стандартная инициализация модуля
		$this->Init();
	}

	function main(){
		// передать команду модулю
		Engine::set404(0);
		if(isset($this->GET['mod'])){
			if(isset($this->GET['modname']))
				return Engine::moduleByName($this->GET['modname']);
			else
				return Engine::module($this->GET['mod'])->ajax();
		}
	}

}
?>
