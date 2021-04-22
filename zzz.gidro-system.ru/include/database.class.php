<?
// класс работы с базой данных
// jedCoder
//----------------------------------------------------------------------------------------
class DataBase{

	static $base;
	private $query;
	private $table;
	public $row; // текущая строка
	// в качестве параметра указывается база с которой необходимо делать выборки
	function __construct(){
		// если вызван с параметром то сразу устанавливаем таблицу
		if(func_num_args()==1) $this->table = func_get_arg(0);
	}

	//-----------------------------------------------------------------------------------
	// установить новую таблицу для выборок
	//-----------------------------------------------------------------------------------
	public function setTable($_table){
		$this->table = $_table;
	}

	//-----------------------------------------------------------------------------------
	// Соединение с базой данных
	// статична для всех классов, вызывается один раз как метод класса для инициализации
	//-----------------------------------------------------------------------------------
	static function connect($_type,$_host,$_dbname,$_user,$_pass) {

		try {
			self::$base = new PDO("{$_type}:host={$_host};dbname={$_dbname}", $_user, $_pass);
			self::$base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$base->exec("set names utf8");
		}catch(PDOException $e) {
			echo $e->getMessage();
		}
	}

	//-----------------------------------------------------------------------------------
	// свой любой прямой запрос
	//-----------------------------------------------------------------------------------
	public function sql($_sql,$_values = 0) {
		$this->query = self::$base->prepare($_sql);
		if($_values) 
			$this->query->execute($_values); 
			else $this->query->execute();
		return $this;
	}

	//-----------------------------------------------------------------------------------
	// количество строк
	//-----------------------------------------------------------------------------------
	public function count(){
		return $this->query->rowCount();
	}

	//-----------------------------------------------------------------------------------
	// свой прямой запрос SELECT
	//-----------------------------------------------------------------------------------
	public function select($_sql,$_values = 0) {
		$this->query = self::$base->prepare("SELECT * FROM {$this->table} {$_sql}");
		if($_values) $this->query->execute($_values); else $this->query->execute();
		return $this;
	}

	//-----------------------------------------------------------------------------------
	// выборка одной строки из запроса в виде массива
	//-----------------------------------------------------------------------------------
	public function assoc(){
		$this->row = $this->query->fetch(PDO::FETCH_ASSOC);
		return $this->row;
	}
	public function next() {return $this->assoc();}

	// вся выборка из запроса в виде массива
	public function all(){
		return $this->query->fetchAll();
	}

	//-----------------------------------------------------------------------------------
	// достать строки из таблицы согласно указанным параметрам
	// может принимать от 0 до 2-х параметров
	// - без параметров достает все строки
	// - 1 параметр, достает по id если число, достает по множествам полям если это массив
	// - 2 параметра, поле в базе, значение в базе
	//-----------------------------------------------------------------------------------
	public function get() {
		if(func_num_args()==2) {
			// передаётся поиск значения в указанном поле,
			// 1-й аргумент это искомое значение, 2-й - это поле в котором искать
			$_value = func_get_arg(0);
			$_field = func_get_arg(1);
		} elseif(func_num_args()==1&&is_array(func_get_arg(0))) {
			// передаётся массив, поиск идёт сразу по нескольким полям
			return $this->getMulti(func_get_arg(0));
		} elseif(func_num_args()==1) {
			// передайтся 1 аргумент и это не массив, значит искать строку с задданм id
			$_value = func_get_arg(0);
			$_field = "id";
		}
		if(func_num_args()==0){
			// функция вызвана без параметров, получить все строки из таблмцы
			$this->query = self::$base->prepare("SELECT * FROM {$this->table}");
		} else {
			// получить определённые строки
			$this->query = self::$base->prepare("SELECT * FROM {$this->table} WHERE {$_field} = :value");
			$this->query->bindParam(":value",$_value);
		}
		$this->query->execute();
		return $this;
	}
	public function rowCount(){
		return $this->query->rowCount();
	}
	//-----------------------------------------------------------------------------------
	// вытаскивает строки в заданных полях из массива
	// Для оператора OR в значении необходимо передать списко значений разделённых |
	// перед значение можно указать оператор = ! > < @(LIKE)
	//-----------------------------------------------------------------------------------
	private function getMulti($_columns){
		$SELECT = "*";
		$BONUS = "";
		if(isset($_columns['*'])){
			$SELECT = $_columns['*'];
			unset($_columns['*']);
		}
		if(isset($_columns['sql'])){
			$BONUS = $_columns['sql'];
			unset($_columns['sql']);
		}
		$sql = "SELECT {$SELECT} FROM {$this->table} WHERE ";
		$or_values = array();// массив OR
		$first = true;
		// создаем запрос
		foreach($_columns as $col=>$val){
			// пропускаем значниея где условие требует все значения, иначе избыточно			
			if($val=="*") {
				unset($_columns[$col]);
				continue;
			}
			if(!$first) {$sql.=" AND ";}
			$ors = explode("|",$val);
			// смотрим оператор OR
			$sql .="(";
			$first_or = true;
			foreach($ors as $i=>$or){
                // получаем оператор, = > < ! @-LIKE
                $operator = @$or[0];
                if(!in_array($operator, array("=",">","<","!","@","#"))) {
					$operator = "="; // если не указано оператор сравнивания
					$value = $or;
				} else {
					$value = substr($or,1); // если был оператор, у значения уберем первый символ
				}
                if($operator=="!") $operator = "!=";
                if($operator=="@") $operator = "LIKE";
				if($operator=="#") $operator = "NOT LIKE";
				if(!$first_or) {$sql.=" OR ";}
				$sql .= "{$col} {$operator} :{$col}{$i}";
                $or_values[$col.$i] = $value;
				$first_or = false;
			}
			$sql .=")";
			unset($_columns[$col]);
			$first = false;
		}

		/*$sql .= " ".$BONUS;
		echo $sql."<br>";
		print_r($or_values);
		echo "<br>------------------<br>";*/

		$_columns = array_merge($_columns,$or_values);
		$this->query = self::$base->prepare($sql.$BONUS);
		$this->query->execute($_columns);
		return $this;
	}
	//-----------------------------------------------------------------------------------
	// записывает значения в базу, автоматически определят добавить/обновить запись
	// если указать ID и массив, то обновит строку по ID
	// если передать массив и sql запрос, то обновит согласну sql запросу
	//-----------------------------------------------------------------------------------
	public function set(){
		// епердаём условие поиска строки в которую записываем
		if(is_array(func_get_arg(0))){
			$_values = func_get_arg(0);
			$_sql = func_get_arg(1);
		} else { // запись идёт по указанному id
			$_sql = "WHERE id = '".func_get_arg(0)."'";
			$_values = func_get_arg(1);
		}

		$sql = self::$base->prepare("SELECT * FROM {$this->table} {$_sql}");
		$sql->execute();
		// проверяем обновить запись или добавить
		if($sql->rowCount()>0)
		{
			// обновляем запись
			$str = "";
			$count = 0;
			foreach($_values as $key=>$value)
			{
				if($count>0) $str .= ",";
				$count++;
				$str .= " {$key}=:{$key}";
                if($this->getFieldType($key)==="int(11)"||$this->getFieldType($key)==="decimal(9,2)") {
                    if(trim($value)=="") $_values[$key] = 0;
                }
                // в поле date всегда записываем текущую дату и время
				if($key==="date"){
					$value = date("d/m/Y H:i:s",mktime());
				}
				if($this->getFieldType($key)==="datetime") {
					$value = str_replace("/",".",$value);
					$_values[$key] = date("Y-m-d H:i:s",strtotime($value));
				}

			}
			$request = "UPDATE {$this->table} SET {$str} {$_sql}";
		}
		else
		{
			// добавляем новую запись
			$str1 = "(";
			$str2 = "(";
			$count = 0;
			foreach($_values as $key=>$value)
			{
				if($count>0) {$str1 .= ",";$str2 .= ",";}
				$count++;
				$str1 .= " {$key}";
				// в поле date всегда записываем текущую дату и время
				if($key==="date"){
					$value = date("d/m/Y H:i:s",mktime());
				}

                if($this->getFieldType($key)==="int(11)"||$this->getFieldType($key)==="decimal(9,2)") {
                    if(trim($value)=="") $_values[$key] = 0;
                }

				if($this->getFieldType($key)==="datetime") {
					$value = str_replace("/",".",$value);
					$_values[$key] = date("Y-m-d H:i:s",strtotime($value));
					//echo $this->getFieldType($key)." = datetime $key = | {$value} :  {$_values[$key]} |";
				}

				$str2 .= " :{$key}";

			}
			$str1 .=")";
			$str2 .=")";
			$request = "INSERT INTO {$this->table}{$str1} VALUES {$str2}";
		}
		$sql = self::$base->prepare($request);
		$sql->execute($_values);
		$return = self::$base->lastInsertId();
		return $return;
	}

	public function delete($_id){
		$values = array(":id"=>$_id);
		return $this->sql("DELETE FROM {$this->table} WHERE id = :id",$values);
	}

	//-------------------------------------------------------------------------------------------------------
	// создание новой чистой таблицы / модификация таблицы происходит автоматически при добавлении данных
	//-------------------------------------------------------------------------------------------------------
	public function createTable($_table){
		$sql = "CREATE TABLE IF NOT EXISTS {$_table} (id int NOT NULL AUTO_INCREMENT, PRIMARY KEY(id))";
		return $this->sql($sql);
	}

	// получение имен всех колонок таблицы в виде массива
	public function getFields(){
		$fields = $this->sql("SHOW COLUMNS FROM {$this->table}")->all();
		$field = array();
		foreach($fields as $f){
			$field[$f['Field']] = true;
		}
		return $field;
	}
	// создание колонки в таблице
	public function createField($_name,$_type){
		//$values = array(":name"=>$_name,":type"=>$_type);
		$sql = "ALTER TABLE {$this->table} ADD {$_name} {$_type} NULL";
		return $this->sql($sql);
	}

	// удаление колонки в таблице
	public function deleteField($_name){
		$values = array(":name"=>$_name);
		return $this->sql("ALTER TABLE {$this->table} DROP COLUMN :name",$values);
	}
	// возвращает тип поля
	public function getFieldType($_name){
		$values = array(":name"=>$_name);
		$sql = "SHOW COLUMNS FROM {$this->table} WHERE field = :name";
		$type = $this->sql($sql,$values)->assoc();
		return @$type["Type"];
	}

	public function setPostFunctions(){
		if($this->row['post_func']){
			$line = explode("\n",$this->row['post_func']);
			foreach($line as $kv){
				$kv = explode("=",$kv);
				$param = trim($kv[0]);
				if(trim($kv[1])!=""){
					$func = "post_".trim($kv[1]);
					$this->row[$param] = Post::$func($this->row,$param);
				}
			}
		}
	}

	public function setPreFunctions(){
		if($this->row['pre_func']){
			$line = explode("\n",$this->row['pre_func']);
			foreach($line as $kv){
				$kv = explode("=",$kv);
				$param = trim($kv[0]);
				$func = "pre_".trim($kv[1]);
				$this->row[$param] = pre::$func($this->row,$param);
			}
		}
	}
		
}

?>
