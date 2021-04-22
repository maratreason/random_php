<?
class mod_cat_menu extends Module{

	private function getTree($parents,$param, $level){
        $city = func::getCityParams();
        $reg = trim(Engine::constant("serviceRegion"));
		static $tree = "";
		if(!$tree) $tree = func::getAllParents(Engine::$page['id']);
		$out = "";
		$first = true;
		$pages = new Page();
        $parent_list = explode(",", $parents);
		$show = $param['menu_show'];
        if(count($parent_list)>1){
            foreach($parent_list as $parent){
                $parent = trim($parent);
                $down = true;
                if($parent<0) {$parent *=-1;$down = false;}
                //$list = $pages->get($parent,"id")->assoc();
				$sql= "";
				if($param['sql_text']!="") $sql = "AND ".$param['sql_text'];
				$list = $pages->sql("SELECT * FROM pages WHERE id = {$parent} {$sql} ORDER BY prior")->assoc();
                $list['_down'] = $down;
                $pages_list[] = $list;
            }

        } else {
            //$pages_list = $pages->get($parent_list[0],"parent")->all();
			$sql= "";
			if($param['sql_text']!="") $sql = "AND ".$param['sql_text'];
            $pages_list = $pages->sql("SELECT * FROM pages WHERE parent = {$parent_list[0]} {$sql} ORDER BY prior")->all();
            
        }
		$_url = $_SERVER['REQUEST_URI'];
        foreach($pages_list as $row){
            if(@$row['status']==0) continue;
            if(!$first) {$out .= $param['menu_border'];}
            $row['url_name'] = strip_tags($row['url_name']);
            $first = false;

			if(array_search($row['id'],$tree)===false){
                if($level==0) {
                    $item = Engine::templaterArrayKeys($param['menu_link'],$row);
                } else {
                    $item = Engine::templaterArrayKeys($param['menu_link_2'],$row);
                }
				if($show) $row['_down'] = false;
            } else {
                if($level==0) {
                    $item = Engine::templaterArrayKeys($param['menu_selectlink'],$row);
                } else {
                    $item = Engine::templaterArrayKeys($param['menu_selectlink_2'],$row);
                }
				if($show) $row['_down'] = true;
            }
            if(!isset($row['_down'])) $row['_down'] = true;
            if($row['type'] == 1&&$param['menu_max_level']>$level+1&&$row['_down']) {
				$cathtml = $this->getTree($row['id'],$param, $level+1);
                if(trim($cathtml)!="") {
					$out .= Engine::templaterKeys($param['menu_block_category'],array("[#item#]"=>$item,"[#menu#]"=> $cathtml,"[#id#]"=>$row['id']));
				} else {
					$out .= $item;
				}
            } else {
                $out .= $item;
            }
        }

		return $out;
	}


	public function main($param){
		$out = "";
		$first = true;
        // страницы брать из текущего раздела (текущая страница должна быть разделом)
        if($param['menu_type']==0){
			$out = $this->getTree(Engine::$page["id"],$param,0);
		}
        // страницы брать из указанного раздела
		if($param['menu_type']==1){
			$out = $this->getTree($param['menu_category'],$param,0);
		}
        // страницы брать из списка указанных страниц
		if($param['menu_type']==2){
			$out = $this->getTree($param['menu_list'],$param,0);
		}

        $out = Engine::templaterKeys($param['menu_block'], array("[#menu#]"=>$out));
		return $out;

	}
}


?>
