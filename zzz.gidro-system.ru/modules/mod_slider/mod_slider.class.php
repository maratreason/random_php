<?
/*Модуль управления слайдером*/
class mod_slider extends Module{
	
	static public $content = "";
	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_slider"; // получим класс
		if(!Engine::$admin){
			//скрипты фронтальной части
			$this->setFile("imagesloaded.min.js");
			$this->setFile("jquery.finger.min.js");
			$this->setFile("slider.js"); // подключим js файл модуля
            $this->setFile("slider.css"); // подключим js файл модуля
			// стандартная инициализация модуля
			$this->Init();
		} else {
			// скрипты админской части

		}
	}
	
	// админская часть модуля
	public function admin($param){
		
		$out = mod_admin::createButton("Редактировать слайдеры",mod_admin::$admin_url."/base-sliders","icon-edit.png","inline-button");		
		return $out;
	}
	
	public function main($param){
		$slider = new DataBase();
		$slider->setTable("sliders");
		$data['slider'] = $slider->select("WHERE status = 1 ORDER BY priority")->all();
		
		$out = Engine::templater(dirname(__FILE__)."/slider.html",$data);
		return $out;		
	}
}


?>