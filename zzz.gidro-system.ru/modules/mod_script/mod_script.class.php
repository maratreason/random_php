<?
class mod_script extends Module{

	public function main($param){

		$out = "";
        $data = (array)json_decode($param['vars'],true);
        $data["_mod"] = $param;
		$out = Engine::templater(Engine::$root.Config::$front_template."/scripts/{$param['menu_pages']}.php",$data);
		return $out;
	}
}


?>
