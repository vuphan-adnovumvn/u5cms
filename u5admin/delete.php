<?php

if (isset($_COOKIE['dora']) && $_COOKIE['dora']=='a') {
    header("Location: archiveunarchive.php?".$_SERVER['QUERY_STRING']);
} else {
    header("Location: deleteanitem.php?".$_SERVER['QUERY_STRING']);
}
