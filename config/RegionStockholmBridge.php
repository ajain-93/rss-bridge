<?PHP
class RegionStockholmBridge extends BridgeAbstract {

    const NAME          = 'Region Stockholm';
    const URI           = 'https://www.regionstockholm.se';
    const DESCRIPTION   = 'Returns the latest news articles by Region Stockholm';
    const MAINTAINER    = 'ajain-93';
    const CACHE_TIMEOUT = 300; // 5min

    public function collectData(){

        $html = getSimpleHTMLDOMCached(self::URI . '/Nyheter')
        or returnServerError('Could not request list: ' . self::URI);

        // $this->logger->debug('LogTestStart');

        foreach($html->find('article') as $element) {
        // foreach($html->find('div.filter-list-item') as $element) {

            $title_span = $element->find('h2', 0)->find('a', 0);
            // $this->logger->debug('Title Span: ' . $title_span);

            $title = $title_span->plaintext;
            // $this->logger->debug('Title: ' . $title);

            $url = 	$title_span->href;
            // $this->logger->debug('URL: ' . $url);

            $date = $element->find('time', 0)->datetime;
            // $this->logger->debug('Date: ' . $date);

            $category = $element->find('div div span',0)->plaintext;
            // $this->logger->debug('Category: ' . $category);

            $preamble = $element->find('p', 0)->plaintext;
            // $this->logger->debug('Preamble: ' . $preamble);

            $article_html = getSimpleHTMLDOMCached($url, 18000)
                or returnServerError('Could not request article: ' . self::URI);

            $article = $article_html->find('div.prose', 0);
            $article_text = $article->find('div', 2);
            // $this->logger->debug('Text: ' . $article_text);

            $figure = $article_html->find('img.object-cover', 0)->src;
            $figure = str_replace('/_next/image/?url=', '', $figure);
            $figure = urldecode($figure);
            $figure = substr($figure, 0, strpos($figure, '&'));
            // $this->logger->debug('Figure: ' . $figure);

            if ($figure == null) {
                $content = "<i>{$preamble}</i><br/><br/> {$article_text}";
            } else {
                $content = "<i>{$preamble}</i><br/><img src=\"{$figure}\" /><br/><br/> {$article_text}";
                // $content = "<i>{$preamble}</i><br/><img src=\"{$cover_image}\" /><br/>{$cover_caption}<br/> {$article_text}";
            }
            // $this->logger->debug('Content: ' . $content);

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
