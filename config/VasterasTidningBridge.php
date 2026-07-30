<?PHP

class VasterasTidningBridge extends BridgeAbstract
{
	const NAME = 'Västerås Tidning';
	const BASEURI = 'https://www.vasterastidning.se';
	const URI = '/nyheter';
	const DESCRIPTION = 'Latest news by Västerås Tidning.';
	const MAINTAINER = 'ajain-93';

	public function getIcon()
	{
		return self::BASEURI . '/view-resources/dachser2/public/vasterastidning/favicon-32x32.png';
	}

	public function collectData()
	{
		$NEWSURL = $this->getURI();

		$html = getSimpleHTMLDOM($NEWSURL) or
			throwServerException('Could not request: ' . $NEWSURL);

		$this->debugLog("ASFD");

		foreach ($html->find('article') as $element) {
			$itemURL = $element->find('a', 0)->href;
			$this->debugLog("Item URL: " . $itemURL);

			$this->items[] = $this->parseNewsItems($itemURL);

			if (count($this->items) >= 10) {
				break;
			}
		}
	}

	private function parseNewsItems($url)
	{
		$html = getSimpleHTMLDOM($url) or
			throwServerException('Could not request: ' . $url);


		$title = $html->find('h1', 0)->plaintext;
		$this->debugLog("Title: " . $title);

		$date = $html->find('time', 0)->datetime;
		$this->debugLog("Date: " . $date);

		$preamble = $html->find('p.subtitle', 0)->plaintext;
		$this->debugLog("Preamble: " . $preamble);

		$author = $html->find('address.name', 0)->find('a', 0)->plaintext;
		$this->debugLog("Author: " . $author);

		$article_text = $html->find('div.bodytext', 0);
		$tags_to_remove = ['script', 'iframe', 'input', 'form'];
		$attributes_to_keep = ['title', 'href', 'src', 'br', 'p', 'i', 'strong', 'em', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
		$text_to_keep = [];
		$article_text = sanitize($article_text, $tags_to_remove, $attributes_to_keep, $text_to_keep);
		$this->debugLog("Text: " . $article_text);

		$factbox = $html->find('div.factbox', 0);
		$this->debugLog("Factbox: " . $factbox);


		$figure = $html->find('div.media', 0)->find('source', 0)->srcset;
		$this->debugLog("Figure: " . $figure);
		$caption = $html->find('div.caption ', 0)->plaintext;
		$this->debugLog("Caption: " . $caption);

		if ($figure == null) {
			$content = "<i>{$preamble}</i><br/><br/> {$article_text}";
		} else {
			$content = "<i>{$preamble}</i><br/><br/><img src=\"{$figure}\" /><br/>{$caption}<br/><br/> {$article_text}";
		}
		$this->debugLog("Content: " . $content);

		return
			[
				'uri' => $url,
				'title' => $title,
				'author' => $author,
				'timestamp' => $date,
				'content' => $content,
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
	private function debugLog($message)
	{
		if (Configuration::getConfig('system', 'env') === 'dev') {
			$this->logger->info(sprintf('[VasterasTidningBridge] %s', $message));
		}
	}
}
