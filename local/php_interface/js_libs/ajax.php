<?php

$class = Array(
    'TEXT' => 'Пока, ',
    'CLASS1' => 'my_class1',
    'CLASS2' => 'my_class2',
);

if(isset($_POST['name']) && $_POST['name'] !='') {
    $ar["NAME"] = $_POST['name'];
    $result = array_merge($class, $ar);
    echo json_encode($result);
}
else {
    echo json_encode($class);
}
