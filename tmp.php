<?php
//DB Credentials
define('DB_HOST','localhost');
define('DB_USER','mysql');
define('DB_PASSWORD','mysql');
define('DB_NAME','parser');

//Lib for parsing
require_once "./simple_html_dom.php";
require_once "./db.class.php";

//URL for parsing
$urlSuite='https://freelansim.ru';
//Connection to DB
$db= new DB (DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

/*
*@param $url
*@return $data
*/

function getTask($url){
  global $db;
  $task=file_get_html($url);
  $headline=$db->escape($task->find('h2',0)->innertext);
  $desTask=$db->escape($task->find('div.task__description',0)->innertext);
  $data = array('headline'=>$headline,
                'desTask'=>$desTask);


  $sql="UPDATE Task SET headline='{$headline}', descTasc='{$desTask}', date_parsed = NOW() WHERE url='{$url}'";
  $db->query($sql);
  return $data;
}
/*
*@param $url,$urlSuite
*/
function getArticlesLinksFromCatalog($url,$urlSuite){
  global $db;//для того что бы была доступна в ф-ии
  if($url == $urlSuite){
    echo $url."<br>";
    //Get page
    //с помощью ф-ии из библиотеки мы получили объект класса simple_html_dom
    $html = file_get_html($url);
    //find метод который по заданному селектору ищет элементы к примеру по классу(все ссылки с таким классом)
    foreach ($html->find('div.task__title a') as $link_to_article) {//"div a" находит вложенный a

      echo $urlSuite.$link_to_article->href."<br>";
      //print_r(getTask($urlSuite.$link_to_article->href));
      //Экранирование
      $article_url = $db->escape($urlSuite.$link_to_article->href);
      //Добавление ссылок в базу данных на задачу
      //в таблицу Task колонка url = 'ссылка'
      //     ВСТАВИТЬ В  Task ЗАДАТЬ url = ссылка (ignore - для предотвращения поппытки вставки существующий url, в индексе должнобыть unix)
      $sql= "INSERT ignore INTO Task SET     url ='{$article_url}'";
      $db->query($sql);
      getTask($urlSuite.$link_to_article->href);

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
      //echo getTask($urlSuite.$link_to_article->href);
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
