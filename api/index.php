<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';//Framework yüklemiş gibi bir şey yaptık,gerekli materyalleri projemize eklemiş olduk

require '../src/config/db.php';

require '../src/routes/employees.php';




$app->run();