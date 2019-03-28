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