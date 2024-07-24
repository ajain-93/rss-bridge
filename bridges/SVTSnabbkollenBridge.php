<?PHP

class SVTSnabbkollenBridge extends BridgeAbstract
{
    const NAME          = 'SVT Nyheter Snabbkollen';
    const URI           = 'https://www.svt.se/nyheter/snabbkollen/';
    const BASEURL       = 'https://www.svt.se';
    const DESCRIPTION   = 'Latest news by SVT';
    const MAINTAINER    = 'ajain-93';

    public function getIcon()
    {
        return 'https://www.svt.se/static/build/news-apple-touch-icon-180x180-cdbff4ab906c878b99d7.png';
    }

    public function collectData()
    {
        $NEWSURL = self::URI;

        $html = getSimpleHTMLDOM($NEWSURL) or
            returnServerError('Could not request: ' . $NEWSURL);

        foreach ($html->find('article') as $element) {

            $link = self::BASEURL . $element->find('a', 0)->getAttribute('href');
            // Debug::log($link);

            $article_html = getSimpleHTMLDOM($link) or
                returnServerError('Could not request: ' . $link);

            $article_content = $article_html->find('article', 0);
            // Debug::log($article_content);

            $title = $article_content->find('h1',0)->plaintext;
            $author = $article_content->find('ul.ArticleFooterAuthors__root___vPAVF', 0)->plaintext;
            $datetime = $article_content->find('time', 0)->getAttribute('datetime');
            $article_text = $article_content->find('.StructuredArticleBody__root___yznDI', 0);

            // Debug::log($title);
            // Debug::log($author);
            // Debug::log($datetime);
            // Debug::log($article_text);

            $this->items[] = [
                'uri' => $link,
                'title' => $title,
                'author' => trim($author),
                'timestamp' => $datetime,
                'content' => trim($article_text),
            ];
        }
    }
}
