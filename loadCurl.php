<?php
class WebPage {
    private $url;
    private $content;
    public function __construct($url) {
        $this->url = $url;
    }
    public function get_content() {
        $curl = curl_init($this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $this->content = curl_exec($curl);
        curl_close($curl);
    }
    public function get_links() {
        $links = array();
        preg_match_all('/<a.*?href="(.*?)".*?>/i', $this->content, $matches);
        foreach ($matches[1] as $link) {
            if (strpos($link, 'http') === 0) {
                $links[] = $link;
            } else {
                $links[] = $this->url . '/' . ltrim($link, '/');
            }
        }
        return $links;
    }

}
// Crawl dữ liệu từ trang vnexpress, dan tri ,vnexpress
$vnexpress = new WebPage('https://vietnamnet.vn/');
$vnexpress->get_content();
$links = $vnexpress->get_links();
echo "Links from vietnamnet: " . implode(", ", $links) . "\n";


