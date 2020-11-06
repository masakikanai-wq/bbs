<?php 

    // 定数の定義・定数は変数と見分けがつくように大文字で指定する習慣がある
    define( 'FILENAME', './message.txt');

    // タイムゾーンの指定
    date_default_timezone_set('Asia/Tokyo');

    // 変数の初期化
    $now_time = null;
    $data = null;
    $file_handle = null;
    $split_data = null;
    // $message = array();のような形で連想配列を定義できる
    $message = array();
    $message_array = array();
    $error_message = array();

    if(!empty($_POST['btn_submit'])){

        $view_name = $_POST['view_name'];
        $message = $_POST['message'];

        // ユーザーからの値をHTMLに出力するときにセキュリティ目的で使用する
        $view_name = htmlspecialchars($view_name, ENT_QUOTES);
        $message = htmlspecialchars($message, ENT_QUOTES);

        // タイトルのチェック
        if(empty($_POST['view_name'])){
            $error_message[] = 'タイトルは必須です。';
        }

        // タイトル文字数チェック
        if((mb_strlen($_POST['view_name'])) > 30){
            $error_message[] = 'タイトルは30文字以下にしてください。';
        }

        // コメントのチェック
        if(empty($_POST['message'])){
            $error_message[] = 'メッセージは必須です。';
        }

        // fopen関数を使用して指定したファイルを開く
        if($file_handle = fopen( FILENAME, "a")){

            if(empty($error_message)){

            $now_date = date("Y-m-d H:i:s");
            // 書き込み日時を取得

            $data = "'".$_POST['view_name']."', '".$_POST['message']."', '".$now_date."'\n";
            // 書き込むデータの作成

            fwrite($file_handle, $data);
            // データの書き込み

            fclose($file_handle);
            // fclose関数はファイルを閉じるための関数
            // fopenとセットで使用する、ファイルポインターリソースを渡して閉じるファイルを指定する
            }
        }
    }

    if($file_handle = fopen(FILENAME, 'r')){
        while($data = fgets($file_handle)){

            $split_data = preg_split('/\'/', $data);

            $message = array(
                'view_name' => $split_data[1],
                'message' => $split_data[3],
                'post_date' => $split_data[5]
            );
            array_unshift($message_array, $message);
        }
        // ファイルを閉じる
        fclose($file_handle);
    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BBS</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="script.js"></script>
</head>
<body>
    <section id="bbs-wrapper">
        <div class="container">
            <div class="bbs">
                <!-- エラーメッセージ表示 -->
                <?php if(!empty($error_message)):?>
                    <ul>
                        <?php foreach($error_message as $value): ?>
                            <li><?php echo $value; ?></li>
                        <?php endforeach ?>
                    </ul>
                <?php endif; ?>
                <!-- 投稿フォーム -->
                <form action="" method="post" onsubmit="return submitChk()">
                    <div>
                        <label for="view_name">タイトル</label>
                        <input id="view_name" type="text" name="view_name" value="<?php if(isset($view_name)){echo $view_name;} ?>">
                    </div>
                    <div>
                        <label for="message">一言コメント</label>
                        <textarea name="message" id="message" cols="30" rows="10"><?php if(isset($message)){echo $_POST['message'];} ?></textarea>
                    </div>
                    <input class="btn" type="submit" name="btn_submit" value="送信">
                </form>
            </div>
        </div>
    </section>
    <section id="message-wrapper">
        <div class="container">
            <?php if(!empty($message_array)): ?>
                <?php foreach($message_array as $value): ?>
                    <article>
                        <div class="info">
                            <h2><?php echo $value['view_name']; ?></h2>
                            <time><?php  echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                        </div>
                        <p><?php echo $value['message']; ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif ?>
        </div>
    </section>
</body>
</html>