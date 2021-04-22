<?
    $array = (array) json_decode($DATA['_mod']['vars'], true);
    $keys = array_keys($array);
    $values = array_values($array);

    $loadPage = new Page();
    $links = new Page();
?>

  <?
    $txtkey = implode("|",$keys);
    $pages = $loadPage->get(array("id"=>$txtkey,"sql"=>"ORDER BY prior"))->all();

    foreach($pages as $page) {
      $sectionClass = 'section__'.$array[$page['id']]['name'];
      $class = "btn btn-large btn-{$array[$page['id']]['buttons']} button-cat";
      $textClass = "title-text text-" . $array[$page['id']]['name'] . " white"
  ?>

  <section class="<?=$sectionClass;?>">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-12">

          <div class="<?=$textClass?>">
            <div class="h2"><?=$page['url_name']?></div>
            <?=$page['url_desc']?>
          </div>

          <div class="block__buttons-wrapper">
            <div class="block__buttons">
                <a href="<?=$page['url']?>" class="<?=$class?>"><i class="las la-stream"></i> Каталог продукции</a>
                <a href="" class="<?=$class?>"><i class="las la-check-circle"></i> Подобрать товар</a>
            </div>

            <div class="block__links">
              <? 
                $items = $links->get(array("parent"=>$page['id'],"status"=>1))->all();
                foreach($items as $item) {?>
                  <a href=""><i class="las la-chevron-circle-right"></i> <?=$item['url_name']?></a>
                <?}?>
            </div>
          </div>

      </div>

      <div class="col-lg-6 col-md-12">
        <div class="block__text">
          <div class="h2">Сферы применения:</div>
          <button class="btn btn-primary slide-button">Сферы применения</button>
          <div itemscope itemtype="http://schema.org/ImageObject" class="block__images">
          <?
            $images = json_decode($page['multi_images']);
            foreach($images as $img) {
              $full = func::imageToWidth($img[0],800);  
              $preview = func::imageToWidth($img[0],350);  
              ?>
              <a href="<?=$full?>" class="block__images-item" title="<?=Func::templateCityVars($img[1]);?>">
                <img itemprop="contentUrl" src="<?=$preview?>" alt="<?=Func::templateCityVars($img[1]);?>" />
                <div itemprop="description" class="h3"><?=Func::templateCityVars($img[1]);?></div>
              </a>
            <?}?>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
<?}?>

