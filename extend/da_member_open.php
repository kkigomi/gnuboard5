<?php


sql_query("UPDATE g5_member SET mb_open = 1 WHERE mb_id NOT IN ('admin'');");
?>
