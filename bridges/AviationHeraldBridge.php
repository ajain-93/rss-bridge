<?PHP
class AviationHeraldBridge extends BridgeAbstract {

	const NAME		  = 'The Aviation Herald';
	const URI		  = 'https://avherald.com';
	const DESCRIPTION = 'Returns the latest articles';
	const MAINTAINER  = 'ajain-93';
	const PARAMETERS = array(
		'' => array(
			'crashes' => array(
				'name' => 'Include Crashes',
				'type' => 'checkbox',
				'defaultValue' => 'checked',
			),
			'accidents' => array(
				'name' => 'Include Accidents',
				'type' => 'checkbox',
				'defaultValue' => 'checked',
			),
			'incidents' => array(
				'name' => 'Include Incidents',
				'type' => 'checkbox',
				'defaultValue' => 'checked',
			),
			'news' => array(
				'name' => 'Include News',
				'type' => 'checkbox',
				'defaultValue' => 'checked',
			),
			'reports' => array(
				'name' => 'Include Reports',
				'type' => 'checkbox',
				'defaultValue' => 'checked',
			),
		)
	);

	public function collectData(){

		$powBase = 8;
		$options = pow(2,5+$powBase) - pow(2,$powBase);
		if ($this->getInput('crashes') == 1) {
			$options = $options - pow(2,0+$powBase);
		};
		if ($this->getInput('accidents') == 1) {
			$options = $options - pow(2,1+$powBase);
		};
		if ($this->getInput('incidents') == 1) {
			$options = $options - pow(2,2+$powBase);
		};
		if ($this->getInput('news') == 1) {
			$options = $options - pow(2,3+$powBase);
		};
		if ($this->getInput('reports') == 1) {
			$options = $options - pow(2,4+$powBase);
		};

		// $this->logger->debug($this->getInput('crashes'));
		// $this->logger->debug($this->getInput('accidents'));
		// $this->logger->debug($this->getInput('incidents'));
		// $this->logger->debug($this->getInput('news'));
		// $this->logger->debug($this->getInput('reports'));
		// $this->logger->debug($options);

		$this->logger->debug(self::URI . "/h?opt=" . $options);
		$url = self::URI . "/h?opt=" . $options;

		$html = getSimpleHTMLDOM($url)
			or returnServerError('Could not request list: ' . $url);

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
			$category = $element->find('img', 0)->title;
			$title = substr($category,0,1) . ": " . $title_span->plaintext;
			$link = $element->find('a', 0);
			$url = self::URI . $link->href;

			// $this->logger->debug($element);
			// $this->logger->debug("Article: " . $title);
			// $this->logger->debug("Link: " . $link);
			// $this->logger->debug("URL: " . $url);
			// $this->logger->debug("Category: " . $category);

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
