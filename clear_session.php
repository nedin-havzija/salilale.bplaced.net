<?php
session_start();
unset($_SESSION['cart']);
unset($_SESSION['total_price']);
session_write_close();
