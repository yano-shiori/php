<?php include(dirname(__FILE__) . '/header.php'); ?>
<main class="main delete inner-small">
    <?php
    session_start();
    function h($var)
    {
        if (is_array($var)) {
            return array_map('h', $var);
        } else {
            return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
        }
    }
    // 不正遷移処理
    // $_SESSION['count']の初期値は0
    $_SESSION["count"] = $_SESSION["count"] + 1;
    if ($_SESSION['count'] !== 1) {
        header("location: contact.php");
        exit();
    }
    $error = array();
    if (count($error) > 1) {
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $dirname = $dirname == DIRECTORY_SEPARATOR ? '' : $dirname;
        //サーバー変数 $_SERVER['HTTPS'] が取得出来ない環境用
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and $_SERVER['HTTP_X_FORWARDED_PROTO'] === "https") {
            $_SERVER['HTTPS'] = 'on';
        }
        //入力画面（contact.php）の URL
        $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . $dirname . '/contact.php';
        header('HTTP/1.1 303 See Other');
        header('location: ' . $url);
        exit;
    }
    $urlTop = $_SERVER['HTTP_REFERER'];

    // DB接続
    $dsn = 'mysql:dbname=cafe;host=127.0.0.1';
    $user = 'root';
    $password = 'rootlocalhost';
    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->beginTransaction();
        $sql = "DELETE FROM contacts WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $id = $_GET["id"];
        $params = array(':id' => $id);
        $stmt->execute($params);
        echo "削除しました。<br> 5秒後に元のページにジャンプします。<br>";
        echo '<a href=' .$urlTop. ' class="btn btn__return textlink">戻る</a>';
        $pdo->commit();
    } catch (PDOException $e) {
        echo "接続失敗: " . $e->getMessage() . "\n";
        $pdo->rollBack();
    } finally {
        // DB接続を閉じる
        $pdo = null;
    }
    ?>
</main>
<script>
    setTimeout(function() {
        window.location.href = 'contact.php';
    }, 5 * 1000);
</script>
<?php include(dirname(__FILE__) . '/footer.php'); ?>