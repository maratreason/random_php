<?
class mod_lite_menu extends Module{

	public function main($param){

		$out = "";
		$first = true;
		$pages = explode(",",$param['menu_pages']);
		$page = new Page();
		foreach($pages as $id){
			$id = trim($id);
			if(!$id) continue;
			if(!$first) {$out .= $param['menu_border'];}
			$first = false;
			$row = $page->get($id)->assoc();
            if($row){
                // если домены не совпадают, то перепишем урл с доменом
                if(Engine::$domain!=Config::$domains[$row['domain']]) $row['url'] = "http://".Config::$domains[$row['domain']].$row['url'];
                if(substr_count(Engine::$url,$row['url']."/")==0&&Engine::$url != $row['url']){
                    $out .= Engine::templaterKeys($param['menu_link'],array("[#link#]"=>$row['url'],"[#name#]"=>$row['url_name'],"[#id#]"=>$row['id']));
                } else {
                    $out .= Engine::templaterKeys($param['menu_selectlink'],array("[#link#]"=>$row['url'],"[#name#]"=>$row['url_name'],"[#id#]"=>$row['id']));
                }
            } else {
                Engine::errorMod($param, "id={$id} страницы не найден");
            }
		}
		$out = Engine::templaterKeys($param['menu_block'],array("[#menu#]"=>$out));
		return $out;
		
	}
}


?>