<?PHP
class RegionDalarnaBridge extends BridgeAbstract {

	const NAME		  = 'Region Dalarna';
	const URI		  = 'https://www.regiondalarna.se';
	const DESCRIPTION = 'Returns the latest news articles by Region Dalarna';
	const MAINTAINER  = 'ajain-93';
	const CACHE_TIMEOUT = 300; // 5min

	public function collectData(){


		$newsURL = self::URI . '/press/nyheter-och-pressmeddelanden/';

		$html = getSimpleHTMLDOMCached($newsURL)
		or returnServerError('Could not request list: ' . $newsURL);

		// $this->logger->debug('LogTestStart');

		foreach($html->find('a.no-1177') as $element) {
			// $this->logger->debug($element);

			$title = $element->find('h2', 0)->plaintext;
			// $this->logger->debug('Title: ' . $title);

			$url = self::URI . $element->href;
			// $this->logger->debug('URL: ' . $url);

			$date = $element->find('.text-muted.mb-0',0)->plaintext;
			// $this->logger->debug('Date: ' . $date);

			$category = $element->find('.text-muted',1)->plaintext;
			// $this->logger->debug('Category: ' . $category);


			$article_html = getSimpleHTMLDOMCached($url, 18000)
				or returnServerError('Could not request article: ' . $article_html);

			// $this->logger->debug('URL: ' . $url);

			$article = $article_html->find('div.article', 0);
			// $this->logger->debug('Article HTML: ' . $article_html);

			// $preamble = $article->find('h3', 0)->plaintext;
			// $this->logger->debug('Preamble: ' . $preamble);

			// $article_text = $article_html->find('div.editorial-content', 0);
			// $this->logger->debug('Text: ' . $article_text);

			// $figure = $article->find('figure', 0);
			// $this->logger->debug('Figure: ' . $figure);

			// if ($figure == null) {
			// 	$content = "<i>{$preamble}</i><br/><br/> {$article_text}";
			// 	// $this->logger->debug('Content: ' . $content);
			// } else {
			// 	$cover_image = $figure->find('img',0)->src;
			// 	// $this->logger->debug('CoverImage: ' . $cover_image);

			// 	if ($figure->find('figcaption',0) != null) {
			// 		$cover_caption = "<i>" . $figure->find('span',0)->plaintext . "</i><br/>";
			// 		// $this->logger->debug('CoverCaption ' . $cover_caption);
			// 	} else {
			// 		$cover_caption = "";
			// 	}

			// 	$content = "<i>{$preamble}</i><br/><img src=\"{$cover_image}\" /><br/>{$cover_caption}<br/> {$article_text}";
			// 	// $this->logger->debug('Content: ' . $content);
			// }

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = $category;
			$item['timestamp'] = $date;
			// $item['content'] = trim($content);
			$item['content'] = trim($article);
			$this->items[] = $item;
			// break;
		}
	}
}
