<?

    // автоматическая подгрузка классов
    function __autoload($name) {
        include_once($_SERVER['DOCUMENT_ROOT']."/include/".mb_strtolower($name).".class.php");
    }
    if(!isset($_GET['voidpass'])){
        echo "ERROR";
        exit;
    }
    $mysqldump = "mysqldump"; // файл дампа базы
    if(isset($_GET['win'])){
        $mysqldump = "d:\\xampp\\mysql\\bin\\mysqldump.exe"; // файл дампа базы под виндовс на моей машине
    }
    if(isset($_GET['export'])){
        exportBase($mysqldump);
    }
    if(isset($_GET['import'])){
        importBase();
    }
	// Дамп базы
	function exportBase($mysqldump_file)
	{
		$fullFileName = '../_SQL/database.sql';
		if(trim(Config::$base_pass)){
			$command = $mysqldump_file.' -h'.Config::$base_host .' -u'.Config::$base_user.' -p'.Config::$base_pass.' '.Config::$base_name.' > '.$fullFileName;
		} else {
			$command = $mysqldump_file.' -h'.Config::$base_host .' -u'.Config::$base_user.' '.Config::$base_name.' > '.$fullFileName;
		}
        shell_exec($command);
        echo $command."<br>";
		echo "Таблицы успешно экспортированы {$fullFileName}";
	}	
	// Обновить базу
	function importBase()
	{
		$fullFileName = '../_SQL/database.sql';
		// соединение с базой
		DataBase::connect(Config::$base_type,Config::$base_host,Config::$base_name,Config::$base_user,Config::$base_pass);
		$base = new DataBase();
		$templine = '';
		$lines = file($fullFileName);
		foreach ($lines as $line){
			if (substr($line, 0, 2) == '--' || $line == '')
			continue;
			$templine .= $line;
			if (substr(trim($line), -1, 1) == ';'){
				$base->sql($templine);
				$templine = '';
			}
		}
		echo "Таблицы успешно импортированы";
    }
    
?>