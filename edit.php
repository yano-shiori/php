<?php include(dirname(__FILE__) . '/header.php'); ?>
<main class="main edit">
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

    // 送信フラグ
    $send_flg = false;
    // エラーメッセージ
    $error = array();

    // 送信ボタンを押した後の処理
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $kana = isset($_POST['kana']) ? $_POST['kana'] : '';
    $tel = isset($_POST['tel']) ? $_POST['tel'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $body = isset($_POST['body']) ? $_POST['body'] : '';
    $time = isset($_POST['time']) ? $_POST['time'] : '';

    if (!isset($_POST["post_flg"])) {
        echo "<div class='error__container'>";
        echo "<h1>不正なアクセスです</h1>";
        echo '<a href="contact.php" class="btn btn__return textlink">戻る</a></div>';
        exit();
    }

    if ($_POST["post_flg"]) {
        if ($name == '') {
            $error['name'] = '氏名は必須項目です。10文字以内で入力してください。';
        } else if (mb_strlen($name) > 10) {
            $error['name'] = '氏名は10文字以内で入力してください。';
        }
        if ($kana == '') {
            $error['kana'] = 'フリガナは必須項目です。10文字以内で入力してください。';
        } else if (mb_strlen($name) > 10) {
            $error['kana'] = 'フリガナは10文字以内で入力してください。';
        }
        if ($tel !== '' && preg_match('/^[0-9]+$/', $tel) == 0) {
            $error['tel'] = '電話番号は0-9の数字のみで入力してください。';
        }
        if ($email == '') {
            $error['email'] = 'メールアドレスは必須項目です。';
        } else {
            $pattern = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/uiD';
            if (!preg_match($pattern, $email)) {
                $error['email'] = 'メールアドレスは正しい形式で入力してください。';
            }
        }
        if ($body == '') {
            $error['comment'] = 'お問い合わせ内容は必須項目です。';
        }
        if (count($error) == 0) {
            $send_flg = true;
        }
    }

    ?>
    <div class="table__wrapper">
        <?php
        if (!$send_flg) {
            // 取得したid
            $id = (int)$_POST['id'];

            // DB接続
            $dsn = 'mysql:dbname=cafe;host=127.0.0.1';
            $user = 'root';
            $password = 'rootlocalhost';

            try {
                // データ取得
                $pdo = new PDO($dsn, $user, $password);
                $pdo->beginTransaction();
                $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = :id');
                $stmt->bindValue(':id', $id);
                $stmt->execute();

                // データ出力
                foreach ($stmt as $row) {
                    $_SESSION["count"] = $_SESSION["count"] + 1;
                    // エラーメッセージがある場合
                    if (count($error) > 0) {
                        $_SESSION["count"] = 1;
                        echo '<p style="color:red;">';
                        foreach ($error as $val) {
                            echo "{$val}<br></p>";
                        }
                    }
                    echo '<form action="" method="post">';
                    echo '<table class="table" border="1" id="table">';
                    echo '<tr>';

                    echo '<th>id</th><td class="d_type">int(11)</td>';
                    echo '<td><input name="id" type="number" id="id" value="' . $row["id"] . '"  readonly></td></tr>';

                    echo '<tr><th>name</th><td class="d_type">varchar(50)</td>';
                    echo '<td><input type="text" name="name" id="name" value="' . $row["name"] . '"></td></tr>';

                    echo '<tr><th>kana</th><td class="d_type">varchar(50)</td>';
                    echo '<td><input type="text" name="kana" id="kana" value="' . $row["kana"] . '"></td></tr>';

                    echo '<tr><th>tel</th><td class="d_type">varchar(11)</td>';
                    echo '<td><input type="tel" name="tel" id="tel" value="' . $row["tel"] . '"></td></tr>';

                    echo '<tr><th>email</th><td class="d_type">varchar(100)</td>';
                    echo '<td><input type="email" name="email" id="email" value="' . $row["email"] . '"></td></tr>';

                    echo '<tr><th>body</th><td class="d_type">text</td>';
                    echo '<td><textarea name="body" id="comment">' . $row["body"] . '</textarea></td></tr>';

                    echo '<tr><th>created_at</th><td class="d_type">varchar(50)</td>';
                    echo '<td><input type="datetime-local" name="time" id="time" value="' . $row["created_at"] . '"></td></tr>';

                    echo '</table>';
                    echo '<div class="btn-area">
                        <a href="contact.php" class="btn btn__return textlink">戻る</a>
                        <input type="hidden" name="ticket" value="<?php echo h($ticket); ?>">
                        <input name="post_flg" onclick="return run();" class="btn btn__submit" type="submit" value="送信"></div>';
                    echo '</form>';
                    if ($_SESSION['count'] !== 1) {
                        header("location: contact.php");
                        exit();
                    }
                }
                $pdo->commit();
            } catch (PDOException $e) {
                $_SESSION["count"] = $_SESSION["count"] + 1;
                if ($_SESSION['count'] !== 1) {
                    header("location: contact.php");
                    exit();
                }
                echo "接続失敗: " . $e->getMessage() . "\n";
                $pdo->rollBack();
            } finally {
                // DB接続を閉じる
                $pdo = null;
            }
        } else {
            try {
                $dsn = 'mysql:dbname=cafe;host=127.0.0.1';
                $user = 'root';
                $password = 'rootlocalhost';
                $pdo = new PDO($dsn, $user, $password);
                $pdo->beginTransaction();

                $id = $_POST['id'];

                $name = isset($_POST['name']) ? $_POST['name'] : '';
                $kana = isset($_POST['kana']) ? $_POST['kana'] : '';
                $tel = isset($_POST['tel']) ? $_POST['tel'] : '';
                $email = isset($_POST['email']) ? $_POST['email'] : '';
                $body = isset($_POST['body']) ? $_POST['body'] : '';
                $time = isset($_POST['time']) ? $_POST['time'] : '';

                $sql = "UPDATE contacts SET id = :id, name = :name, kana = :kana, tel = :tel, email = :email, body = :body, created_at = :created_at WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([":id" => $id, ":name" => $name, ":kana" => $kana, ":tel" => $tel, ":email" => $email, ":body" => $body, ":created_at" => $time]);

                echo "<div class='edit__complete'>編集しました。<br>";
                echo '<a href="contact.php" class="btn btn__return textlink">戻る</a></div>';
            } catch (PDOException $e) {
                echo "接続失敗: " . $e->getMessage() . "\n";
                $pdo->rollBack();
            } finally {
                $pdo = null;
            }
        }
        ?>
    </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>