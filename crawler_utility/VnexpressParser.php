<?php
// Parser for parsing content from the vn website
class VnexpressParser extends Parser {
    public function parse() {
        $html = $this->get_html();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass('title-detail');
            $content = $this->getElementsByClass('fck_detail');
            $date = $this->getElementsByClass('date');
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}