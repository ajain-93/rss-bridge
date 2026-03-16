<?PHP

class BRFKungsljusetBridge extends BridgeAbstract
{
	const NAME = 'Brf Kungsljuset';
	const BASEURI = 'https://www.hsb.se';
	const URI = '/malardalarna/brf/Kungsljuset/nyheter/';
	const DESCRIPTION = 'Latest news by Brf Kungsljuset.';
	const MAINTAINER = 'ajain-93';

	public function getIcon()
	{
		return self::BASEURI . '/Static/Common/img/apple-touch-icon.png';
	}

	public function collectData()
	{
		$NEWSURL = $this->getURI();

		$html = getSimpleHTMLDOM($NEWSURL) or
			throwServerException('Could not request: ' . $NEWSURL);

		foreach ($html->find('li.item') as $element) {
			$itemURL = self::BASEURI . $element->find('a', 0)->href;
			$this->items[] = $this->parseNewsItems($itemURL);
		}
	}

	private function parseNewsItems($url)
	{
		$html = getSimpleHTMLDOM($url) or
			throwServerException('Could not request: ' . $url);

		$attachmentCount = $html->find('div.file-list-block', 0)->getAttribute('data-total-count') ?? 0;
		$attachementText = "($attachmentCount tillhörande bilagor)";

		return [
			'uri' => $url,
			'title' => $html->find('title', 0)->plaintext,
			'author' => 'HSB BRF Kungsljuset',
			'timestamp' => $this->searchAttribute($html->find('meta'), 'name', 'created')->content,
			'content' => $html->find('div.breadtext', 0)->plaintext . '<br/><br/>' . $attachementText,
		];

	}

	public function getURI()
	{
		return self::BASEURI . self::URI;
	}

	private function searchAttribute($elements, $attribute, $value)
	{
		foreach ($elements as $element) {
			if (strpos($element->getAttribute($attribute), $value) !== false) {
				return $element;
			}
		}
		return null;
	}
}
