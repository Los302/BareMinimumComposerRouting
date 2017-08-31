<?php
include 'includes/initialize.php';

use \Includes\Model;

//$Count = Student::Check4DuplicateEmail('look@me.com', 55);
$Class = 'DatabaseObject';
$Function = 'EncryptThis';
$PWs = [
    'Tamera' => Model::$Function('test1234', KEY2),
    'Los' => Model::EncryptThis('test1234', KEY2),
];
$foo = '6';
echo $foo > 5 ? 'greater than' : 'lte';
?>
<pre><?=print_r(compact('Class', 'Function'))?></pre>
<pre><?=print_r($PWs)?></pre>
<p><?=$Count?></p>