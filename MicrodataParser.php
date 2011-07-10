<?
/**
 * Microdata library to read microdata from websites and provide it back
 * as structured data. Currently supports:
 *
 * - PHP Array
 * - JSON
 * - XML
 *
 * @package Microdata
 * @author Alex Holt <alex@outsideinmedia.co.uk>
 * @copyright 2011
 */
/**
 * Class: MicrodataParser
 * Implements a simple parser to load the html for a URL and read out a 
 * data structure from the microdata items defined on the page.
 *
 * @package Microdata
 */
class MicrodataParser {
	private $dataArray=false;
	
	public function __construct($url) {
		// Require the simple_html_dom.php library, this handles reading and
		// accessing hte dom of the destination document
		require_once('simplehtmldom/simple_html_dom.php');


		$html = file_get_contents($url);			// get hte HTML from the url:
		$html = preg_replace('|\n|',' ',$html);		// remove newlines
		$html = str_get_html($html);				// get a dom from the html.
		
		// parse out the microdata into our internal private dataArray:
		$this->dataArray = $this->getItems($html->find('body',0));
	}
	
	/**
	 * getArray()
	 * Returns the parsed document's microdata tree as a PHP array.
	 *
	 * @return array $microdataArray
	 */ 
	public function getArray() {
		return($this->dataArray);
	}
	/**
	 * getJson()
	 * Returns the parsed document's microdata tree as a JSON string.
	 *
	 * @return string $microdataJson
	 */ 
	public function getJson() {
		return(json_encode($this->dataArray));
	}
	/**
	 * prettyType()
	 * Returns Microdata type as a simple string - nicer for matching.
	 *
	 * @return string $microdataType
	 */ 
	private function prettyType($type) {
		return strtolower(preg_replace('|.*/|','',$type));
	}
	/**
	 * getValue()
	 * Returns an element's value.
	 *
	 * @return string value
	 */ 
	private function getValue($elem) {	
		switch($elem->tag) {
			case 'img':
				return($elem->src);
				break;
			case 'meta':
				return($elem->content);
				break;
			case 'a':
				return($elem->href);
				break;
		}
		return(strip_tags($elem->innertext));
	}
	/**
	 * getItems()
	 * Recurse through the tree and find microdata.. the guts.
	 *
	 * @return array $microdataItems
	 */ 
	private function getItems($elem) {
		$notitem = false;
		$item = array();
		if($elem->itemscope) {
			$item['_type'] = $elem->itemtype;
			$item['_class'] =  $this->prettyType($elem->itemtype);			
		} else {	
			$notitem = true;	
		}
		foreach($elem->children() as $child) {
			if($child->itemprop && !$child->itemscope) {
				$item[$child->itemprop] = $this->getValue($child);
			} else {
				$car = $this->getItems($child);
				if(count($car) > 0)
					$item = array_merge_recursive($item,$car);
			}
		}
		if(count($item) > 1 && !$notitem)
			$item = array($elem->itemprop=>$item);
		
		return($item);		
	}	
}
?>
