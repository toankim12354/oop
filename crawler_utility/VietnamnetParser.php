<?php
// Parser for parsing content from the Vietnamnet website
class VietnamnetParser extends Parser {
    public function parse() {
        $html = $this->get_html();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title  = $this->getElementsByClass('content-detail-title');
            // Get the content
            $content = $this->getElementsByClass('maincontent main-content');
            // Get the publication
            $date = $this->getElementsByClass('bread-crumb-detail__time');
            return ['title' => $title,'content' => $content, 'date' => $date];
        }
        return null;
    }
}