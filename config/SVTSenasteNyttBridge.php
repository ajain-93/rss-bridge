<?PHP

class SVTSenasteNyttBridge extends BridgeAbstract
{
    const NAME          = 'SVT Nyheter Senaste Nytt';
    const URI           = 'https://www.svt.se';
    const DESCRIPTION   = 'Latest news by SVT Nyheter about a certain topic.';
    const MAINTAINER    = 'ajain-93';
    const PARAMETERS = [
        [
            'topic' => [
                'name' => 'Topic',
                'title' => 'The topic to get the latest news about.',
                'required' => true,
                'exampleValue' => 'nyheter/utrikes/senaste-nytt-om-iran-och-konflikten-med-usa-och-israel',
            ],
        ]
    ];

    private $title = '';

    public function getIcon()
    {
        return 'https://www.svt.se/static/build/news-apple-touch-icon-180x180-cdbff4ab906c878b99d7.png';
    }

    public function collectData()
    {
        $NEWSURL = $this->getURI();

        $html = getSimpleHTMLDOM($NEWSURL) or
            throwServerException('Could not request: ' . $NEWSURL);

        $this->title = $html->find('h1', 0)->plaintext;

        foreach ($html->find('article') as $element) {

            // if $element->find('h3',0)->plaintext is empty, skip the element
            if (empty($element->find('h3', 0)->plaintext)) {
                continue;
            }
            // $this->logger->debug($element);

            $this->items[] = [
                // 'uri' => $link,
                'title' => $element->find('h3',0)->plaintext,
                'author' => trim($this->searchAttribute($element->find('div'), 'class', 'Text__text-S-bold')->plaintext),
                // 'timestamp' => $datetime,
                // 'content' => $this->searchAttribute($element->find('div'), 'class', 'Author__body')->find('div',0)->plaintext,
                // 'content' => trim($element),
                'content' => trim($this->searchAttribute($element->find('div'), 'class', 'Post__bodyContent')->plaintext),
                // 'content' => trim($article_text),
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

    public function getName()
    {
        if ($this->title) {
            return 'SVT Nyheter - ' .  htmlspecialchars_decode($this->title);
        } else {
            return self::NAME;
        }
    }

    public function getURI()
    {
        if ($this->getInput('topic')) {
            return self::URI . '/' . $this->getInput('topic');
        } else {
            return self::URI;
        }
    }

}
