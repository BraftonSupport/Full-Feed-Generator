<?php

class Feed {
	public $idArray = array();
	public $url;
	public $xml;
	public $length;

	public function __construct($ref){
		$this->url = $ref;
		$this->xml = $this->getXml($ref);
		$this->idArray = $this->setIdArray($this->xml);
		$this->length = sizeof($this->idArray);
	}

	private function getXml($uri){
		$temp = simplexml_load_file($uri);
		return $temp;
	}

	private function updateXml($xml){
		$i = 0;
		foreach($xml->newsListItem as $key) {
			$id = $key->id;
			$content = $this->parseFeed($id);
			$xml->newsListItem[$i]->addChild('content',htmlspecialchars($content->text));
			$i++;
		}
		//convert simple xml object to straight xml here
		$doc = new DOMDocument();
		$doc->formatOutput = TRUE;
		$doc->loadXML($xml->asXML());
		$fxml = $doc->saveXML();
		echo $fxml;
	}

	private function parseFeed($bid){
		$addContent = simplexml_load_file($_POST['feed-url'].'/news/'.$bid);
		return $addContent;
	}

	private function setIdArray($x){
		$tmp = array();
		foreach($x->newsListItem as $key) {
			
			array_push($tmp, $key->id);
		}
		return $tmp;
	}
	private function retrieveFullArticle(){

	}
}

class FullXML {
	public $url;
	public $idArray;
	public $output;
	public $storage;
	public function __construct($u, $ids){
		$this->url = $u;
		$this->idArray = $ids;
		$this->parseFeed($this->url,$this->idArray);
	}
	private function parseFeed($a,$b){
		$master = new DOMDocument();
		foreach($b as $i) {
			$tempUrl = $a.'/'.$i;			 
			$tempOut = simplexml_load_file($tempUrl);
			$elem = $master->createElement('block',$tempOut->asXML());
			$master->appendChild($elem);
			//$master->addChild('newsItem',$tempOut);
		}
		$master->formatOutput = TRUE;
		$master = $master->saveXML();
		echo '<pre>';
		var_dump($master);
		die();
		$fp = fopen('feed2.xml', 'w');
		fwrite($fp, html_entity_decode($master, ENT_QUOTES | ENT_XML1, 'UTF-8'));
		fclose($fp);
	}
}

if(isset($_POST['feed-url'])&&isset($_POST['feed-file'])) :

	$feed = new Feed('./feeds/'.strip_tags($_POST['feed-file']));
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