<?PHP
class VasterasStadBridge extends BridgeAbstract
{

	const NAME = 'Västerås Stad';
	const URI = 'https://www.vasteras.se';
	const DESCRIPTION = 'News from Municipality of Västerås';
	const MAINTAINER = 'ajain-93';
	const NEWSPAGE = '/nyheter.html';
	const AUTHOR = 'Västerås Stad';
	const LIMIT = 10;

	public function getIcon()
	{
		return self::URI . '/images/18.2a23ba8615090ceccb61559/1554823126164/favicon.ico';
	}

	public function collectData()
	{

		$NEWSURL = self::URI . self::NEWSPAGE;


		$html = getSimpleHTMLDOM($NEWSURL)
			or throwServerException('Could not request: ' . $NEWSURL);

		foreach ($html->find('li.sv-channel-item') as $element) {

			// $this->logger->debug($element);

			$link = $element->find('a', 0);
			$url = self::URI . $link->href;
			$title = $element->find('h3', 0)->plaintext;
			$intro = '';
			$text = '';
			$datetime = '';

			$timeTag = $element->find('time', 0);
			if ($timeTag) {
				$datePart = trim((string) $timeTag->datetime);
				$timePart = '00:00';

				// Example text: "16 mars 2026 09:00" -> extract "09:00"
				if (preg_match('/\b([01]?\d|2[0-3]):([0-5]\d)\b/u', $timeTag->plaintext, $match)) {
					$timePart = sprintf('%02d:%02d', (int) $match[1], (int) $match[2]);
				}

				// Keep date from attribute, inject time from visible text
				$timeTag->datetime = $datePart . ' ' . $timePart;
				$datetime = strtotime($element->find('time', 0)->datetime);
			}

			$article_html = getSimpleHTMLDOMCached($url, 1800)
				or throwServerException('Could not request article: ' . $url);
			$content_div = $article_html->find('.pagecontent.sv-layout', 0);

			foreach ($content_div->find('div.sv-text-portlet.sv-use-margins') as $div) {

				$isTitle = strlen($div->find('div#Rubrik', 0)) > 0;
				if ($isTitle) {
					$title = $div->find('div.sv-text-portlet-content', 0)->plaintext;
				}

				$isIntro = strlen($div->find('div#Ingress', 0)) > 0;
				if ($isIntro) {
					$intro = $div->find('div.sv-text-portlet-content', 0);
				}

				$isText = strlen($div->find('div#Brodtext', 0)) > 0;
				if ($isText) {
					$text = $div->find('div.sv-text-portlet-content', 0);
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
