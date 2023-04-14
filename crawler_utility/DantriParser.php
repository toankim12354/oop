<?php
// Parser for parsing content from the Dan Tri website

class DantriParser extends Parser {
    public function parse() {
        $html = $this->get_html();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass('title-page detail');
            $content = $this->getElementsByClass('singular-content');
            $date = $this->getElementsByClass('author-time');
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}