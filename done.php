<?php

require_once __DIR__ . '/functions.php';

$id = $_GET['id'];

$dbh = connectDb();

$sql = "UPDATE plans SET status = 'done' WHERE id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: index.php');
exit;
