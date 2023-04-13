<?php

class Parser {
    protected $url;

    public function __construct($url) {

            $this->url = $url;


    }
//Get the HTML content
    public function get_html() {
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
    public function innerHTML(DOMNode $node) {
        return strip_tags( implode(array_map([$node->ownerDocument, "saveHTML"],
            iterator_to_array($node->childNodes))));
    }
    public function parse() {
        throw new Exception('Not implemented');
    }
}
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
//connect dtabasea
class DB {
    protected $conn;
    public function __construct($host, $username, $password, $dbname) {
        $this->conn = mysqli_connect($host, $username, $password, $dbname);
    }
//data processed with in the database
    public function query($sql) {
        try {
            return mysqli_query($this->conn, $sql);
        } catch (mysqli_sql_exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
    public function escape($value) {
        return mysqli_real_escape_string($this->conn, $value);
    }
}
$db = new DB('localhost', 'toanlt', 'Toanlt123', 'Parser');
$url = 'https://dantri.com.vn/giao-duc-huong-nghiep/ha-noi-ca-covid-19-tang-1535-hoc-sinh-mot-lop-12-nghi-vi-om-sot-20230413101238282.htm';
//$vnexpress_parser = new VnexpressParser($url);
//$VietnamnetParser = new VietnamnetParser($url);
$DantriParser = new DantriParser($url);
//$data = $vnexpress_parser->parse();
//$data = $VietnamnetParser->parse();
$data = $DantriParser->parse();
$title = $db->escape($data['title']);
$content = $db->escape($data['content']);
$date = $db->escape($data['date']);
if(!empty($data)) {
    $sql = "INSERT INTO wrapper (title, content, thoi_gian) VALUES ('$title', '$content', '$date')";
    $db->query($sql);

} else {
    // handle the case where $date is empty
    echo "nor";
}








