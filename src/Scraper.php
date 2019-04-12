<?php

namespace Devneri\Aifa;

/**
*  Devneri\Aifa\Scraper
*
*  A class to scrape and extract data from AIFA (Agenzia Italiana del FArmaco) database using its public web service
*
*  @author Filippo Neri <dev@filipponeri.net>
*/
class Scraper {

    /** @var array $config AIFA configuration */
    private $config = [];

    /**
    * Class constructor
    * 
    * Instantiate a new Scraping class based on $config
    *
    * @param array $config Parsed INI file
    */
    function __construct($config = []) {
        if (!isset($config['baseurl'])) {
            $config['baseurl'] = 'https://www.agenziafarmaco.gov.it/services/search/select';
        }

        if (!isset($config['fieldlist'])) {
            $config['fieldlist'] = '*';
        }

        if (!isset($config['maxrows'])) {
            $config['maxrows'] = 100;
        }

        $this->config = $config;
    }

    private function buildAifaUrl($queryParams, $fieldList = null, $format = 'json') {
        $url = $this->config['baseurl'] . '?q=%s&fl=%s&wt=%s&rows=%d';

        if (\is_array($queryParams)) {
            $queryParams = str_replace('=', ':', \http_build_query($queryParams, null, '+'));
        }

        $queryParams .= '+bundle:confezione_farmaco';

        if (\is_array($fieldList)) {
            $fieldList = implode(',', $fieldList);
        }
        else if (\is_null($fieldList)) {
            $fieldList = $this->config['fieldlist'];
        }

        return sprintf($url, $queryParams, $fieldList, $format, $this->config['maxrows']);
    }

    private function decodeHtmlEntities($rows) {
        $result = [];

        foreach ($rows as $row) {
            $row->sm_field_descrizione_confezione[0] = html_entity_decode($row->sm_field_descrizione_confezione[0]);
            $result[] = $row;
        }

        return $result;
    }

    private function query($url) {
        $result = json_decode(file_get_contents($url))->response->docs;
        $result = $this->decodeHtmlEntities($result);

        return $result;
    }

    /**
    * Search by ATC
    *
    * Search AIFA database by ATC code and return array of records found
    *
    * @param string $atc    ATC code (use of * wildcard is allowed)
    * @param string $state  Administrative state of drug - 'A' Authorized / 'R' Revoked / 'S' Suspended / '*' all
    */
    public function searchByAtc($atc, $state = '*') {
        $url = $this->buildAifaUrl([
            'sm_field_codice_atc' => $atc,
            'sm_field_stato_farmaco' => $state
        ]);

        return $this->query($url);
    }

    /**
    * Search by drug name
    *
    * Search AIFA database by drug name and return array of records found
    *
    * @param string $name   Drug name (use of * wildcard is allowed)
    * @param string $state  Administrative state of drug - 'A' Authorized / 'R' Revoked / 'S' Suspended / '*' all
    */
    public function searchByName($name, $state = '*') {
        $url = $this->buildAifaUrl([
            'sm_field_descrizione_farmaco' => $name,
            'sm_field_stato_farmaco' => $state
        ]);

        return $this->query($url);
    }

    /**
    * Search by drug active principle
    *
    * Search AIFA database by active principle and return array of records found
    *
    * @param string $principle  Active principle (use of * wildcard is allowed)
    * @param string $state      Administrative state of drug - 'A' Authorized / 'R' Revoked / 'S' Suspended / '*' all
    */
    public function searchByPrinciple($principle, $state = '*') {
        $url = $this->buildAifaUrl([
            'sm_field_descrizione_atc' => $principle,
            'sm_field_stato_farmaco' => $state
        ]);

        return $this->query($url);
    }
}