<?php
//DB Credentials
define('DB_HOST','localhost');
define('DB_USER','mysql');
define('DB_PASSWORD','mysql');
define('DB_NAME','parser');

//Lib for parsing
require_once "/simple_html_dom.php";
require_once "/db.class.php";

//URL for parsing
$urlSuite='https://freelansim.ru';
//Connection to DB
$db= new DB (DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

//Get param from CLI
if(isset($argv[1])){
  $action = $argv[1];
  echo $action;
  exit;
}else{
  echo 'No action';
  exit;
}

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
*@param $url
*/
function getArticlesLinksFromCatalog($url){
  global $db;
  global $urlSuite;
  if($url == $urlSuite){

    echo $url."<br>";
    //Get page
    $html = file_get_html($url);
    foreach ($html->find('div.task__title a') as $link_to_article) {
      echo $urlSuite.$link_to_article->href."<br>";
      $article_url = $db->escape($urlSuite.$link_to_article->href);
      $sql= "INSERT ignore INTO Task SET     url ='{$article_url}'";
      $db->query($sql);
      getTask($urlSuite.$link_to_article->href);
    }
      if($next_link = $html->find('div.pagination a[rel=next]',0)){
        getArticlesLinksFromCatalog($next_link->href);
      }

  }else{
    $url=$urlSuite.$url;
    echo "<br>".$url."<br><br>";
    //Get page
    $html = file_get_html($url);
    foreach ($html->find('div.task__title a') as $link_to_article) {
      echo $urlSuite.$link_to_article->href."<br>";
    }
    if($next_link = $html->find('div.pagination a[rel=next]',0)){
      getArticlesLinksFromCatalog($next_link->href);
    }
  }
}

getArticlesLinksFromCatalog($urlSuite);
?>
