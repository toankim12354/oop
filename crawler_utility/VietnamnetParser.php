<?php
require 'Parser.php';
class VietnamnetParser extends Parser {
    public function parse() {
        $html = $this->get_html();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $dom->getElementsByTagName('h1')[0]->textContent;
            // Get the content
            $class = $this->getElementsByClass('maincontent main-content');
            $content = $class;
            // Get the publication
            $tg = $this->getElementsByClass('bread-crumb-detail__time');
            $date = $tg;

            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}