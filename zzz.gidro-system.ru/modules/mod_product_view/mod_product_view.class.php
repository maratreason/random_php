<?
/*
	Выводит продукт согласно шаблону
*/
class mod_product_view extends Module{
	
	public function main($param){
		$out = "";
		$template = Engine::$root.Config::$front_template."/{$param['template_list']}.html";
		$data = Engine::$page;

		$content = $data['content'];
		 
		$s1 = strpos($content, '<h1');
		$s2 = strpos($content,'</h1>',$s1);
		// отделяем заголовк и название продукта
		$data['_title'] = substr($content, $s1, ($s2-$s1+5));
		$data['_content'] = str_replace($data['_title'],"",$content);	
		$data['_gallery'] = Engine::moduleByName('product-images');
		
		// получим другие товары из раздела
		$good = new Page();
		$good->select("WHERE parent=:parent AND unit = '{$data['unit']}' AND id != {$data['id']} ORDER BY rand() LIMIT {$param['image_count']}",array(":parent"=>$data['parent']));
		while($item = $good->next()){
			$img =  explode(";",$item['multi_images']);
			$img = $img[0];
			
			$new_image = dirname($img)."/preview-".basename($img);
			if(!file_exists(Engine::$root.$new_image)&&file_exists($img)){
				$image = new SimpleImage();
				$image->load(Engine::$root.$img);
				$image->resizeToWidth(300);
				$image->save(Engine::$root.$new_image);
			}
			$item['_image'] = $new_image;
			$data['_goods'][] = $item;
		}
		$out = Engine::templater($template,$data);
		// заменяем текущую контентную область на новую
		Engine::$page['content'] = $out;
		return "";
		
	}
}


?>