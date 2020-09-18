<!--?php
session_start();
session_destroy();
echo "<script>window.alert('try be back soon !');
			window.location='./index.php'</script>";
die();
?-->

<?php
session_start();
session_destroy();
echo "<script>window.alert('try be back soon !');
			window.location='/IUSocial-master/register.php'</script>";
die();
?>