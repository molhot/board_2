<?php
// 以下データーベースへの接続
$link = mysql_connect('localhost', 'root', '');
if(!$link){
    die('データベースに接続できない:' . mysql_error());
    //die関数で直前の動作を終わらす、この場合ならmysql_connect
}

mysql_select_db('online_bbs',$link);

$errors = array();

//POSTなら保存処理実行 必要っぽいで
//GETとかならそもそもsqlを実行しない
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = null;
    if(!isset($_POST['name']) || !strlen($_POST['name'])){
        $errors['name'] = '名前を入力してください';
    }
    else if(strlen($_POST['name']) > 40){
        $errors['name'] = '名前は40文字以内にしてください';
    }
    else{
        $name = $_POST['name'];
    }

    $comment = null;
    if(!isset($_POST['comment']) || !strlen($_POST['comment'])){
        $errors['comment'] = '入力しろ';
    }
    else if(strlen($_POST['comment']) > 200){
        $errors['comment'] = '200文字以内やで';
    }
    else{
        $comment = $_POST['comment'];
    }

    if(count($errors) === 0){
        //信用できないデータに対してmysql_real_escapeを行っている、単純に文字列の代入になっている dateにはいらんのね
        $sql = "INSERT INTO `post`(`name`,`comment`,`created_at`)VALUES(
            '".mysql_real_escape_string($name)."',
            '".mysql_real_escape_string($comment)."',
            '".date('Y-m-d H:i:s')."')";

            mysql_query($sql, $link);
        //この辺分からん
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            ひとこと掲示板
        </title>
    </head>
    <body>
        <h1>
            一言掲示板
        </h1>

        <?php
        //以下挙動確認
        //実行問題なくできている　値がない時はfalse認定っぽい　優秀ね
        //詳しくは知らない
        $i = 0;
        $array_1 = array();
        $array_1 = array(100, 200, 300, null);

        while($array_1[$i] != null):
            echo $array_1[$i];
            echo "<br>";
            $i = $i + 1;
        endwhile;
        ?>

        <form action = "bbs.php" method = "post">
            名前:<input type = "text" name = "name"><br>
            一言:<input type = "text" name = "comment" size = "60"><br>
            <input type = "submit" name = "submit" value = "送信">
        </form>

        <?php

        $sql = "SELECT * FROM `post` ORDER BY `created_at` DESC";
        $result = mysql_query($sql, $link);

        ?>
        <?php
        if ($result !== false && mysql_num_rows($result)):
        ?>

        <ul>
            <?php
            while($post = mysql_fetch_assoc($result)):
            //値がなくなる(false)までは繰り返す、これはCであったかも
            ?>
            <li>
                <?php 
                echo htmlspecialchars($post['name'], ENT_QUOTES, 'utf-8');
                ?>:
                <?php
                echo htmlspecialchars($post['comment'], ENT_QUOTES, 'utf-8');
                ?>
            </li>
            <?php
            endwhile;
            ?>
        </ul>
        <?php
        endif;
        ?>

        <?php
        mysql_free_result($result);
        mysql_close($link);
        ?>
    </body>
</html>