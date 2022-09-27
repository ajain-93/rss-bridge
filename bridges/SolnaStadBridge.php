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

            $article_html = getSimpleHTMLDOMCached($url, 1800)
				or returnServerError('Could not request article: ' . self::URI);

			$content_div = $article_html->find('.pagecontent.sv-layout', 0);
			foreach($content_div->find('div.sv-text-portlet.sv-use-margins') as $div) {

				// Debug::log($div);

				$isTitle = strlen($div->find('div#Rubrik', 0)) > 0;
				if ($isTitle) {
					$title = $div->find('div.sv-text-portlet-content', 0)->plaintext;
					// Debug::log($title);
				}
				$isIntro = strlen($div->find('div#Ingress', 0)) > 0;
				if ($isIntro) {
					$intro = $div->find('div.sv-text-portlet-content', 0);
					// Debug::log($intro);
				}
				$isText = strlen($div->find('div#Text', 0)) > 0;
				if ($isText) {
					$text = $div->find('div.sv-text-portlet-content', 0);
					// Debug::log($text);
				}
			}

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = 'Solna Stad';
			$item['timestamp'] = $datetime;
			$item['content'] = trim("<i>" . $intro . "</i>" . $text);
			$this->items[] = $item;
		}
	}
}
