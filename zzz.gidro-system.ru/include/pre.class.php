<?
class Pre {
	function pre_password($_row, $_param){
		if(!empty($_row[$_param]) && preg_match('/^[a-f0-9]{32}$/', $_row[$_param]))
			return $_row[$_param];
		else
			return md5($_row[$_param]);
	}

	function pre_setalias($_row, $_param){
		return $_row['url']."/*";
	}
// автоматический урл
	function pre_auto_url($_row,$_param){
		if($_row['url']=="//"||$_row['url']=="/"){
			$parent = $_row['parent'];
			$name = $_row['url_name'];
			$page = new Page();
			$url = $page->get($parent)->assoc();
			$set = $url['url'];
			if(isset($url['url_generic'])) if($url['url_generic']!="") $set = $url['url_generic'];
			$slesh = "/";
			if($set=="/") $slesh = "";
			$url = $set.$slesh.func::translit($name);
			return $url;
		} else {
			return $_row['url'];
		}

	}

// автоматический урл для карточек товаров
	function pre_item_url($_row,$_param){
		$parent = $_row['parent'];
		$name = $_row['url_name'];
		$page = new Page();
		$url = $page->get($parent)->assoc();
		$set = $url['url'];
		if(isset($url['url_generic'])) if($url['url_generic']!="") $set = $url['url_generic'];
		$slesh = "/";
		if($set=="/") $slesh = "";
		$url = $set.$slesh.func::translit($name);
		return $url;
	}
}
?>
