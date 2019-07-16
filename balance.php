<?php

use Goutte\Client;

require_once __DIR__ . '/vendor/autoload.php';

ini_set('max_execution_time', 3 * 60 * 60);

$client = new Client();

$crawler = makeRequest(
    $client, 
    'GET', 
    'http://aplicacoes.mds.gov.br/suaswebcons/restrito/execute.jsf?b=*tbmepQbsdfmbtQbhbtNC&event=*fyjcjs'
);

$years = [ '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019' ];

$states = [ 
    'MA', 'AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MG', 'MS', 
    'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 'SC',
    'SE', 'SP', 'TO' 
];

foreach ($years as $year) {

    $file = fopen('balance_' . $year . '.csv', 'a');

    $view_state = $crawler
        ->filterXpath('//*[@id="javax.faces.ViewState"]/@value')
        ->text();

    $crawler_getting_months = makeRequest(
        $client,
        'POST',
        'http://aplicacoes.mds.gov.br/suaswebcons/publico/xhtml/saldoparcelaspagas/saldoParcelasPagas.jsf',
        [
            'AJAXREQUEST' => '_viewRoot',
            'form' => 'form',
            'form:ano' => $year,
            'javax.faces.ViewState' => $view_state,
            'form:j_id88' => 'form:j_id88'
        ]
    );

    $months = $crawler_getting_months
        ->filterXpath('//*[@id="form:mes"]/option')
        ->extract('value');

    foreach ($months as $month) {

        if ($month == '' ) { continue; }

        if (($handle = fopen("cities.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                ob_start();
                echo "$month/$year 
                    Cidade: $data[1]
                    Estado: $data[2]
                    IBGE: $data[0] \n ";
                ob_flush();
                ob_end_clean();
                
                $crawler_get_info = makeRequest(
                    $client,
                    'POST',
                    'http://aplicacoes.mds.gov.br/suaswebcons/publico/xhtml/saldoparcelaspagas/saldoParcelasPagas.jsf',
                    [
                        'AJAXREQUEST' => '_viewRoot',
                        'form' => 'form',
                        'form:ano' => $year,
                        'form:uf' => $data[2],
                        'form:mes' => $month,
                        'form:esferaAdministrativa' => 'M',
                        'javax.faces.ViewState' => $view_state,
                        'form:j_id94' => 'form:j_id94'
                    ]
                );

                $crawler_get_info = makeRequest(
                    $client,
                    'POST',
                    'http://aplicacoes.mds.gov.br/suaswebcons/publico/xhtml/saldoparcelaspagas/saldoParcelasPagas.jsf',
                    [
                        'AJAXREQUEST' => '_viewRoot',
                        'form' => 'form',
                        'form:ano' => '2018',
                        'form:uf' => $data[2],
                        'form:mes' => $month,
                        'form:municipio' => $data[0],
                        'form:esferaAdministrativa' => 'M',
                        'javax.faces.ViewState' => $view_state,
                        'form:pesquisar' => 'form:pesquisar'
                    ]
                );

                if($crawler_get_info->filterXpath('//*[@id="mensagens"]/div')->count()) {
                    echo $crawler_get_info->filterXpath('//*[@id="mensagens"]/div')->text();
                    echo "\n";
                } else {
                    $crawler_get_info = makeRequest(
                        $client,
                        'POST',
                        'http://aplicacoes.mds.gov.br/suaswebcons/publico/xhtml/saldoparcelaspagas/saldoParcelasPagas.jsf',
                        [
                            'form' => 'form',
                            'form:ano' => '2018',
                            'form:uf' => $data[2],
                            'form:mes' => $month,
                            'form:municipio' => $data[0],
                            'form:esferaAdministrativa' => 'M',
                            'javax.faces.ViewState' => $view_state,
                            'form:j_id268' => 'Gerar+Relatório+CSV'
                        ]
                    );

                    saveData($file, $client->getResponse()->getContent(), $month . '/' . $year);
                }
            }
            fclose($handle);
        }
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
        echo $e->getMessage() . "/n";
        sleep(0.4);
    }      
  }
  sleep(0.4);
  return $crawler;
}

function saveData($file, $content, $date) {
    $lines = explode(PHP_EOL, $content);
    foreach ($lines as $line) {
        $data = str_getcsv($line, ";");

        if (strpos($data[0], 'IBGE') !== false) {
            $ibge = str_replace('IBGE:', '', $data[0]);
            $ibge = substr($ibge, 0, 6);
        }

        if (sizeof($data) < 5 || $data[4] == 'SALDO') {
            echo 'Removed: ';  
            var_dump($data);
            echo "\n";
        } else {
            var_dump($data);
            array_pop($data);
            $data[5] = $ibge;
            $data[6] = $date;
            fputcsv($file, $data);
        }
    }
    exit();
}
