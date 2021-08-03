<?php
require_once('./dbconnect.php');

ini_set('display_errors', 1);  /* MAMPの設定次第では記述が必要 1は表示、0は非表示*/
error_reporting(-1);  /* 0は表示させない、-1はすべて表示 */


/* データベースへ接続 */
try {
    $dbh = new PDO(DB_DNS, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e){
      echo $e->getMessage();
      exit;
}

/* データベース新規登録処理 */
if(!empty($_POST['inputName'])){
    try{
        $sql = "INSERT INTO sortable(name) VALUES(:ONAMAE)";
        $stmt = $dbh->prepare($sql);

        $stmt->bindParam(':ONAMAE', $_POST['inputName'], PDO::PARAM_STR);
        $stmt->execute();

        header('location: http://localhost:8888/PHP/13.php_js/');
        exit();
    }catch (PDOException $e) {
        echo 'データベースにアクセスできません！'.$e->getMessage();
    }
}

if(!empty($_POST['left'])){
    try{
      $sql  = 'UPDATE `sortable` SET `left_x` = :LEFT, `top_y` = :TOP WHERE `id` = :NUMBER';
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':LEFT'  , $_POST['left'], PDO::PARAM_INT);
      $stmt->bindParam(':TOP'   , $_POST['top'],  PDO::PARAM_INT);
      $stmt->bindParam(':NUMBER', $_POST['id'],   PDO::PARAM_INT);
      $stmt->execute();
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>sortable</title>
  <link href="css/style.css" rel="stylesheet">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
  <script>
$(function(){
  $('.drag').draggable({
    containment:'#drag-area',
    cursor:'move',
    opacity:0.6,
    scroll:true,
    zIndex:10,
    /* ドラッグ終了時に起動 */
    stop:function(event, ui){
      let myNum  = $(this).data('num');
      let myLeft = (ui.offset.left - $('#drag-area').offset().left);
      let myTop  = (ui.offset.top  - $('#drag-area').offset().top);
      $.ajax({
        type:'POST',
        url :'http://localhost:8888/',
        data: {
          id  :myNum,
          left:myLeft,
          top :myTop
        }
      }).done(function(){
         console.log('成功');
      }).fail(function(XMLHttpRequest, textStatus, errorThrown){
         console.log(XMLHttpRequest.status);
         console.log(textStatus);
         console.log(errorThrown);
      });
        console.log("左: " + myLeft);
        console.log("上: " + myTop);
    }
  });
});
</script>
</head>
<body>
<div id="wrapper">
    <div id="input_form">
        <form action="index.php" method="POST">
            <input type="text" name="inputName">
            <input type="submit" value="登録">
        </form>
    </div>

<div id="drag-area">
<?php
$sql = 'SELECT * FROM sortable';
$stmt = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);

foreach ($stmt as $result){
    echo '  <div class="drag" data-num="'.$result['id'].'" style="left:'.$result['left_x'].'px; top:'.$result['top_y'].'px;">'.PHP_EOL;
    echo '    <p><span class="name">'.$result['id'].' '.$result['name'].'</span></p>'.PHP_EOL;
    echo '  </div>'.PHP_EOL;
}
?>
</div>

</div>

</body>
</html>