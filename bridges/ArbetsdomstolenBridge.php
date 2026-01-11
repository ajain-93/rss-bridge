<?PHP
class ArbetsdomstolenBridge extends BridgeAbstract {

	const NAME		  = 'Arbetsdomstolen - Meddelade domar';
	const URI		  = 'http://www.arbetsdomstolen.se';
	const BASEURI	  = self::URI . '/pages/page.asp';
	const FETCH		  = self::BASEURI . '?lngID=4&lngLangID=1';
	const AUTHOR	  = "Arbetsdomstolen";
	const DESCRIPTION = 'Meddelade domar av Arbetsdomstolen för innevarande eller valt år';
	const MAINTAINER  = 'ajain-93';
	const CACHE_TIMEOUT = 300; // 5min
	const LIMIT		  = 10;
	const PARAMETERS = array(
		'' => array(
			'year' => array(
				'name' => 'Vald år',
				'type' => 'number',
				'defaultValue' => '0',
			),
		)
	);

	public function collectData(){

		if ($this->getInput('year') == '0') {
			$year = date("Y");
		} else {
			$year = $this->getInput('year');
		}
		$this->logger->debug($year);


		$current_url = self::FETCH . '&Year=' . $year;
		$this->logger->debug($current_url);

		$html = getSimpleHTMLDOMCached($current_url)
		or returnServerError('Could not request list: ' . self::URI);

		$content = $html->find('div[id=content]', 0);
		$this->logger->debug($content);
		foreach($content->find('li') as $element) {
			if(count($this->items) > self::LIMIT) {
				break;
			}

			$this->logger->debug("Element: " . $element);

			$title_text = $element->find('a',0)->plaintext;
			$this->logger->debug('Metadata: ' . $title_text);

			$title = substr($title_text,13);
			$this->logger->debug('Title: ' . $title);

			$date = substr($title_text,0,10);
			$this->logger->debug('Date: ' . $date);

			$url = self::BASEURI . $element->find('a',0)->href;
            $this->logger->debug('URL: ' . $url);

			$article_html = getSimpleHTMLDOMCached($url, 18000);

			$article_text = $article_html->find('div.startPageExtrasContent',0)->innertext;
			$article_text = str_replace('="../', '="' . self::URI . '/' ,$article_text);
			$this->logger->debug('Content: ' . $article_text);

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = self::AUTHOR;
			$item['timestamp'] = $date;
			$item['content'] = trim($article_text);
            $this->items[] = $item;
			// break;
		}
	}
}
