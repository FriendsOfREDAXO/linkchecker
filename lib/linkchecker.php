<?php

class linkchecker {
    
    private $pages = [];
    private $links = [];
    private $depth = 0;
    private $baselink = '';
    private $maxdepth = 0;
    private $maxlinks = 0;
    private $base = '';             // wird pro zu spidernder Seite gemäß deren base href oder dem baselink gefüllt
    private $real_root = '';        // scheme + server aus der config
    private $links_checked = [];    // wird gefüllt mit den gecheckten Links und Ergebnissen
    private $pages_checked = [];    // wird gefüllt mit den Links der geprüften Seiten
    
    public function __construct() {
        $this->baselink = rex_config::get('linkchecker','baselink');
        $this->maxlinks = rex_config::get('linkchecker','maxlinks');
        $this->maxdepth = rex_config::get('linkchecker','depth');
        
        $parsed_root = parse_url($this->baselink);
        $this->real_root = $parsed_root['scheme'] . '://' . $parsed_root['host'] . '/';
        
    }
    
    
    public function run () {
//        error_reporting(0);
        $this->crawl_page($this->baselink);
        echo rex_view::info('Fertig');
        return;        
    }
    
    
    /**
     * Ruft den Inhalt der Seite ($plink) auf
     * @param type $plink = pagelink
     * return: links
     */
    private function crawl_page ($plink) {
        $pages_to_crawl = []; // wird gefüllt mit den von dieser Seite weiter zu crawlenden Links
        $this->pages_checked[$plink]['link'] = $plink;
        
        if ($this->depth >= $this->maxdepth) {
            $message = 'Seitentiefe überschritten!';
            $this->log_page($plink,$message);            
            return;
        }        
        
        $this->depth++;
        
        $this->log_page($plink);
        
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(file_get_contents($plink));
        
        $a_elements = $doc->getElementsByTagName('a');
        $base = $doc->getElementsByTagName('base');
        if (@is_object($base[0]->attributes['href']) && $base[0]->attributes['href']->value) {
            $this->base = trim($base[0]->attributes['href']->value, '/') . '/';
        } else {
            $this->base = $this->real_root;
        }
        foreach ($a_elements as $a) {
            if (count($this->links_checked) > $this->maxlinks) {
                $message = 'Maximale Linkanzahl überschritten';
                $this->log_page($plink,$message);
                return;
            }
            
            $alink = $a->getAttribute('href');
            $clink = $this->get_checklink($alink);
            
            if (!$clink) {
                $this->log_link($clink,$alink);
                continue;
            }
            
            $parsed_link = parse_url($clink);
            
            if (in_array($parsed_link['scheme'],['tel','mailto'])) {
                $this->log_link($clink,$alink,$parsed_link['scheme']);
                continue;
            }
            
            // links nicht 2x checken - aber mehrfach loggen
            if (isset($this->links_checked[$alink])) {
                extract($this->links_checked[$alink]['status']);
                $this->log_link($clink,$alink,$status_code);
                continue;
            }
            
            $status_code = false;
            
            if (isset($parsed_link['scheme']) && strpos($parsed_link['scheme'],'http') === 0) {
                $status_code = $this->get_status_code($clink);
            }
            
            // Nur spidern, wenn Link korrekt + noch nicht gespidert
            // nur Links von der eigenen Seite weiter verfolgen
            // unique per Page
            if ($status_code && $status_code < 400 && $this->check_page_link($clink)) {
                $pages_to_crawl[$clink] = $clink;
            }
            $this->log_link($clink,$alink,$status_code);
        }
        
        foreach ($pages_to_crawl as $l) {
            $this->crawl_page($l);
        }
        $this->depth--;
        
    }
    
    /**
     * 
     * @param type $clink - kann auch false sein - durch get_checklink geprüft
     * @param type $alink - ursprünglicher Link
     */
    private function log_link ($clink,$alink,$status_code = 'err') {
        $this->links_checked[$alink]['status'] = [
            'clink'=>$clink,
            'alink'=>$alink,
            'status_code'=>$status_code
        ];
        
        $fragment = new rex_fragment();
        $fragment->setVar('clink', $clink);
        $fragment->setVar('alink', $alink);
        $fragment->setVar('status_code', $status_code);
        echo $fragment->parse('loglink.php');
        /*
        while (@ob_get_status()) {
            @ob_end_flush();            
        }
         */
//        dump(ob_get_status());
 //       ob_end_flush();
//        while(@ob_end_flush());
//        flush();
    }
    
    /**
     * 
     * @param type $link
     */
    private function log_page ($link,$message = '') {
        echo '<h4>Aktuelle Seite: <a href="'.$link.'" target="_blank">'.$link.'</a></h4>';
        if ($message) {
            echo '<p>'.$message.'</p>';
        }
        echo '<p>Tiefe: '.$this->depth.'</p>';
    }
    
    
    private function get_status_code ($link) {
//        dump($link);
        $header = get_headers($link);
        if (!$header) {
            return false;
        }
        $statuscode = explode(' ',$header[0])[1];
        return $statuscode;
    }
    
    /**
     * Prüft den Link und passt ihn gemäß base href bzw. Startadresse für den Check an.
     * @param type $alink
     * @return boolean
     */
    private function get_checklink ($alink) {
        $p_link = parse_url($alink);
        if (!$p_link) {
            return false;
        }
        if (!isset($p_link['scheme'])) {
            return($this->base . ltrim($alink,'/'));
        }
        return $alink;
    }
    
    /**
     * prüft, ob der übergebene Link zur eigenen Seite gehört und gespidert werden soll
     * @param type $link
     */
    private function check_page_link ($link) {
        
        if (strpos($link,$this->real_root) === 0) {
            if (!isset($this->pages_checked[$link])) {
                return true;
            }
        }
        return false;
    }
    
    
}