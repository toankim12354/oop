<?php

abstract class Parser {
    protected $url;

    public function __construct($url) {
        $this->url = $url;
    }

    abstract protected function getTitleClass(): string;
    abstract protected function getContentClass(): string;
    abstract protected function getDateClass(): string;

    abstract public function parse(): ?array;

    protected function getHtml(): string {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    protected function getElementsByClass(string $class): ?string {
        $html = $this->getHtml();
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
        return strip_tags(implode(array_map([$node->ownerDocument, "saveHTML"],
            iterator_to_array($node->childNodes))));
    }
}

class VnexpressParser extends Parser {
    protected function getTitleClass(): string {
        return 'title-detail';
    }

    protected function getContentClass(): string {
        return 'fck_detail';
    }

    protected function getDateClass(): string {
        return 'date';
    }

    public function parse(): ?array {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass($this->getTitleClass());
            $content = $this->getElementsByClass($this->getContentClass());
            $date = $this->getElementsByClass($this->getDateClass());
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}


//abstract class AbstractDatabaseConnection {
//    /**
//     * @var false|mysqli
//     */
//    protected $conn;
//
//    /**
//     * Connect to database.
//     * @param string $host
//     * @param string $username
//     * @param string $password
//     * @param string $dbname
//     */
//    public function __construct($host, $username, $password, $dbname) {
//        $this->conn = mysqli_connect($host, $username, $password, $dbname);
//    }
//
//    /**
//     * Run a query.
//     * @param $sql
//     * @return mixed
//     */
//    abstract public function query($sql);
//
//    /**
//     * Filter special characters.
//     * @param $value
//     * @return string
//     */
//    public function escape($value,$data,) {
//        $db = new DB('localhost', 'toanlt', 'Toanlt123', 'Parser');
//        $title = $db->escape($data['title']);
//        $content = $db->escape($data['content']);
//        $date = $db->escape($data['date']);
//        if(!empty($data)) {
//            $sql = "INSERT INTO wrapper (title, content, thoi_gian) VALUES ('$title', '$content', '$date')";
//            $db->query($sql);
//
//        } else {
//            // handle the case where $date is empty
//            echo "not data";
//        }
//
//        return mysqli_real_escape_string($this->conn, $value);
//    }
//}

