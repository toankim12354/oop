<?php
require 'DB.php';
require 'Parser.php';
$db = new DB('localhost', 'toanlt', 'Toanlt123', 'Parser');
$url = 'https://vietnamnet.vn/my-tam-tiet-lo-chuyen-choi-game-tin-nhan-cho-nguoi-ay-2131430.html';
$vnexpress_parser = new VnexpressParser($url);
$VietnamnetParser = new VietnamnetParser($url);
$DantriParser = new DantriParser($url);
$data = $vnexpress_parser->parse();
$data = $VietnamnetParser->parse();
$data = $DantriParser->parse();
$title = $db->escape($data['title']);
$content = $db->escape($data['content']);
$data = $db->escape($data['date']);
$sql = "INSERT INTO wrapper (title, content, thoi_gian) VALUES ('$title', '$content', '$data')";
$db->query($sql);