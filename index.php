<?php

require_once __DIR__ . '/functions.php';

// データベースに接続
$dbh = connectDb();

// レコードの取得(未達成の場合)
$sql = "SELECT * FROM plans WHERE status = 'notyet' ORDER BY due_date";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$notyet_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// レコードの取得(達成済みの場合)
$sql = "SELECT * FROM plans WHERE status = 'done' ORDER BY due_date DESC";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$done_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// データ追加
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

    // エラーチェック
    if (!$errors) {

        $sql = 'INSERT INTO plans (title, due_date) VALUES (:title, :due_date)';

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
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
        <link rel="stylesheet" href="style.css">
        <title>タスク管理 課題</title>
    </head>
    <body>
        <h1>学習管理アプリ</h1>
        <div>
            <form action="" method="post">
                <div>
                    <label>学習内容</label>:
                    <input type="text" name="title" maxlength="255">
                </div>
                <div>
                    <label>期限日</label>:
                    <input type="date" name="due_date">
                    <input type="submit" value="追加">
                </div>
                <ul  style="color:red;">
                    <?php if ($errors['title']): ?>
                        <li><?= h($errors['title']) ?></li>
                    <?php endif; ?>
                    <?php if ($errors['due_date']): ?>
                        <li><?= h($errors['due_date']) ?></li>
                    <?php endif; ?>
                </ul>
            </form>
        </div>

        <h2>未達成</h2>
        <ul>
            <?php foreach ($notyet_plans as $plan): ?>
                <?php if (date('Y-m-d') >= $plan['due_date']): ?>
                    <li class="expired">
                <?php else: ?>
                    <li>
                <?php endif; ?>
                    <a href="done.php?id=<?= h($plan['id']) ?>">[完了]</a>
                    <a href="edit.php?id=<?= h($plan['id']) ?>">[編集]</a>
                    <?= h($plan['title']); ?>･･･完了期限: <?= h(date('Y/m/d', strtotime($plan['due_date']))); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <hr>
        <h2>達成済み</h2>
        <ul>
            <?php foreach ($done_plans as $plan): ?>
            <li>
                <?= h($plan['title']); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </body>
</html>
