<?
/*
    Конвертирует файл карточки товара в фильтр
*/
    function genYaml($yaml, $tab=0){
        $str = "";
        foreach($yaml as $key=>$value){
            if(!is_array($value)){
                $str .= str_repeat("\t",$tab)."{$key}: \"{$value}\"\n";
            } else {
                // массив продолжается
                $str .= str_repeat("\t",$tab)."{$key}:\n";
                $str .= genYaml($value,$tab+1);
            }
        }
        return $str;
    }

    if($_SESSION['login']==false){
        echo "Ups!";
        //Engine::hardSet404();
    } else {

        $filter = array();
        $count = 0;
        $fileName = $_GET['file'];
        $file = Engine::$root."/units/page/{$fileName}.yaml";
        if(!file_exists($file)){
            echo "Файл {$fileName} не найден!";
            exit;
        }

        $yaml = Yaml::YAMLLoad($file);

        // файл для записи
        $tofile = Engine::$root."/modules/mod_multifilter/filters/filter_{$fileName}.yaml";
        // ищем файл фильтра в файле
        if(isset($yaml['tofilter'])){
            $tofile = Engine::$root."/modules/mod_multifilter/filters/".trim($yaml['tofilter']).".yaml";
        }
        // если указан конкретный файл в запросе то берем его
        if(isset($_GET['to'])){
            $tofile = Engine::$root."/modules/mod_multifilter/filters/{$_GET['to']}.yaml";
        }
        $toBaseFile = basename($tofile);
        // проверяем есть ли такой уже файлы
        $oldYaml = 0;
        if(file_exists($tofile)&&!isset($_GET['rewrite'])){
            $oldYaml = Yaml::YAMLLoad($tofile);
            echo "Обновляем файл {$toBaseFile}<br>";
        } else {
            echo "Создаем файл {$toBaseFile}<br>";
        }


        /*ШАПКА ФАЙЛА*/
        $filter['h1_start'] = "";
        $filter['h1_end'] = "";
        $filter['title_start'] = "";
        $filter['title_end'] = "";
        $filter['desc_start'] = "";
        $filter['desc_end'] = "";

        // сохраняем оптимизацию из уже существующего файла
        if($oldYaml){
            $filter['h1_start'] = $oldYaml['h1_start'];
            $filter['h1_end'] = $oldYaml['h1_end'];
            $filter['title_start'] = $oldYaml['title_start'];
            $filter['title_end'] = $oldYaml['title_end'];
            $filter['desc_start'] = $oldYaml['desc_start'];
            $filter['desc_end'] = $oldYaml['desc_end'];;
        }
        $filter['filters'] = array();
        foreach($yaml['areas'] as $key=>$data){
            if(!isset($data['gen'])) continue;
            $filter['filters'][$key] = array();
            $filter['filters'][$key]['caption'] = $data['caption'];
            $filter['filters'][$key]['name'] = str_replace("-","_",func::translit($data['caption']));
            $filter['filters'][$key]['h1_show'] = "true";
            $filter['filters'][$key]['h1_start'] = "";
            $filter['filters'][$key]['h1_end'] = "";
            $filter['filters'][$key]['title_start'] = "";
            $filter['filters'][$key]['title_end'] = "";
            $filter['filters'][$key]['desc_start'] = "";
            $filter['filters'][$key]['desc_end'] = "";
            /*ШАПКА ФИЛЬТРА*/
            // сохраняем оптимизацию из уже существующего файла
            if($oldYaml){
                echo "[{$key}]<br>";
                echo "name: {$oldYaml['filters'][$key]['name']}<br>";
                echo "h1_show: {$oldYaml['filters'][$key]['h1_show']}<br>";
                echo "h1_start: {$oldYaml['filters'][$key]['h1_start']}<br>";
                echo "h1_end: {$oldYaml['filters'][$key]['h1_end']}<br>";
                echo "title_start: {$oldYaml['filters'][$key]['title_start']}<br>";
                echo "title_end: {$oldYaml['filters'][$key]['title_end']}<br>";
                echo "title_start: {$oldYaml['filters'][$key]['desc_start']}<br>";
                echo "title_end: {$oldYaml['filters'][$key]['desc_end']}<br>";
                echo "<br>";

                if($oldYaml['filters'][$key]['name']) $filter['filters'][$key]['name'] = $oldYaml['filters'][$key]['name'];
                $filter['filters'][$key]['h1_show'] =       $oldYaml['filters'][$key]['h1_show'];
                $filter['filters'][$key]['h1_start'] =      $oldYaml['filters'][$key]['h1_start'];
                $filter['filters'][$key]['h1_end'] =        $oldYaml['filters'][$key]['h1_end'];
                $filter['filters'][$key]['title_start'] =   $oldYaml['filters'][$key]['title_start'];
                $filter['filters'][$key]['title_end'] =     $oldYaml['filters'][$key]['title_end'];
                $filter['filters'][$key]['desc_start'] =    $oldYaml['filters'][$key]['desc_start'];
                $filter['filters'][$key]['desc_end'] =      $oldYaml['filters'][$key]['desc_end'];
            }
            // формирование галочек
            if($data['editor']=="multiselect"){

                $filter['filters'][$key]['type'] = "multiselect";
                $filter['filters'][$key]['etype'] = "multiselect";
                $filter['filters'][$key]['select'] = array();
                //---------- формируем select ---------------
                foreach($data['select'] as $value=>$caption){
                    /*ПАРАМЕТРЫ ФИЛЬРА*/
                    $filter['filters'][$key]['select'][$value]['caption'] = $caption;
                    $filter['filters'][$key]['select'][$value]['h1'] = $caption;
                    $filter['filters'][$key]['select'][$value]['title'] = "";
                    $filter['filters'][$key]['select'][$value]['desc'] = "";
                    // сохраняем оптимизацию из уже существующего файла
                    if($oldYaml){
                        $filter['filters'][$key]['select'][$value]['h1'] = $oldYaml['filters'][$key]['select'][$value]['h1'];
                        $filter['filters'][$key]['select'][$value]['title'] = $oldYaml['filters'][$key]['select'][$value]['title'];
                        $filter['filters'][$key]['select'][$value]['desc'] = $oldYaml['filters'][$key]['select'][$value]['desc'];
                    }
                }
            }

            //----------- формирование ползунков ----------------
            if($data['editor']=="integer"){
                /*ПАРАМЕТРЫ ФИЛЬРА*/
                $filter['filters'][$key]['type'] = "range";
                $filter['filters'][$key]['etype'] = "integer";
                $filter['filters'][$key]['minmax'] = "1-100000";
            }
        }
        $yaml = genYaml($filter);
        //$file = Engine::$root."/modules/mod_multifilter/filters/filter_{$fileName}.yaml";
        $fd = fopen($tofile, 'w') or die("Не удалось создать файл");
        fwrite($fd, $yaml);
        fclose($fd);
        echo  "Фильтр создан";
    }
?>
