<?php 

    // タイムゾーン設定
    date_default_timezone_set('Asia/Tokyo');
    
    // コメントのユニークidを取得
    $unique_id = uniqid();

    $id = $_GET['id'];
    $FILE = 'message.txt'; //保存ファイル名
    $file = json_decode(file_get_contents($FILE));
    $page_data = [];

    //記事へのコメント関連
    $COMMENT_DATA = 'comment.txt';
    $comment_data = json_decode(file_get_contents($COMMENT_DATA));
    $comment_board = [];    // 全体配列
    $comment = '';          //コメント初期化
    $DATA = [];             // 追加するデータ
    $COMMENT_BOARD = [];    // 表示する配列

    // エラーメッセージ
    // 未使用なので削除予定
    $error_message = [];

    // ネストした配列のlist()による展開（配列の配列の反復処理）
    // 公式のPHPドキュメントを参照
    foreach ($file as $index => list($key, $comment_id)){
        if ($key == $id){
            $page_data = $file[$index];
        }
    }

    // 記事ごとのコメントを取り出す処理
    foreach ((array)$comment_data as $index => list($key, $comment_id)){
        $comment_board[] = $comment_data[$index];
        if ($comment_id == $id){
            $COMMENT_BOARD[] = $comment_data[$index];
        }
    }

    // $COMMENT_DATAというファイルが存在しているときにファイルを読み込む
    // $COMMENT_DATAから$comment_boardに全体の配列を入れている
    if (file_exists($COMMENT_DATA)){
        $comment_board = json_decode(file_get_contents($COMMENT_DATA));
    }

    // コメント送信ボタンが押されてからの処理
    if (!empty($_POST['btn_submit'])){

        if(!empty($_POST['comment'])){

            // 送信されたテキストを代入する
            $comment = $_POST['comment'];

            // 新規コメントデータを全体配列の挿入
            // $idを2番目に配置するのがポイント？
            // $unique_id → コメントごとのid
            // $id → 記事ごとのid
            $DATA = [$unique_id, $id, $comment];
            $comment_board[] = $DATA;

            // この$idを取得する記述は必要ないかも
            $id = $_GET['id'];

            // 全体配列をファイルに保存する
            file_put_contents($COMMENT_DATA, json_encode($comment_board, JSON_UNESCAPED_UNICODE));

            // 現在表示している記事詳細ページへリダイレクト
            // リダイレクトしないとリアルタイムでコメントが反映されなかった
            header("Location: article.php?id=$id");
            exit;
        }
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
    <!-- 記事のタイトルと詳細表示部分 -->
    <section id="bbs-wrapper">
        <div class="container">
            <div class="bbs">
                <div>
                    <label for="view_name">タイトル</label>
                    <p><?php if (isset($page_data)){echo $page_data[1];} ?></p>
                </div>
                <div>
                    <label for="message">一言コメント</label>
                    <p><?php if (isset($page_data)){echo $page_data[2];} ?></p>
                </div>
                <p class="home"><a href="/php_bbs">一覧に戻る</a></p>
            </div>
        </div>
    </section>
    <!-- コメント投稿フォーム -->
    <section id="comment-submit-wrapper">
        <div class="container">
            <div class="comment-submit">
                <form action="" method="post">
                    <div>
                        <label for="comment">コメント</label>
                        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
                    </div>
                    <input class="btn" type="submit" name="btn_submit" value="送信">
                </form>
            </div>
        </div>
    </section>
    <!-- コメント表示部分 -->
    <section id="comment-display-wrapper">
        <div class="container">
            <div class="comment-display">
                <?php foreach((array)$COMMENT_BOARD as $value): ?>
                    <article>
                        <div>
                            <p><?php echo $value[2]; ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</body>
</html>