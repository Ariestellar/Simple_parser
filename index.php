<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="utf-8">
    <title>Simple parser</title>
    <?php
    require_once "/db.class.php";
    define('DB_HOST','localhost');
    define('DB_USER','mysql');
    define('DB_PASSWORD','mysql');
    define('DB_NAME','parser');
    $db =new DB(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
     ?>
  </head>
  <body>
    <input type="submit" value="Launch">
    <?php

    $sql="SELECT * FROM task ";
    $result=$db->query($sql);
    var_dump($result->fetch_assoc());
     ?>
    <table>
      <tr>
        <td>id</td>
        <td>Headline</td>
        <td>Description</td>
      </tr>
      <tr>
        <?php foreach($result as $value){?>
        <td><?php echo $value['id']; ?></td>
        <td><?php echo $value['url']; ?></td>
        <td><?php echo $value['id'];} ?></td>
      </tr>
    </table>
  </body>
</html>
