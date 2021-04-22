<?
class mod_path extends Module{

	function __construct(){

		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_path"; // получим класс
        //скрипты фронтальной части
        if(!Engine::$admin){

        }

        // стандартная инициализация модуля
        $this->Init();
	}

    public function main(){

		$out = "";
		$page = new Page();
		$parent = Engine::$page['parent'];
		$path = array();
		$path[] = Engine::$page;
		while($parent>0){
			$page->get($parent)->assoc();
			$path[] = $page->row; 
			$parent = $page->row['parent'];
		}
		
		$count = count($path);
		foreach($path as $n=>$item){
			$slesh = " / ";
			if($count-$n==1)  $slesh = "";
			$out = "{$slesh}
					<span itemscope='' itemprop='itemListElement' itemtype='http://schema.org/ListItem'>
					<a rel='nofollow' itemprop='item' title='".$item['url_name']."' href='".$item['url']."'>
					<span itemprop='name'>".strip_tags($item['url_name'])."</span>
					<meta itemprop='position' content='".($count-$n)."'></a></span>".$out;
		}
        return "<div class='url' itemscope='' itemtype='http://schema.org/BreadcrumbList' id='breadcrumbs'>{$out}</div>";
	}
}
?>


