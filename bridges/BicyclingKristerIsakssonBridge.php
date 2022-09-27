<?PHP
class BicyclingKristerIsakssonBridge extends BridgeAbstract {

	const NAME		  = 'Bicycling - Krister Isaksson';
	const URI		  = 'https://bicycling.se/author/KristerIsaksson';
	const DESCRIPTION = 'Returns the latest articles by Krister Isaksson';
	const MAINTAINER  = 'ajain-93';

	public function collectData(){

		$html = getSimpleHTMLDOM(self::URI)
			or returnServerError('Could not request list: ' . self::URI);

		foreach($html->find('article') as $element) {

            $title_span = $element->find('h2.entry-title', 0);
            $title = $title_span->plaintext;
			$link = $title_span->find('a', 0);
			$url = $link->href;

            // Debug::log('');

            // Debug::log($title_span);
            // Debug::log($title);
            // Debug::log($url);

            $byline = $element->find('p.byline', 0);
            $date = $byline->find('span',0)->plaintext;
            $author = $byline->find('a',0)->plaintext;

            // Debug::log('');

            // Debug::log($byline);
            // Debug::log($date);
            // Debug::log($author);

            $content = $element->find('div.row', 0);

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = $author;
			$item['timestamp'] = $date;
			$item['content'] = trim($content);
            $this->items[] = $item;
            // break;
		}
	}
}
