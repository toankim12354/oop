<?php
class Parser {
    protected $url;

    public function __construct($url) {

        $this->url = $url;


    }
    /**
     * check url
     * @return bool|string
     */
//Get the HTML content
    public function getHtml(): string {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }
//Get the elements in the HTML document

    /**
     * get class check html document
     * @param  string $class
     * @return string|null
     */
    protected function getElementsByClass(string $class): ?string {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $finder = new DomXPath($dom);
            $node = $finder->query("//*[contains(@class, '$class')]")->item(0);
            if ($node) {
                return $this->innerHTML($node);
            }
        }
        return null;
    }

    /**
     * get the innerHTML of a node
     *
     * @param DOMNode $node
     * @return string
     */
    public function innerHTML(DOMNode $node) {
        return strip_tags( implode(array_map([$node->ownerDocument, "saveHTML"],
            iterator_to_array($node->childNodes))));
    }

    /**
     * Parse html to array or null  if html is null
     * @return array|null
     * @throws Exception
     */
    public function parse(): ?array {
        throw new Exception('Not implemented');
    }
}
// Parser for parsing content from the vn website

class VnexpressParser extends Parser {
    // get class  title from vnexpress
    const title_Vnexpress = 'title-detail';
    // get class content from vnexpress
    const content_Vnexpress = 'fck_detail';
    // get class date
    const date_Vnexpress = 'date';
    /**
     * @inheritDoc
     */
    public function parse(): ?array {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass(self::title_Vnexpress);
            $content = $this->getElementsByClass(self::content_Vnexpress);
            $date = $this->getElementsByClass(self::date_Vnexpress);
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}
// Parser for parsing content from the Dan Tri website
class DantriParser extends Parser {

    // get class title  name from dan tri
    const  title_Dantri = 'title-page detail';
    // get class content from dan tri
    const  content_Dantri = 'fck_detail';
    // get class date from dan tri
    const  date_Dantri = 'date';
    /**
     * @inheritDoc
     */

    public function parse() {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass( self::title_Dantri);
            $content = $this->getElementsByClass(fck_detail::content_Dantri);
            $date = $this->getElementsByClass(self::date_Dantri);
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}
// Parser for parsing content from the Vietnamnet website
class VietnamnetParser extends Parser {

    // get class title  name from vietnamnet
    const title_Vietnamnet = 'content-detail-title';
    // get class content from vietnamnet
    const content_Vietnamnet = 'maincontent main-content';
    // get class date from vietnamnet
    const date_Vietnamnet = 'bread-crumb-detail__time';
    /**
     * @inheritDoc
     */
    public function parse() {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title  = $this->getElementsByClass( self::title_Vietnamnet);
            // Get the content
            $content = $this->getElementsByClass( self::content_Vietnamnet);
            // Get the publication
            $date = $this->getElementsByClass( self::date_Vietnamnet);
            return ['title' => $title,'content' => $content, 'date' => $date];
        }
        return null;
    }
}