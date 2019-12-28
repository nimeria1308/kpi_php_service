<?php
require_once('libraries/myview.php');

use Simona\MyView;

$t = new MyView('header.phtml');
$t->title = "За нас";
$t->render();

$t = new MyView('about.phtml');
$t->render();

$t = new MyView('footer.phtml');
$t->render();
?>
