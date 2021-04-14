<?php

require_once __DIR__ . '/functions.php';

$id = $_GET['id'];

$dbh = connectDb();

$sql = "SELECT * FROM plans WHERE id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$plan = $stmt->fetch(PDO::FETCH_ASSOC);

// データの編集
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力データ取得
    $title = $_POST['title'];
    $due_date = $_POST['due_date'];

    // エラーチェック用の配列
    $errors = [];

    // バリデーション
    if ($title == '') {
        $errors['title'] = '学習内容を入力してください';
    }
    if ($due_date == '') {
        $errors['due_date'] = '期限日を入力してください';
    }
    if (($title == $plan['title']) && ($due_date == $plan['due_date'])) {
        $errors['nochange'] = '変更内容がありません';
    }

    // エラーチェック
    if (!$errors) {

        $sql = "UPDATE plans SET title = :title, due_date = :due_date WHERE id = :id";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>編集画面</title>
    </head>
    <body>
        <h2>編集</h2>
        <div>
            <form action="" method="post">
                <div>
                    <label>学習内容</label>:
                    <input type="text" name="title" value="<?= h($plan['title']); ?>" maxlength="255">
                </div>
                <div>
                    <label>期限日</label>:
                    <input type="date" name="due_date" value="<?= h($plan['due_date']); ?>">
                    <input type="submit" value="編集">
                </div>
                <?php if ($errors): ?>
                    <ul style="color:red;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= h($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <a href="index.php">戻る</a>
            </form>
        </div>
    </body>
</html>
