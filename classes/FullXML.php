<?php

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

	/**
	 * @return array containing photo path and id
	 */
	private function getFeaturedImage($imageurl){
		$photos = simplexml_load_file($imageurl);
		$path = $photos->photo->instances->instance->url;
		$id = $photos->photo->id;
		return array(
			"path"=>(string)$path[0],
			"id"=>(string)$id[0]
		);
	}

	/**
	 *
	 * @param array $photoArray
	 * @param string $location
	 * @return void
	 */
	private function saveImage($photoArray,$location){				
		file_put_contents(dirname(__DIR__).'\\..\\'.$location.'\\'.$photoArray['id'].".jpg", file_get_contents($photoArray["path"]));
	}

	/**
	 *
	 * @param object $simple
	 * @param string $location
	 * @return void
	 */
	private function saveText($simple, $location) {

        $text = preg_split('/<br \/>/',$simple->text);
        ob_start();
        echo $simple->headline;
		echo "\r\n";
		echo "\r\n";
		echo $simple->createdDate;
		echo "\r\n";
        echo "\r\n";
		echo $this->getCategory($simple);
        echo "\r\n";
        echo "\r\n";
		foreach($text as $line){
            $alter = preg_split('/<strong>/',$line);
            foreach($alter as $newline){
                echo preg_replace('/&nbsp;/',' ',strip_tags($newline));
                echo "\r\n";
                echo "\r\n";
            }
        }
		$output = ob_get_contents();
		ob_end_clean();
		$file = dirname(__DIR__).'\\..\\'.$location.'\\'.$simple->id.'.rtf';
		$fp = fopen($file, 'w');
		fwrite($fp, html_entity_decode($output, ENT_QUOTES | ENT_XML1, 'UTF-8'));
		fclose($fp);
	}
	/**
	 * @param object $xml
	 * @return string
	 */
	private function getCategory($xml) : string{
		$cats = simplexml_load_file($xml->categories['href']);
		$category = $cats->category->name;
		return $category;
	}

	/**
	 *
	 * @param string $a
	 * @param string $b
	 * @return void
	 */
	private function parseFeed($a,$b){
        $master = array();
		foreach($b as $i) {
                $temp = array();
                $tempUrl = $a.'/'.$i;			 
                $tempOut = simplexml_load_file($tempUrl);
                $photos = $this->getFeaturedImage($tempOut->photos["href"]);
                $location = $tempOut->id;
                echo $location;
                if(!file_exists($location)) { mkdir('..\\'.$location); }
                $this->saveText($tempOut,$location);
                $this->saveImage($photos,$location);
		}
	}
}