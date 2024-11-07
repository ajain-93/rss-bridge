<?PHP

class SiljanNewsBridge extends BridgeAbstract
{
    const NAME = 'Siljan News';
    const URI = 'https://www.siljannews.se';
    const BASEURI = 'https://admin.siljannews.se';
    const DESCRIPTION = 'Latest news by Siljan News';
    const MAINTAINER = 'ajain-93';
    const PARAMETERS = [
        [
            'platform' => [
                'name' => 'Platform',
                'type' => 'list',
                'values' => [
                    'Ludvika & Smedjebacken' => '114',
                    'Södra Siljan' => '77',
                    'Norra Dalarna' => '78',
                    'Västerdalarna' => '79',
                    'Säter News' => '92',
                ]
            ]
        ]
    ];

    public function getIcon()
    {
        return self::URI . '/icon_120x120.ad4849021a6c8d43d68bab270c0d075e.png';
    }

    public function collectData()
    {
        $NEWSURL = self::BASEURI . '/wp-json/sn/v1/feed?_embed=true&page=1&per_page=5&sn_region=' . $this->getInput('platform');

        $parsedJson = Json::decode(getContents($NEWSURL), false);

        foreach ($parsedJson as $element) {

            $this->items[] = [
                'uri' => $element->link,
                'title' => $element->title->unescaped,
                'author' => $element->_embedded->author[0]->name,
                'timestamp' => $element->modified_gmt,
                'content' => $element->content->rendered,
            ];
        }
    }
}
