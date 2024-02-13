<?php



function ddump() {
    ob_start(); // شروع بافر خروجی

    array_map(function($x) {
        Symfony\Component\VarDumper\VarDumper::dump($x);
    }, func_get_args());

    $output = ob_get_clean(); 

    // در اینجا، شما می‌توانید هدرهای خود را ارسال کنید
    // برای مثال: header('Content-Type: text/html');

    echo $output; // چاپ خروجی
    die();
}
