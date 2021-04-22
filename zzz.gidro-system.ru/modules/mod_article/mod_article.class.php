<?
/*
	Выводит статью
*/
class mod_article extends Module{
	
	public function main($param){
		$out = "";
		$data = Engine::$page;
		
		if($param['image_show']){
			$data['preview_image'] = "<p><img src='{$data['url_image']}' alt='{$data['url_name']}'></p>";
		} else $data['preview_image'] = "";
		
		$flag = false;
		// вывести дату
		if($param['date_show']){
			$data['content_date'] = "<span class='date'>".Engine::rusDate("d F Y", strtotime($data['content_date']));
			if($param['category_show']||$param['author_show']) $data['content_date'] .=" | ";
			$data['content_date'] .= "</span>";
			$flag = true;
		} else $data['content_date'] = "";
		
		// показать раздел
		if($param['category_show']){
			$parent = new Page();
			$parent = $parent->get($data['parent'])->assoc();
			$data['category'] = "<a href='{$parent['url']}'>{$parent['url_name']}</a>";
			if($param['author_show']) $data['category'] .=" | ";
			$flag = true;
		} else $data['category'] = "";
		
		// показать автора
		if($param['author_show']){
			//$data['author'] = "Автор: ".$data['author'];
			$base = new DataBase();
			$base->setTable("users");
			$author = $base->get(array("name"=>trim($data['author']),"status"=>1))->assoc();
			if($author){
				$data['author'] = $author['fio'];
				$flag = true;
			} else {
				$data['author'] = "";
			}
		} else $data['author'] = "";
		// если надо вывести хоть один из информационных полей
		if($flag){
			$data['option'] = "<p class='article-option'>{$data['content_date']}{$data['category']}{$data['author']}</p>";
		}
		$data['_comment_show'] = $param['comment_show'];
		$out .= Engine::templater(Engine::$root.Config::$front_template."/article.html",$data);
		return $out;
		
	}
}


?>