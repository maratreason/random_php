<?
/*
	Панель на странице
*/
class mod_basket extends Module{

	function __construct(){
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_basket"; // получим класс
		$this->setFile("basket.js"); // подключим js файл модуля
		// стандартная инициализация модуля
		$this->Init();
	}

	// обработчик ajax событий
	public function ajax(){
		// добавить товар, установить количество товара
		if(isset($this->GET['add'])){
			$id = $this->GET['add'];
			$page = new Page();
			$page = $page->get(array("*"=>"id","id"=>$id))->assoc();
			if($page['id']){
				if(isset($this->GET['count'])){
					$_SESSION['basket'][$id] = $this->GET['count'];
				} else {
					if(!isset($_SESSION['basket'][$id])) $_SESSION['basket'][$id] = 1;
					// найдем все связанные товары и тоже добавим в корзину
					// if(trim($page['parent_item'])!==""){
					// 	$pages = new Page();
					// 	$parents = explode(",",$page['parent_item']);
					// 	$parents = array_diff($parents, array(''));
					// 	$pages = $pages->get(array("*"=>"id","id"=>implode("|",$parents)))->all();
					// 	foreach ($pages as $item) {
					// 		if(!isset($_SESSION['basket'][$item['id']])) $_SESSION['basket'][$item['id']] = 1;
					// 	}
					// }
				}
				return json_encode($_SESSION['basket'],true);
			}
		}

		// удалить полностью товар
		if(isset($this->GET['remove'])){
			$id = $this->GET['remove'];
			unset($_SESSION['basket'][$id]);
			// найдем связанные товары и удалим их
			// $pages = new Page();
			// $pages = $pages->get(array("*"=>"id"))->all();
			// foreach ($pages as $item) {
			// 	unset($_SESSION['basket'][$item['id']]);
			// }

			// // удалим карточки которые нельзя выбрать без выбора родителей
			// if(count($_SESSION['basket'])>0){
			// 	$keys = array_keys($_SESSION['basket']);
			// 	$pages = new Page();
			// 	$pages = $pages->get(array("*"=>"id,bound_item","id"=>implode("|",$keys)))->all();
			// 	foreach ($pages as $page) {
			// 		if(trim($page['bound_item'])!=""){
			// 			$bounds = explode(",",$page['bound_item']);
			// 			$bounds = array_diff($bounds, array(''));
			// 			$count = 0;
			// 			foreach ($bounds as $bound) {
			// 				if(isset($_SESSION['basket'][$bound])) $count++;
			// 			}
			// 			if($count==0) unset($_SESSION['basket'][$page['id']]);
			// 		}
			// 	}
			// }
			return json_encode($_SESSION['basket'],true);
		}

	}

	// плашка с корзиной
	public function basketPanel(){
		if(!isset($_SESSION['basket']))
		$_SESSION['basket'] = array();
		$data['count'] = count($_SESSION['basket']);
		$data['disable'] = "disable";
		if($data['count']>0) $data['disable'] = "";
		$out = Engine::templater($this->mod_path."/basketPanel.html",$data);
		return $out;
	}

	public function main($param=0)
	{
		$out = "";
		if (!isset($_SESSION['basket'])) {
			$keys = [];
		} else {
			// вывод корзины
			$keys = array_keys($_SESSION['basket']);
			$page = new Page();
			if(count($keys)>0){
				$pages = $page->get(array("id"=>implode("|",$keys)))->all();
			} else $pages = array();
				$out = Engine::templater($this->mod_path."/basketTable.html",$pages);
		}
		
		return $out;
	}

}

?>
