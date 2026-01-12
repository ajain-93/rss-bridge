<?PHP

class SVTSnabbkollenBridge extends BridgeAbstract
{
    const NAME          = 'SVT Nyheter Snabbkollen';
    const URI           = 'https://www.svt.se';
    const DESCRIPTION   = 'Latest news by SVT Nyheter Snabbkollen';
    const MAINTAINER    = 'ajain-93';

    public function getIcon()
    {
        return 'https://www.svt.se/static/build/news-apple-touch-icon-180x180-cdbff4ab906c878b99d7.png';
    }

    public function collectData()
    {
        $NEWSURL = self::URI;

        $html = getSimpleHTMLDOM($NEWSURL) or
            throwServerException('Could not request: ' . $NEWSURL);

        $html_snabbkollen = $this->searchAttribute($html->find('ul'), 'class', 'MostImportant__list');

        if (!$html_snabbkollen) {
            // If no Snabbkollen articles available.
            return;
        }
        foreach ($html_snabbkollen->find('a') as $element) {
            // $this->logger->debug($element);

            $link = self::URI . $element->getAttribute('href');
            // $this->logger->debug($link);

            $article_html = getSimpleHTMLDOM($link) or
                throwServerException('Could not request: ' . $link);

            $article_content = $article_html->find('article', 0);
            // $this->logger->debug($article_content);

            $title = $article_content->find('h1',0)->plaintext;
            $author = $this->searchAttribute($article_content->find('span'), 'class', 'ArticleFooterAuthor__name')->plaintext;
            $datetime = $this->searchAttribute($article_content->find('time'), 'class', 'ArticleFooterTimestamps__timestamp')->getAttribute('datetime');
            $article_text = $this->searchAttribute($article_content->find('div'), 'class', 'TextArticle__body');

            // $this->logger->debug($title);
            // $this->logger->debug($author);
            // $this->logger->debug($datetime);
            // $this->logger->debug($article_text);

            $this->items[] = [
                'uri' => $link,
                'title' => $title,
                'author' => trim($author),
                'timestamp' => $datetime,
                'content' => trim($article_text),
            ];
        }
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
