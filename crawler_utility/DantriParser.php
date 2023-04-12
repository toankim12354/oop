<?php
require 'Parser.php';
require 'Curl.php';
class DantriParser extends Parser {
    public function parse() {
        $html = $this->get_html();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $dom->getElementsByTagName('h1')[0]->textContent;

            $class = $this->getElementsByClass('singular-content');
            $content = $class;
            $tg = $this->getElementsByClass('author-time');
            $date = $tg;

            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}