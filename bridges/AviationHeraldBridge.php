<?PHP
class AviationHeraldBridge extends BridgeAbstract {

	const NAME		  = 'The Aviation Herald';
	const URI		  = 'https://avherald.com';
	const DESCRIPTION = 'Returns the latest articles';
	const MAINTAINER  = 'ajain-93';

	public function collectData(){

		$html = getSimpleHTMLDOM(self::URI)
			or returnServerError('Could not request list: ' . self::URI);

		$limit = 10;

		foreach($html->find('td#ad1cell.center_td table') as $element) {

			if(count($this->items) > $limit) {
				break;
			}

			$title_span = $element->find('span.headline_avherald', 0);
			$is_article = strlen($title_span) > 0;
			if (!$is_article) {
				continue;
			}
			$title = $title_span->plaintext;
			$link = $element->find('a', 0);
			$url = self::URI . $link->href;

			// Debug::log($element);
			// Debug::log("Article: " . $title);
			// Debug::log("Link: " . $link);
			// Debug::log("URL: " . $url);

			$article_html = getSimpleHTMLDOMCached($url, 18000)
				or returnServerError('Could not request article: ' . self::URI);

			$metadata = $article_html->find('.time_avherald', 0)->plaintext;
			$last_updated_start = strpos($metadata, "last updated");
			if ($last_updated_start > 0) {
				$time_start = $last_updated_start + 13;
			} else {
				$time_start = strpos($metadata, "created") + 8;
			}
			$datetime = substr($metadata, $time_start);

			$content_all = $article_html->find('td#ad1cell.center_td', 0);
			$content = $content_all->find('span.sitetext', 0);

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = 'Simon Hradecky';
			$item['timestamp'] = $datetime;
			$item['content'] = trim($content);
			$this->items[] = $item;
		}
	}
}
