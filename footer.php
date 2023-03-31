<footer class="footer">
    <div class="inner">
        <div class="footer__info">
            <h2 class="logo"><a href="#" class="textlink2">cafe</a></h2>
            <dl>
                <dt>address:</dt>
                <dd>XXXXXXXXXXXXXXXXXXXXXXXXXXXX</dd>
                <dt>tel:</dt>
                <dd>XXX-XXXX-XXX</dd>
            </dl>
        </div>
        <nav class="footer__nav">
            <ul class="list">
                <li class="item"><a href="#" class="textlink2">news</a></li>
                <li class="item"><a href="#" class="textlink2">gallery</a></li>
                <li class="item"><a href="#" class="textlink2">profile</a></li>
                <li class="item"><a href="#" class="textlink2">contact</a></li>
            </ul>
        </nav>
    </div>
</footer>
<script>
    function run() {
        const name = document.getElementById('name').value;
        const kana = document.getElementById('kana').value;
        const tel = document.getElementById('tel').value;
        const email = document.getElementById('email').value;
        const comment = document.getElementById('comment').value;

        let error = 0;
        let alert_str = [];

        const regexNum = RegExp(/^[+,-]?([0-9]\d*|0)$/);
        const regexEmail = RegExp(/^[a-zA-Z0-9_.+-]+@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/);

        if (name == "") {
            alert_str.push("氏名は必須項目です。10文字以内で入力してください。");
            error++;
        }
        if (name.length > 10) {
            alert_str.push("氏名は10文字以内で入力してください。");
            error++;
        }
        if (kana == "") {
            alert_str.push("フリガナは必須項目です。10文字以内で入力してください。");
            error++;
        }
        if (kana.length > 10) {
            alert_str.push("フリガナは10文字以内で入力してください。");
            error++;
        }
        if (regexNum.test(tel) == false && tel) {
            alert_str.push("電話番号は0-9の数字のみで入力してください。");
            error++;
        }
        if (email == "") {
            alert_str.push("メールアドレスは必須項目です。");
            error++;
        }
        if (regexEmail.test(email) == false) {
            alert_str.push("メールアドレスは正しい形式で入力してください。");
            error++;
        }
        if (comment == "") {
            alert_str.push("お問い合わせ内容は必須項目です。");
            error++;
        }

        if (error == 0) {
            return true;
        } else {
            alert_str = alert_str.join('\n');
            alert(alert_str);
            return false;
        }
    }
</script>
</body>

</html>