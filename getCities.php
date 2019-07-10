<?php

use Goutte\Client;

require_once __DIR__ . '/vendor/autoload.php';

ini_set('max_execution_time', 3 * 60 * 60);

$client = new Client();

$crawler = makeRequest(
    $client, 
    'GET', 
    'http://aplicacoes.mds.gov.br/suaswebcons/restrito/execute.jsf?b=*dpotvmubsQbsdfmbtQbhbtNC&event=*fyjcjs'
);

$years = [ '2018' ];

$states = [ 'MA', 'AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MG',
    'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 
    'SC', 'SE', 'SP', 'TO' 
];

foreach ($years as $year) {
    $file = fopen('cities.csv', 'a');

    foreach ($states as $state) {
        $view_state = $crawler->filterXpath('//*[@id="javax.faces.ViewState"]/@value')->text();
        
        $params_getting_cities = [
            'AJAXREQUEST' => '_viewRoot',
            'form' => 'form',
            'form:ano' => $year,
            'form:uf' => $state,
            'form:agrupamento' => 'GRUPO',
            'form:esferaAdministrativa' => 'M',
            'form:referencia' => 'OB',
            'form:dataInicialInputCurrentDate' => '04/2019',
            'form:dataFinalInputCurrentDate' => '04/2019',
            'javax.faces.ViewState' => $view_state,
            'form:j_id98' => 'form:j_id98'
        ];

        $crawler_getting_cities = makeRequest(
            $client,
            'POST', 
            'http://aplicacoes.mds.gov.br/suaswebcons/publico/xhtml/consultarparcelaspagas/consultarParcelasPagas.jsf', 
            $params_getting_cities
        );
        
        $cities = $crawler_getting_cities
            ->filterXPath('//*[@id="form:municipio"]/option')
            ->extract(array('value','_text'));

        foreach($cities as $key => $c) {
            if ($cities[$key][0] == '' ) { continue; }
            array_push($cities[$key], $state);
            fputcsv($file, $cities[$key]);
        }
        
        sleep(1);

    }

    fclose($file);  
}

function makeRequest($client, $method, $url, $params = []) {
 $crawler = null;
  while (!$crawler) {
    try {
        $crawler = $client->request($method, $url, $params);
    } catch (\Exception $e) {
        echo 'Falha ao conectar ao servidor. Tentando novamente em 3 segundos\n';
        sleep(3);
    }      
  }
  return $crawler;
}





