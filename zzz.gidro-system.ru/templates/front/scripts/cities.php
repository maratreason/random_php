<?
    $items = new DataBase('vars');
    $items = $items->sql("SELECT * FROM vars, cities WHERE vars.city=cities.i ORDER BY cities.size DESC")->all();

?>
<div class="modal-wrapper">
    <div class="container">
        <? $newArray = array_chunk($items, 4); ?>

        <div class="close">×</div>
        <div class="row">
            <? foreach($newArray as $array) {?>
            <div class="col-md-3">
                <? foreach($array as $item) {?>
                <a href="https://<?=$item['domen']?>"><?=$item['city']?></a>
                <?}?>
            </div>
            <?}?>
        </div>
    </div>

</div>
</div>