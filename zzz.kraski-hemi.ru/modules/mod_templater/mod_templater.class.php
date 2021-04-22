<?
class mod_templater extends Module{

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
        $com = $_GET['com'];
        $temp = $_GET['temp'];
        $dir = Engine::$root."/templates/jed";
        if($com=='build'){
            $html = file_get_contents("{$dir}/block/bone/header.html");
            $css = file_get_contents("{$dir}/block/bone/style.css");
            
            $file = "{$dir}/{$temp}.txt";
            if(file_exists($file)){
                $file_handle = fopen($file, "r");
                while (!feof($file_handle)) {
                    $block = trim(fgets($file_handle));
                    // ищем и собираем файлы блоков
                    $html .= file_get_contents("{$dir}/block/{$block}/temp.html");
                    if(file_exists("{$dir}/block/{$block}/style.css")){
                        $css .= file_get_contents("{$dir}/block/{$block}/style.css");
                    }
                }
                fclose($file_handle);
                $html .= file_get_contents("{$dir}/block/bone/footer.html");
                $data = Engine::$page;
                $data['style'] = "<style>{$css}</style>";
                
                $out = Engine::templaterString($html,$data);
            } else {
                $out = "Файл сборки не найден";
            }
        }
        
        
        return $out;
	}
}
?>