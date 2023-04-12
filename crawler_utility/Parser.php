<?php
class Parser {
    protected $url;

    public function __construct($url) {
        $this->url = $url;
    }
//Get the HTML content
    protected function get_html() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }
//Get the elements in the HTML document
    protected function getElementsByClass($class) {
        $html = $this->get_html();
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



    protected function innerHTML(DOMNode $node) {
        return implode(array_map([$node->ownerDocument, "saveHTML"],
            iterator_to_array($node->childNodes)));
    }

    public function parse() {
        throw new Exception('Not implemented');
    }
}