<?php

	include(dirname(__FILE__)."/init.php");
	header(sprintf("Location: %s/areadoclientewebshop.php?action=order_status", $GLOBALS["ShopPathSSL"]));