<?PHP
class RegionStockholmBridge extends BridgeAbstract {

	const NAME		  = 'Region Stockholm';
	const URI		  = 'https://www.sll.se';
	const DESCRIPTION = 'Returns the latest news articles by Region Stockholm';
	const MAINTAINER  = 'ajain-93';
	const CACHE_TIMEOUT = 300; // 5min

	public function collectData(){

		$html = getSimpleHTMLDOMCached(self::URI . '/Nyheter')
		or returnServerError('Could not request list: ' . self::URI);

		Debug::log('LogTestStart');

		foreach($html->find('a.block-link') as $element) {
		// foreach($html->find('div.filter-list-item') as $element) {

			$title_span = $element->find('h2', 0);

			Debug::log('Title Span: ' . $title_span);

			$title = $title_span->plaintext;
			Debug::log('Title: ' . $title);

			$url = self::URI . $element->href;
            Debug::log('URL: ' . $url);

			$article_html = getSimpleHTMLDOMCached($url, 18000)
				or returnServerError('Could not request article: ' . self::URI);

			// Debug::log('URL: ' . $url);

			$meta = $article_html->find('div.m-article-meta', 0);
            // Debug::log('Article HTML: ' . $article_html);

            $date = $meta->find('time.m-article-meta--pubdate',0)->datetime;
            Debug::log('Date: ' . $date);

            $category = $meta->find('strong',0)->plaintext;
            Debug::log('Category: ' . $category);

			$preamble = $article_html->find('p.preamble', 0);
            Debug::log('Preamble: ' . $preamble);

			$article_text = $article_html->find('div.editorial-content', 0);
            Debug::log('Text: ' . $article_text);

			$figure = $article_html->find('figure', 0);
            Debug::log('Figure: ' . $figure);

			if ($figure == null) {
				$content = "<i>{$preamble}</i><br/><br/> {$article_text}";
				Debug::log('Content: ' . $content);
			} else {
				$cover_image = self::URI . $figure->find('img',0)->src;
				Debug::log('CoverImage: ' . $cover_image);

				if ($figure->find('figcaption',0) != null) {
					$cover_caption = "<i>" . $figure->find('span',0)->plaintext . "</i><br/>";
					Debug::log('CoverCaption ' . $cover_caption);
				} else {
					$cover_caption = "";
				}

				$content = "<i>{$preamble}</i><br/><img src=\"{$cover_image}\" /><br/>{$cover_caption}<br/> {$article_text}";
				Debug::log('Content: ' . $content);
			}

			$item = array();
			$item['uri'] = $url;
			$item['title'] = $title;
			$item['author'] = $category;
			$item['timestamp'] = $date;
			$item['content'] = trim($content);
            $this->items[] = $item;
            // break;
		}
	}
}
