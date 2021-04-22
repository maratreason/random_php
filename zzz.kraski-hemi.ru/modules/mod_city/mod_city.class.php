<?
// получаем список городов
class mod_city extends Module{

	function __construct(){

		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_city"; // получим класс
		//скрипты фронтальной части
		if(!Engine::$admin){

		}

		// стандартная инициализация модуля
		$this->Init();
	}

	// обработчик ajax событий
	public function ajax(){
		// если запрос на поиск маршрута
		if(isset($_GET['query'])) {
			$rus = array('й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', 'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч', 'с', 'м', 'и', 'т', 'ь', 'б', 'ю');
			$eng = "qwertyuiop[]asdfghjkl;'zxcvbnm,.";
			// переделаем англ символы
			$str = $_GET['query'];
			$new_str = "";
			if (!preg_match('/[а-я\,\.]/ui', $str)) {
				for ($i = 0; $i < strlen($str); $i++) {

					$pos = strpos($eng, $str[$i]);
					$new_str .= $rus[$pos];
				}
				$str = $new_str;
			}

			$mas = "[";
			$cities = new DataBase();
			$cities->setTable('cities');
			$city = $cities->sql("SELECT * FROM cities WHERE i LIKE '{$str}%' LIMIT 10");
			$flag = 0;
			while ($c = $city->assoc()) {
				if ($flag) $mas .= ",";
				$flag = 1;
				$mas .= '{"value":"' . $c['i'] .'", "data":"' . $c['i_eng'] . '","region": "'.$c['region'].'"}';
			}

			$mas .= "]";
			$mas = '{
			"query": "Unit",
			"suggestions":' . $mas . '}';
			return $mas;
		}
		// если запрос на проверку существования города
		if(isset($_GET['city'])){
			$city = $_GET['city'];
			$cities = new DataBase();
			$cities->setTable('cities');
			$c = $cities->get(array("i"=>$city))->assoc();
			if(isset($c['id'])) return '1'; else return '0';
		}
	}

	public function main(){


	}
}
?>