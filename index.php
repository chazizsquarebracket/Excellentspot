<?php
require($_SERVER['DOCUMENT_ROOT'] . '/lib/common.php');

// currently selects all uploaded videos, should turn it into all featured only
$postData = query("SELECT $userfields p.post_id, p.title, p.description, p.author,  p.time FROM posts p JOIN users u ON p.author = u.id ORDER BY p.id DESC");

$twig = twigloader();

echo $twig->render('index.twig', [
	'posts' => $postData
]);
