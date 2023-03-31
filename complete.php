<?php
include(dirname(__FILE__) . '/header.php');
session_start();
// 不正遷移防止
$_SESSION["count"] = $_SESSION["count"] + 1;

function h($var)
{
    if (is_array($var)) {
        return array_map('h', $var);
    } else {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}

if ($_SESSION['count'] !== 2) {
    header("location: contact.php");
    exit();
}

// セッションの保存値を変数に格納
$name = h($_SESSION['name']);
$kana = h($_SESSION['kana']);
$tel = h($_SESSION['tel']);
$email = h($_SESSION['email']);
$body = h($_SESSION['comment']);

// DB接続
$dsn = 'mysql:dbname=cafe;host=127.0.0.1';
$user = 'root';
$password = 'rootlocalhost';
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->beginTransaction();

    // テーブルに登録するINSERT INTO文を変数に格納　VALUESはプレースフォルダーで空の値を入れとく
    $sql = "INSERT INTO contacts (name, kana, tel, email, body) VALUES (:name, :kana, :tel, :email, :body)";

    //値が空のままSQL文をセット
    $stmt = $pdo->prepare($sql);

    // 挿入する値を配列に格納
    $params = array(':name' => $name, ':kana' => $kana, ':tel' => $tel, ':email' => $email, ':body' => $body);

    //挿入する値が入った変数をexecuteにセットしてSQLを実行
    $stmt->execute($params);
    $pdo->commit();
} catch (PDOException $e) {
    exit('データベースに接続できませんでした。' . $e->getMessage());
    $pdo->rollBack();
} finally {
    // DB接続を閉じる
    $pdo = null;
}
?>
<main class="main">
    <div class="inner-small">
        <h1>お問い合わせ</h1>
        <div class="form">
            <p class="form-info">お問い合わせありがとうございます。<br>送信いただいた件につきましては、当社より折り返しご連絡を差し上げます。<br>なお、ご連絡までに、お時間を頂く場合もございますので予めご了承ください。</p>
            <a href="contact.php" class="totop">トップへ戻る</a>
            </form>
        </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>