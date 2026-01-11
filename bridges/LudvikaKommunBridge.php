<?PHP
class LudvikaKommunBridge extends BridgeAbstract {

	const NAME			= 'Ludvika Kommun';
	const URI			= 'https://www.ludvika.se';
	const DESCRIPTION	= 'News from Municipality of Ludvika';
	const MAINTAINER	= 'ajain-93';
	const NEWSPAGE		= '/nyheterochservicemeddelanden/nyheter.4.7c82b0fc1638b8db71b12ce9.html';
	const AUTHOR		= 'Ludvika Kommun';
	const LIMIT			= 20;

	public function getIcon(){
		return self::URI . '/images/18.421f7e0816662b0123c5f7c7/1542292999480/favicon-32x32.png';
	}

	public function collectData(){

		$NEWSURL = self::URI . self::NEWSPAGE;


		$html = getSimpleHTMLDOM($NEWSURL)
			or returnServerError('Could not request: ' . $NEWSURL);

		foreach($html->find('li.sv-channel-item') as $element) {

			// $this->logger->debug($element);

			$link = $element->find('a', 0);
			$url = self::URI . $link->href;
			$title = $element->find('h2', 0)->plaintext;
			$intro = '';
			$text = '';
			$datetime = strtotime($element->find('time', 0)->datetime);

			// $this->logger->debug($link);
			// $this->logger->debug($url);
			// $this->logger->debug($title);
			// $this->logger->debug($datetime);

			$article_html = getSimpleHTMLDOMCached($url, 1800)
			or returnServerError('Could not request article: ' . $url);

			$content_div = $article_html->find('.pagecontent.sv-layout', 1);
			// $this->logger->debug($content_div);

			foreach($content_div->find('div.sv-text-portlet.sv-use-margins') as $div) {

				// $this->logger->debug($div);

				$isTitle = strlen($div->find('div#Rubrik', 0)) > 0;
				if ($isTitle) {
					$title = $div->find('div.sv-text-portlet-content', 0)->plaintext;
					// $this->logger->debug($title);
				}

				$isIntro = strlen($div->find('div#Ingress', 0)) > 0;
				if ($isIntro) {
					$intro = $div->find('div.sv-text-portlet-content', 0);
					// $this->logger->debug($intro);
				}

				$isText = strlen($div->find('div#Innehall', 0)) > 0;
				if ($isText) {
					$text = $div->find('div.sv-text-portlet-content', 0);
					// $this->logger->debug($text);
				}
			}

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = self::AUTHOR;
			$item['timestamp'] = $datetime;
			$item['content'] = trim("<i>" . $intro . "</i>" . $text);
			$this->items[] = $item;

			$limit = $this->getInput('limit') ?: -1;

			if (count($this->items) > self::LIMIT) {
				break;
			}
		}
	}
}
