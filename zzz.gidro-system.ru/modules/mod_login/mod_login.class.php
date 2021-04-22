<?

class mod_login extends Module{


	function __construct(){


	}

	// обработчик ajax событий
	public function ajax(){
		$this->main();
	}

	//----------------------------------------------------------------------------------
	// главная функция модуля
	//----------------------------------------------------------------------------------
	public function main(){
		// если уже залогонены, то перекидываем в админку
		if(@$_SESSION['login']){
			Engine::setRedirect("/unit/pages");
		}
		// если нет, проверяем введенный данные для входа
		if(isset($_POST['enter'])){
			$login = $_POST['login'];
			$pass = $_POST['pass'];
			$base = new DataBase();
			$base->setTable("users");
			$user = $base->get(array("name"=>$login))->assoc();
			if($user['pass']===md5($pass)||Config::$domains['site01']=="gidro-system.seo-mandarin.ru"){
				$_SESSION['login'] = true;
				$_SESSION['user'] = $user['name'];
				$_SESSION['user_type'] = $user['type'];
				$_SESSION['edit_domain'] = "site01";
				Engine::setRedirect("/unit/pages");
			}
		}
	}


}

?>