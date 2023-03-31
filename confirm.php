<?php include(dirname(__FILE__) . '/header.php'); ?>
<main class="main">
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

    $name = trim(filter_input(INPUT_POST, 'name'));
    $kana = trim(filter_input(INPUT_POST, 'kana'));
    $tel = trim(filter_input(INPUT_POST, 'tel'));
    $email = trim(filter_input(INPUT_POST, 'email'));
    $comment = trim(filter_input(INPUT_POST, 'comment'));

    $error = array();

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

    if ($comment == '') {
        $error['comment'] = 'お問い合わせ内容は必須項目です。';
    }

    $_SESSION['name'] = $name;
    $_SESSION['kana'] = $kana;
    $_SESSION['tel'] = $tel;
    $_SESSION['email'] = $email;
    $_SESSION['comment'] = $comment;
    $_SESSION['error'] = $error;

    if (count($error) > 0) {
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

    ?>
    <div class="inner-small">
        <h1>お問い合わせ</h1>
        <form action="complete.php" method="post" class="form">
            <p class="form-info">下記の項目を入力し、送信ボタンを押してください。<br>内容を訂正する場合は戻るを押してください。</p>
            <h2 class="label--confirm">氏名</h2>
            <p class="return_value">
                <?php echo h($name); ?>
            </p>
            <h2 class="label--confirm">フリガナ</h2>
            <p class="return_value">
                <?php echo h($kana); ?>
            </p>
            <h2 class="label--confirm">電話番号</h2>
            <p class="return_value">
                <?php echo h($tel); ?>
            </p>
            <h2 class="label--confirm">メールアドレス</h2>
            <p class="return_value">
                <?php echo h($email); ?>
            </p>
            <h2 class="label--confirm">お問い合わせ内容</h2>
            <p class="return_value">
                <?php echo h($comment); ?>
            </p>
            <div class="btn-area">
                <a href="<?php echo $urlTop ?>" class="btn btn__return textlink">戻る</a>
                <input type="hidden" name="ticket" value="<?php echo h($ticket); ?>">
                <input class="btn btn__submit" type="submit" value="送信">
            </div>
        </form>
    </div>
</main>
<?php include(dirname(__FILE__) . '/footer.php'); ?>