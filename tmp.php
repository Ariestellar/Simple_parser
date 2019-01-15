<?php
//Lib for parsing
require_once "./simple_html_dom.php";

//URL for parsing
$url='https://freelansim.ru/tasks?q=php';

//Get page
//с помощью ф-ии из библиотеки мы получили объект класса simple_html_dom
$html = file_get_html($url);



//find метод который по заданному селектору ищет элементы к примеру по классу(все ссылки с таким классом)
foreach ($html->find('div.task__title a') as $link_to_article) {//"div a" находит вложенный a
  echo $link_to_article->href."<br>"; // выведем href атрибут куда ведет эта ссылка и добавим перевод строки
}

// innertext свойство библиотеки возвращает html код
//print_r($html->innertext);
?>
