<?php include(dirname(__FILE__) . '/header.php'); ?>
<main class="main">
    <?php
    session_start();
    session_regenerate_id(TRUE);
    // xss対策のhtmlspecialchars関数
    function h($var)
    {
        if (is_array($var)) {
            return array_map('h', $var);
        } else {
            return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        }
    }

    $_SESSION['count'] = 0;

    $name = $_SESSION['name'] ?? NULL;
    $kana = $_SESSION['kana'] ?? NULL;
    $tel = $_SESSION['tel'] ?? NULL;
    $email = $_SESSION['email'] ?? NULL;
    $comment = $_SESSION['comment'] ?? NULL;

    $error = $_SESSION['error'] ?? NULL;

    $error_name = $error['name'] ?? NULL;
    $error_kana = $error['kana'] ?? NULL;
    $error_tel = $error['tel'] ?? NULL;
    $error_email = $error['email'] ?? NULL;
    $error_comment = $error['comment'] ?? NULL;

    ?>
    <div class="inner-small">
        <h1>お問い合わせ</h1>
        <form action="confirm.php" method="post" class="form" novalidate>
            <p class="form-info">下記の項目を入力し、送信ボタンを押してください。</p>
            <label for="name" class="label__name"><span class="label__acc">必須</span>氏名</label>
            <span class="error-php"><?php echo h($error_name); ?></span>
            <input id="name" type="text" name="name" value="<?php echo h($name); ?>" />

            <label for="kana" class="label__kana"><span class="label__acc">必須</span>フリガナ</label>
            <span class="error-php"><?php echo h($error_kana); ?></span>
            <input id="kana" type="text" name="kana" value="<?php echo h($kana); ?>" />

            <label for="tel" class="label__tel">電話番号</label>
            <span class="error-php"><?php echo h($error_tel); ?></span>
            <input id="tel" type="tel" name="tel" value="<?php echo h($tel); ?>" />

            <label for="email" class="label__email"><span class="label__acc">必須</span>メールアドレス</label>
            <span class="error-php"><?php echo h($error_email); ?></span>
            <input id="email" type="email" name="email" value="<?php echo h($email); ?>" />

            <label for="comment" class="label__comment"><span class="label__acc">必須</span>お問い合わせ内容</label>
            <span class="error-php"><?php echo h($error_comment); ?></span>
            <textarea name="comment" id="comment"><?php echo h($comment) ?></textarea>

            <button type="submit" name="confirm" class="btn btn__submit" id="submit1" onclick="return run();">
                送信
            </button>
        </form>
    </div>
    <div class="table__wrapper">
        <table class="table" border="1" id="table">
            <tr class="table__key">
                <th>id</th>
                <th>name</th>
                <th>kana</th>
                <th>tel</th>
                <th>email</th>
                <th>body</th>
                <th>created_at</th>
                <th></th>
                <th></th>
            </tr>
            <?php
            // DB接続
            $dsn = 'mysql:dbname=cafe;host=127.0.0.1';
            $user = 'root';
            $password = 'rootlocalhost';

            try {
                $pdo = new PDO($dsn, $user, $password);
                $pdo->beginTransaction();
                $sql = "SELECT * FROM contacts";
                $stmt = $pdo->query($sql);

                // テーブル出力
                $i = 0;
                foreach ($stmt as $row => $num) {
                    echo '<form action="edit.php" method="post"><tr class="table__datas"><td class="data__center data__id data"><input name="id" type="number" value="' . $num["id"] . '" readonly></input></td>';
                    echo '<td class="data__name data"><div class="strlong">' . $num["name"] . '</div></td>';
                    echo '<td class="data__kana data"><div class="strlong">' . $num["kana"] . '</div></td>';
                    echo '<td class="data__tel data"><div class="strlong">' . $num["tel"] . '</div></td>';
                    echo '<td class="data__email data"><div class="strlong">' . $num["email"] . '</div></td>';
                    echo '<td class="data__body data"><div class="strlong">' . nl2br($num["body"]) . '</div></td>';
                    echo '<td class="data__time data">' . $num["created_at"] . '</td>';
                    echo '<td class="btn--edit"><button name="post_flg" type="submit" class="u--blue">編集</button></td></form>';
                    echo '<td class="btn--delete"><button onClick="jumpDelete(' . $num["id"] . ')" class="delete_btn u--blue" id="delete_btn">削除</button></td></tr>';
                    $i++;
                }
                $pdo->commit();
            } catch (PDOException $e) {
                echo "接続失敗: " . $e->getMessage() . "\n";
                $pdo->rollBack();
            } finally {
                // DB接続を閉じる
                $pdo = null;
            }
            ?>
        </table>
    </div>
</main>
<script>
    function jumpDelete(id) {
        let result = window.confirm(`${id}番のデータを削除します。`);
        if (result) {
            window.location.href = 'delete.php?id=' + id;
        }
    }
</script>

<?php include(dirname(__FILE__) . '/footer.php'); ?>