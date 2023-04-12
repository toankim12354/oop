<?php
require 'Parser.php';
require 'Curl.php';
class VnexpressParser extends Parser {
    public function parse() {
        $html = $this->get_html();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $dom->getElementsByTagName('h1')[0]->textContent;

            $class = $this->getElementsByClass('fck_detail');
            $content = $class;
            $tg = $this->getElementsByClass('date');
            $date = $tg;

            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}