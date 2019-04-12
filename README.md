# AIFA Scraper
A class to scrape and extract data from AIFA (Agenzia Italiana del FArmaco) database using its public web service

## Use example

```
<?php

require 'aifa-scraper/src/Scraper.php';

$config = [
    // This is the base url
    'baseurl' => 'https://www.agenziafarmaco.gov.it/services/search/select',

    // Field list to retrieve during query. See below for description of interesting fields
    'fieldlist' => 'sm_field_descrizione_farmaco,sm_field_descrizione_atc,sm_field_descrizione_confezione,sm_field_descrizione_ditta',

    // Max records to retrieve
    'maxrows' => 1000
];

$aifaScraper = new \Devneri\Aifa\Scraper($config);

$drugResult = $aifaScraper->searchByName('eutirox', 'A');

...
```

## Fields

__Fields used in config and retrieved as result__<br/>
[result of Eutirox 75mcg tablets]

> __sm_field_aic__<br/>["024402051"]

> __sm_field_chiave_confezione__<br/>["024402051"]

> __sm_field_codice_atc__<br/>["H03AA01"]

> __sm_field_codice_confezione__<br/>["051"]

> __sm_field_codice_ditta__<br/>["2392"]

> __sm_field_codice_farmaco__<br/>["024402"]

> __sm_field_descrizione_atc__<br/>["Levotiroxina sodica"]

> __sm_field_descrizione_confezione__<br/>[""75 MICROGRAMMI COMPRESSE" 50 COMPRESSE"]

> __sm_field_descrizione_ditta__<br/>["MERCK SERONO S.P.A."]

> __sm_field_descrizione_farmaco__<br/>["EUTIROX"]

> __sm_field_link_fi__<br/>[["https://farmaci.agenziafarmaco.gov.it/aifa/servlet/PdfDownloadServlet?pdfFileName=footer_002392_024402_FI.pdf&amp;retry=0&amp;sys=m0b1l3"]]

> __sm_field_link_rcp__<br/>[[https://farmaci.agenziafarmaco.gov.it/aifa/servlet/PdfDownloadServlet?pdfFileName=footer_002392_024402_RCP.pdf&amp;retry=0&amp;sys=m0b1l3"]]

> __sm_field_stato_farmaco__<br/>["A"]

> __sm_field_tipo_procedura__<br/>["N"]