<?PHP
class TrafikverketRV5066LudvikaBridge extends BridgeAbstract {

	const NAME		  = 'Trafikverket Väg 50/66 genom Ludvika';
	// const URI		  = 'https://www.trafikverket.se/vara-projekt/projekt-i-dalarnas-lan/vag-5066-genom-ludvika-forbattrad-trafiksakerhet-och-framkomlighet/nyheter-for-vag-5066-genom-ludvika/';
	const URI		  = 'https://www.trafikverket.se/';
	const PAGEID	  = '15059';
	const DESCRIPTION = 'Returns the latest new from the project Väg 50/66 genom Ludvika';
	const MAINTAINER  = 'ajain-93';

	public function collectData(){

		$URL = self::URI . 'api/localnews?pageID=' . self::PAGEID;
		$html = getSimpleHTMLDOMCached($URL)
			or returnServerError('Could not request list: ' . $URL);

		// $this->logger->debug('Getting Data');

		foreach($html->find('ListingItem') as $element) {

			$title = $element->find('Name', 0)->plaintext;
			$link = extractFromDelimiters($element, "<link>","</Link>");
			// $this->logger->debug($title);
			// $this->logger->debug($link);

			// $byline = $element->find('p.byline', 0);
			$author = 'Trafikverket';
			$date = strtotime($element->find('Changed', 0)->plaintext);
			// $this->logger->debug($byline);
			// $this->logger->debug($date);
			// $this->logger->debug($author);

			$picture = $element->find('PictureTag', 0)->plaintext;
			$picture = self::URI . extractFromDelimiters($picture, "srcset='/","?format");
			// $this->logger->debug($picture);

			$preamble = $element->find('Preamble', 0)->plaintext;
			// $this->logger->debug($preamble);

			$html_content = getSimpleHTMLDOMCached($link)
				or returnServerError('Could not request list: ' . $link);
			$text = $html_content->find('div#mainBodyArea',0);

			$content = '<img src="' . $picture . '?width=440&mode=crop&heightratio=0.5&quality=80" alt="" loading="lazy" /><br/><br/><i><b>' . $preamble . "</b></i><br/><br/>" . $text;

			$item = array();
			$item['title'] = $title;
			$item['uri'] = $link;
			$item['author'] = $author;
			$item['timestamp'] = $date;
			$item['content'] = trim($content);
			$this->items[] = $item;
			// break;
		}
	}
}
