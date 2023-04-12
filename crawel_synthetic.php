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
// Parser for parsing content from the vn website
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
// Parser for parsing content from the Dan Tri website   Authentication failed for
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
// Parser for parsing content from the Vietnamnet website
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
class DB {
    protected $conn;
    public function __construct($host, $username, $password, $dbname) {
        $this->conn = mysqli_connect($host, $username, $password, $dbname);
    }

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

class CURL {
    public static function get($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

$db = new DB('localhost', 'toanlt', 'Toanlt123', 'Parser');
$url = 'https://vietnamnet.vn/my-tam-tiet-lo-chuyen-choi-game-tin-nhan-cho-nguoi-ay-2131430.html';
$vnexpress_parser = new VnexpressParser($url);
$VietnamnetParser = new VietnamnetParser($url);
$DantriParser = new DantriParser($url);
$data = $vnexpress_parser->parse();
$data = $VietnamnetParser->parse();
$data = $DantriParser->parse();
$title = $db->escape($data['title']);
$content = $db->escape($data['content']);
$data = $db->escape($data['date']);
$sql = "INSERT INTO wrapper (title, content, thoi_gian) VALUES ('$title', '$content', '$data')";
    $db->query($sql);




