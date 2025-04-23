<?php

include "db.php";

$sql = file_get_contents("sql.php");

Database::GetInstance()->PDO->exec($sql);