<?php
interface ParserFactoryInterface {
    public function create(string $type): ParserInterface;
}

class ParserFactory implements ParserFactoryInterface {
    public function create(string $type): ParserInterface {
        switch ($type) {
            case 'vnexpress':
                return new VnexpressParser();
            case 'dantri':
                return new DantriParser();
            case 'vietnamnet':
                return new VietnamnetParser();
            default:
                throw new Exception('Invalid parser type');
        }
    }
}
interface ParserInterface {
    public function parse(): ?array;
}

class Parser implements ParserInterface {
    protected $url;

    public function __construct($url) {
        $this->url = $url;
    }

    public function getHtml(): string {
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
        return strip_tags( implode(array_map([$node->ownerDocument, "saveHTML"],
            iterator_to_array($node->childNodes))));

    }

    public function parse(): ?array {
        throw new Exception('Not implemented');
    }
}

class VnexpressParser extends Parser {
    const title_Vnexpress = 'title-detail';
    // get class content from vnexpress
    const content_Vnexpress = 'fck_detail';
    // get class date
    const date_Vnexpress = 'date';
    /**
     * @inheritDoc
     */
    public function parse(): ?array {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass(self::title_Vnexpress);
            $content = $this->getElementsByClass(self::content_Vnexpress);
            $date = $this->getElementsByClass(self::date_Vnexpress);
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }

}

class DantriParser extends Parser {

    // get class title  name from dan tri
    const  title_Dantri = 'title-page detail';
    // get class content from dan tri
    const  content_Dantri = 'fck_detail';
    // get class date from dan tri
    const  date_Dantri = 'date';
    /**
     * @inheritDoc
     */

    public function parse(): ?array {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass(self::title_Dantri);
            $content = $this->getElementsByClass(self::content_Dantri);
            $date = $this->getElementsByClass(self::date_Dantri);
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}

class VietnamnetParser extends Parser {
    // get class title  name from vietnamnet
    const title_Vietnamnet = 'content-detail-title';
    // get class content from vietnamnet
    const content_Vietnamnet = 'maincontent main-content';
    // get class date from vietnamnet
    const date_Vietnamnet = 'bread-crumb-detail__time';
    /**
     * @inheritDoc
     */
    public function parse(): ?array {
        $html = $this->getHtml();
        if (!empty($html)) {
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            $title = $this->getElementsByClass(self::title_Vietnamnet);
            $content = $this->getElementsByClass(self::content_Vietnamnet);
            $date = $this->getElementsByClass(self::date_Vietnamnet);
            return ['title' => $title, 'content' => $content, 'date' => $date];
        }
        return null;
    }
}
class Database {
    private static $instance;

    private function __construct() {}

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new Database();
        }

        return self::$instance;
    }
}
//$factory = new ParserFactory();
//$parser = $factory->create('vnexpress');
//$db = new DatabaseConnection('localhost', 'toanlt', 'Toanlt123', 'Parser');
//$url = 'https://dantri.com.vn/giao-duc-huong-nghiep/ha-noi-ca-covid-19-tang-1535-hoc-sinh-mot-lop-12-nghi-vi-om-sot-20230413101238282.htm';
////$vnexpress_parser = new VnexpressParser($url);
////$VietnamnetParser = new VietnamnetParser($url);
//$DantriParser = new DantriParser($url);
////$data = $vnexpress_parser->parse();
////$data = $VietnamnetParser->parse();
//$result = $parser->parse();
//
////CHECKING OBJECT CREATED OR NOT
//$db1 = Database::getInstance();
//$db2 = Database::getInstance();
