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
    function __construct($config) {
        $this->config = $config;
    }

    private function buildAifaUrl($queryParams, $fieldList = null, $format = 'json', $maxRows = 100) {
        $url = $this->config['baseurl'] . '?q=%s&fl=%s&wt=%s&rows=%d';

        if (\is_array($queryParams)) {
            $queryParams = str_replace('=', ':', \http_build_query($queryParams, null, '+'));
        }

        if (\is_array($fieldList)) {
            $fieldList = implode(',', $fieldList);
        }
        else if (\is_null($fieldList)) {
            $fieldList = $this->config['fieldlist'];
        }

        return sprintf($url, $queryParams, $fieldList, $format, $maxRows);
    }

    private function decodeHtmlEntities($rows) {
        $result = [];

        foreach ($rows as $row) {
            $row->sm_field_descrizione_confezione[0] = html_entity_decode($row->sm_field_descrizione_confezione[0]);
            $result[] = $row;
        }

        return $result;
    }

    /**
    * Search by ATC
    *
    * Search AIFA database by ATC code and return array of records found
    *
    * @param string $atc ATC code
    */
    public function searchByAtc($atc, $state) {
        $url = $this->buildAifaUrl([
            'sm_field_codice_atc' => $atc . '*',
            'sm_field_stato_farmaco' => $state
        ]);

        $result = json_decode(file_get_contents($url))->response->docs;
        $result = $this->decodeHtmlEntities($result);

        return $result;
    }

    public function searchByName($name, $state) {
        $url = $this->buildAifaUrl([
            'sm_field_descrizione_farmaco' => $name . '*',
            'sm_field_stato_farmaco' => $state
        ]);

        $result = json_decode(file_get_contents($url))->response->docs;
        $result = $this->decodeHtmlEntities($result);

        return $result;
    }

    public function searchByPrinciple($principle, $state) {
        $url = $this->buildAifaUrl([
            'sm_field_descrizione_atc' => $principle . '*',
            'sm_field_stato_farmaco' => $state
        ]);

        $result = json_decode(file_get_contents($url))->response->docs;
        $result = $this->decodeHtmlEntities($result);

        return $result;
    }
}