<?php
//Lib for parsing
require_once "./simple_html_dom.php";

//URL for parsing
$urlSuite='https://freelansim.ru';
/*
*@param $url,$urlSuite
*/
function getArticlesLinksFromCatalog($url,$urlSuite){
  if($url == $urlSuite){
    echo $url."<br>";
    //Get page
    //с помощью ф-ии из библиотеки мы получили объект класса simple_html_dom
    $html = file_get_html($url);
    //find метод который по заданному селектору ищет элементы к примеру по классу(все ссылки с таким классом)
    foreach ($html->find('div.task__title a') as $link_to_article) {//"div a" находит вложенный a
      echo $urlSuite.$link_to_article->href."<br>"; // выведем href атрибут куда ведет эта ссылка и добавим перевод строки
    }
      //Рекурсия
      if($next_link = $html->find('div.pagination a[rel=next]',0)){
      getArticlesLinksFromCatalog($next_link->href,$urlSuite);
      }
  }else{
    //добавляем к ссылке сайта ссылку переход на дальнейшую страницу, если она не равна предыдушей странице
    $url=$urlSuite.$url;
    echo "<br>".$url."<br><br>";
    //Get page
    //с помощью ф-ии из библиотеки мы получили объект класса simple_html_dom
    $html = file_get_html($url);

    //find метод который по заданному селектору ищет элементы к примеру по классу(все ссылки с таким классом)
    foreach ($html->find('div.task__title a') as $link_to_article) {//"div a" находит вложенный a
      echo $urlSuite.$link_to_article->href."<br>"; // выведем href атрибут куда ведет эта ссылка и добавим перевод строки
    }
    //Рекурсия
    //Если есть хоть один элемент в блоке тега <div> с классом pagination в теге <a> с атрибутом [rel] равному next то делаем рекурсию
    if($next_link = $html->find('div.pagination a[rel=next]',0)){
      getArticlesLinksFromCatalog($next_link->href,$urlSuite);
    }
  }
}

getArticlesLinksFromCatalog($urlSuite,$urlSuite);
// innertext свойство библиотеки возвращает html код
//print_r($html->innertext);
?>
