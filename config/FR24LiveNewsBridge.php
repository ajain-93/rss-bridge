<?PHP
class FR24LiveNewsBridge extends BridgeAbstract
{
	const NAME = 'Flight Radar 24 Live News';
	const BASEURI = 'https://www.flightradar24.com';
	const URI = '/blog/live';
	const DESCRIPTION = 'Latest news from Flight Radar 24.';
	const MAINTAINER = 'ajain-93';
	const PARAMETERS = [
		[
			'topic' => [
				'name' => 'Topic URL',
				'title' => 'The topic URL to get the latest news about.',
				'required' => true,
				'exampleValue' => '/israel-launches-pre-emptive-strikes-on-iran-airspace-closures-going-into-place',
			],
		]
	];
	private $title = '';

	public function collectData()
	{
		$html = getSimpleHTMLDOM($this->getURI()) or
			throwServerException('Could not request: ' . $this->getURI());

		$this->title = $html->find('h2', 0)->plaintext;
		$author = $html->find('div.elementor-author-box__text', 0)->plaintext;

		$content_div = $html->find('#live-blog-container div.elementor-shortcode', 0);
		$item = null;

		foreach ($content_div->find('div') as $element) {

			if (strpos($element->getAttribute('class'), 'update_time') !== false) {
				$item = array();
				$item['timestamp'] = $element->plaintext;
				continue;
			}
			if (strpos($element->getAttribute('class'), 'update_text') !== false) {

				$content = $element->innertext;
				$heading = $element->find('h3', 0);
				if ($heading) {
					$content = str_replace($heading->outertext, '', $content);
				}

				$item['uri'] = $this->getURI();
				$item['title'] = htmlspecialchars_decode($heading);
				$item['author'] = $author;
				$item['content'] = trim($content);
				$this->items[] = $item;

			}
		}
	}

	public function getName()
	{
		if ($this->title) {
			return 'FR24 Live - ' . htmlspecialchars_decode($this->title);
		} else {
			return self::NAME;
		}
	}

	public function getURI()
	{
		if ($this->getInput('topic')) {
			// if the topic starts with a slash, remove it
			if (strpos($this->getInput('topic'), '/') === 0) {
				return self::BASEURI . self::URI . $this->getInput('topic');
			} else {
				return self::BASEURI . self::URI . '/' . $this->getInput('topic');
			}
		} else {
			return self::BASEURI . self::URI;
		}
	}

	public function getIcon()
	{
		return self::BASEURI . '/blog/wp-content/uploads/2021/10/favicon.svg';
	}
}
