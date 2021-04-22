<?
class Module extends DataBase{
	public $mod_path;// путь до модуля
	public $mod_name;
	public $GET;

	public function __construct(){
		$this->setTable("modules");
	}
	public function Init(){
		// считываем урл и строим из него GET запрос
		$url_params = preg_replace("@".Engine::$page['url']."@","",Engine::$url,1);
		$this->GET = Engine::urlToParams($url_params);
	}

	public function setFile($_file,$flag=0){
		$ext = pathinfo($_file, PATHINFO_EXTENSION);
		if(!$flag){
			if($ext === "js") {Engine::addMeta("<script type='text/javascript' src='/modules/{$this->mod_name}/{$_file}'></script>\n");}
			if($ext === "css") Engine::addMeta("<link rel='stylesheet' href='/modules/{$this->mod_name}/{$_file}'/>\n");
		} else {
			if($ext === "js") {Engine::addMeta("<script type='text/javascript' src='{$_file}'></script>\n");}
			if($ext === "css") Engine::addMeta("<link rel='stylesheet' href='{$_file}'/>\n");			
		}
	}

}


?>
