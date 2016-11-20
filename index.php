<?php
require('funnyjunksdk.php');
require('pm.php');
use FunnyJunk\FunnyJunk;
echo 'thanks';
$fj = new FunnyJunk();
$fj->login('fjmodbot', '');
$messages = $fj->getInbox();
var_dump($messages);
echo '<a href="#">Delete [0]</a>';
//$messages[0]->delete();