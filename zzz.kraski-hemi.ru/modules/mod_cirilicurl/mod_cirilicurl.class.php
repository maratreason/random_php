<?
// автоматическая генерация кирилического урл
class mod_cirilicurl extends Module{

	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_cirilicurl"; // получим класс
        //скрипты фронтальной части
        if(!Engine::$admin){

        }

        // стандартная инициализация модуля
        $this->Init();
	}
	
    // обработчик ajax событий
	public function ajax(){
		$parent = $_POST['param']['parent'];
        $name = $_POST['param']['url_name'];
        $page = new Page();
        $url = $page->get($parent)->assoc();
        $set = $url['url'];
        if(isset($url['url_generic'])) if($url['url_generic']!="") $set = $url['url_generic'];
        $slesh = "/";
        if($set=="/") $slesh = "";
        $url = $set.$slesh.func::translit($name);
        return $url;

	}
    
    public function main(){
		

	}
}
?>