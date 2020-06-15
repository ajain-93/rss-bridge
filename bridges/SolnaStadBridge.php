<?PHP
class SolnaStadBridge extends BridgeAbstract {

	const NAME		  = 'Solna Stad';
	const URI		  = 'https://www.solna.se/om-solna-stad/alla-nyheter';
	const DESCRIPTION = 'News from City of Solna';
	const MAINTAINER  = 'ajain-93';

	public function collectData(){

		$html = getSimpleHTMLDOM(self::URI)
			or returnServerError('Could not request: ' . self::URI);

		foreach($html->find('article.af-news__item') as $element) {

			$link = $element->find('a', 0);
			$url = $link->href;
			$title = $element->find('h3', 0)->plaintext;
			$datetime = strtotime($element->find('time', 0)->datetime);
			$content = $element->find('p', 0)->plaintext;

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = 'Solna Stad';
			$item['timestamp'] = $datetime;
			$item['content'] = trim($content);
			$this->items[] = $item;
		}
	}
}
