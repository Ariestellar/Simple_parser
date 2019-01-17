<?php
//Rows per block
//define('PER_BLOCK','10');

//DB Credentials
define('DB_HOST','localhost');
define('DB_USER','mysql');
define('DB_PASSWORD','mysql');
define('DB_NAME','parser');

//Lib for parsing
require_once "simple_html_dom.php";
require_once "db.class.php";

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
*@param $url
*/
function getArticlesLinksFromCatalog($url){
  global $db;
  global $urlSuite;
  if($url == $urlSuite){
    echo $url;
    //Get page to BD
    $html = file_get_html($url);
    foreach ($html->find('div.task__title a') as $link_to_article) {
      echo $urlSuite.$link_to_article->href;
      $article_url = $db->escape($urlSuite.$link_to_article->href);
      $sql= "INSERT ignore INTO Task SET url ='{$article_url}'";
      $db->query($sql);
    }
      if($next_link = $html->find('div.pagination a[rel=next]',0)){
        getArticlesLinksFromCatalog($next_link->href);
      }

  }else{
    $url=$urlSuite.$url;
    echo $url;
    //Get page to BD
    $html = file_get_html($url);
    foreach ($html->find('div.task__title a') as $link_to_article) {
      echo $urlSuite.$link_to_article->href;
      $article_url = $db->escape($urlSuite.$link_to_article->href);
      $sql= "INSERT ignore INTO Task SET url ='{$article_url}'";
      $db->query($sql);
    }
    if($next_link = $html->find('div.pagination a[rel=next]',0)){
      getArticlesLinksFromCatalog($next_link->href);
    }else{
      echo "All done";
      exit;
    }
  }
}

//Get param from CLI
if(isset($argv[1])){
  $action = $argv[1];
  echo $action;
}else{
  echo 'No action';
  exit;
}

//Just get links to task
if($action == 'catalog'){
  getArticlesLinksFromCatalog($urlSuite);
}elseif($action == 'task'){
  while(true){
    $tmp_uniq= md5(uniqid().time());
    $db->query("UPDATE task SET tmp_uniq = '{$tmp_uniq}' WHERE tmp_uniq is null limit 10");
    $task = $db->query("SELECT url FROM task WHERE tmp_uniq = '{$tmp_uniq}' and headline is null ");
    
    if($task->num_rows==0){
      echo "All done";
      exit;
    }else{
      while ($x=$task->fetch_assoc()){
           echo $x['url'];
           getTask($x['url']);
      }
    }
  }
}
?>
