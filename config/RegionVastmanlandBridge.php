<?PHP
class RegionVastmanlandBridge extends BridgeAbstract
{
	const NAME = 'Region Västmanland';
	const BASEURI = 'https://regionvastmanland.se';
	const URI = '/nyheter';
	const DESCRIPTION = 'Latest news Region Västmanland.';
	const MAINTAINER = 'ajain-93';

	public function collectData()
	{
		$html = getSimpleHTMLDOM($this->getURI()) or
			throwServerException('Could not request: ' . $this->getURI());

		foreach ($html->find('div.newslist--listed__list-item__content') as $element) {

			$author = $element->find('div.meta', 0)->plaintext;
			$title = $element->find('h2', 0)->plaintext;
			$url = self::BASEURI . $element->find('div.meta', 0)->href;

			$date = $element->find('div.newslist--listed__list-item__content__date', 0)->plaintext;
			$months = [
				'januari' => '01', 'februari' => '02', 'mars' => '03', 'april' => '04',
				'maj' => '05', 'juni' => '06', 'juli' => '07', 'augusti' => '08',
				'september' => '09', 'oktober' => '10', 'november' => '11', 'december' => '12'
			];
			$dateParts = explode(' ', trim($date));
			$day = str_pad($dateParts[0], 2, '0', STR_PAD_LEFT);
			$month = $months[strtolower($dateParts[1])] ?? '01';
			$year = $dateParts[2];

			$content = $element->innertext;
			$content = str_replace($element->find('div.meta', 0)->outertext, '', $content);
			$content = str_replace($element->find('h2', 0)->outertext, '', $content);
			$content = str_replace($element->find('div.newslist--listed__list-item__content__date', 0)->outertext, '', $content);



			$item['uri'] = $url;
			$item['timestamp'] = strtotime("$year-$month-$day");
			$item['title'] = htmlspecialchars_decode($title);
			$item['author'] = $author;
			$item['content'] = trim($content);
			$this->items[] = $item;
		}
	}

	public function getIcon()
	{
		return self::BASEURI . '/Static/Images/Favicons/apple-touch-icon-120x120.png';
	}
	public function getURI()
	{
		return self::BASEURI . self::URI;
	}
}
