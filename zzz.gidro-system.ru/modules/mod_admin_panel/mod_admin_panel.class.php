<?
/*
	Панель на странице
*/
class mod_admin_panel extends Module{

	function __construct(){
		if(@$_SESSION['login']==true){
			$this->mod_path = dirname(__FILE__);
			$this->mod_name = "mod_admin_panel"; // получим класс
			//$this->setFile("js/panel.js"); // подключим js файл модуля
			$this->setFile("style.css"); // подключим js файл модуля
			// стандартная инициализация модуля
			$this->Init();
		}
	}

	public function main($param=0){
		if(@$_SESSION['login']==true){
			$out = Engine::templater($this->mod_path."/panel.html");
			return $out;
		}
	}

	// обработчик ajax событий
	public function ajax(){
		if(@$_SESSION['login']==true){

		}
	}
}


?>
