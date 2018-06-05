<?php
include 'includes/initialize.php';

use App\Models\Model;

$Function = 'EncryptThis';
$PWs = [
        'Los' => Model::EncryptThis('test1234', KEY2),
];
?>
<pre><?=print_r($PWs)?></pre>