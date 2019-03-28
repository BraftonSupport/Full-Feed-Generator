<?php

spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name .'.php';
});

if(isset($_POST['feed-url'])&&isset($_POST['feed-file'])) :

	$feed = new Feed(dirname(__DIR__) ."\\feeds\\".strip_tags($_POST['feed-file']));
	$outxml = new FullXML($_POST['feed-url'].'/news', $feed->idArray);
	echo '<pre>';
	echo $feed->length . ' Nodes in file<br />';
endif;

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>

  <meta charset="utf-8">
  <style>
  		input[type="text"] {
  			width: 350px;
  			margin: 1em 0;
  		}
  </style>
</head>
<body>
	<h1>Full Feed Generator</h1>
	<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
		<input type="text" name="feed-url" placeholder="feed url"/><br/>
		<input type="text" name="feed-file" placeholder="name of archive file xxxx.xml"/><br />
		<input type="submit" />
	</form>
</body>
</html>