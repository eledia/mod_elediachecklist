<?php

export_pdf::$commentsEA=$_POST["commentsEA"];



//global $COMMENT;
//$COMMENT = $_POST["commentsEA"];
//$GLOBALS["commentsEA"] = $_POST["commentsEA"];
//error_log("cabeceras impresos222... " . $GLOBALS["commentsEA"], 0);
error_log("cabeceras impresos333... " . export_pdf::$commentsEA, 0);

?>