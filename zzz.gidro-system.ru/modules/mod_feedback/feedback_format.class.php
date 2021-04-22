<?
class feedback_format {
    // обработчик корзины
    public function basketFormat($json,$none=false){
        $json = json_decode($json,true);
        $out = "<table cellspacing='0' cellpadding='0' border='0' style='min-width:500px'>";
        $out .= "<tr>
                <td style='border-bottom:1px solid #EEE'>Название</td>
                <td style='border-bottom:1px solid #EEE'>Цена</td>
                <td style='border-bottom:1px solid #EEE'>Шт</td>
                <td style='border-bottom:1px solid #EEE'>Итого</td>
                </tr>";
        foreach($json as $id=>$item){
            if($id=="total") continue;
            $out .= "<tr>
                    <td style='border-bottom:1px solid #EEE;padding: 10px 10px 10px 0;'><a href='https://".$_SERVER['SERVER_NAME']."{$item['url']}'>{$item['name']}</a></td>
                    <td style='border-bottom:1px solid #EEE;padding: 10px 10px 10px 0;'>".number_format($item['price'],0,""," ")." руб.</td>
                    <td style='border-bottom:1px solid #EEE;padding: 10px 10px 10px 0;'>{$item['count']} шт</td>
                    <td style='border-bottom:1px solid #EEE;padding: 10px 10px 10px 0;'>".number_format($item['price-count'],0,""," ")." руб.</td>
                    </tr>";
        }
        $out .="</table>";
        $out .="<div style='font-weight:bold;padding:10px 10px 10px 0'>ИТОГО: ".number_format($json['total'],0,""," ")." руб.</div>";
        return $out;
    }

    public function checkbox($data){
        if(is_array($data)) {
            return implode(",", $data);
        } else {
            return $data;
        }
        
    }    
    public function onEvent($value,$formData){
        
        
        return $value;
    }
}
