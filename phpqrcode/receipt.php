<?php 
	include( '../include/db.php' );
	include( '../include/utility.php' );
    include('qrlib.php'); 
     
    // outputs image directly into browser, as PNG stream 
    QRcode::png($site_url.'/qrcode.php?id='.$_GET[ "id" ]);