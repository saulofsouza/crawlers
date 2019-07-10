<?php

use Goutte\Client;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once __DIR__ . '/vendor/autoload.php';

ini_set('max_execution_time', 3 * 60 * 60);

$client = new Client();

$captcha = 'hello';

$states = [ 
    '11', '12', '13', '14', '15', '16', '17', '21', '22', '23', '24',
    '25', '26', '27', '28', '29', '31', '32', '33', '35', '41', '42', 
    '43', '50', '51', '52', '53' 
];

$crawler = makeRequest(
    $client, 
    'GET', 
    'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/index.php'
);

$crawler = makeRequest(
    $client, 
    'POST',
    'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/index.php',
    [
        'relatorio' => '153',
        'file' => 'entrada',
        'subtitulo' => 'Novo Relatório'
    ]
);

$spreadsheet = buildSheet();

foreach ($states as $state) {
    echo $state;
    $crawler_getting_cities = makeRequest(
        $client,
        'POST', 
        'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/js/municipiosBrasil/seleciona_municipios.php', 
        [
            'bras' => '1',
            'estado' => $state
        ]
    );
    
    $cities = $crawler_getting_cities
        ->filterXPath('//cidades/cidade/child::*')
        ->extract(['_text']);

    for ($i = 0; $i < sizeof($cities); $i +=2 ) {
        if ($cities[$i] == $state) { continue; }

        echo ' -- ' . $cities[$i] . ' : ' . $cities[$i+1] . '<br>';
        
        $crawler_get_city_info = makeRequest(
            $client,
            'POST',
            'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/relatorio.php',
            [
                'file' => 'RI',
                'rdbSelTipo' => 'estado',
                'localizaCidades' => $cities[$i+1],
                'cidades' => $cities[$i],
                'area_especial' => '0',
                'ct_captcha' => $captcha,
                'estado' => $state,
                'relatorio' => '153'            
            ] 
        );

        echo $client->getResponse();
        exit();

        $line = 2;

        estimativas($client, $cities[$i], $captcha, $spreadsheet, $line);
        cadastroUnico($client, $cities[$i], $captcha, $spreadsheet, $line);
        beneficios($client, $cities[$i], $captcha, $spreadsheet, $line);
        condicionalidades($client, $cities[$i], $captcha, $spreadsheet, $line);
        gestaoDescentralizada($client, $cities[$i], $captcha, $spreadsheet, $line);

        $line++;

        exit();
    }
}

//     fclose($file);  

function makeRequest($client, $method, $url, $params = []) {
 $crawler = null;

  while (!$crawler) {
    try {
        $crawler = $client->request($method, $url, $params);
    } catch (\Exception $e) {
        echo 'Falha ao conectar ao servidor. Tentando novamente em 3 segundos<br>';
        echo $e->getMessage() . '<br>';
        sleep(3);
    }      
  }
 
  return $crawler;
}

function estimativas($client, $ibge, $captcha, $spreadsheet, $line) {
    $est_crawler = makeRequest(
        $client,
        'GET',
        'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/conteudo_modulo.php?id=460&
        ibge=' . $ibge . '&area=0&ano=&mes=&
        ct_captcha=' . $captcha . '&ctidr=153'
    );

    echo ($client->getResponse());
    var_dump($client->getResponse()->getContent());

    $test = $est_crawler->filterXPath('/html/body/table[1]/tbody/tr[1]/td[2]/@value')->text();
    
    var_dump($test);

    exit();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A' . $line, $ibge);
    // $sheet->setCellValue('B' . $line, $est_crawler->filterXPath('/html/body/table/tbody/tr[1]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('C' . $line, $est_crawler->filterXPath('/html/body/table/tbody/tr[2]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('D' . $line, $est_crawler->filterXPath('/html/body/table/tbody/tr[1]/td[3]')->extract(['_text']));
    $writer = new Xlsx($spreadsheet);
    $writer->save('hello world.xlsx');
    exit();
}

function cadastroUnico($client, $ibge, $captcha, $spreadsheet, $line) {
    $cu_crawler = makeRequest(
        $client,
        'GET',
        'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/conteudo_modulo.php?id=587&
        ibge=' . $ibge . '&area=0&ano=&mes=&
        ct_captcha=' . $captcha . '&ctidr=153'
    );
    
    // echo $client->getResponse()->getContent();
    // $sheet = $spreadsheet->getActiveSjeet();
    // $sheet->setCellValue('E' . $line, $cu_crawler->filterXPath('/html/body/table[1]/tbody/tr[2]/td[2]/strong')->extract(['_text']));
    // $sheet->setCellValue('F' . $line, $cu_crawler->filterXPath('/html/body/table[1]/tbody/tr[3]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('G' . $line, $cu_crawler->filterXPath('/html/body/table[1]/tbody/tr[4]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('H' . $line, $cu_crawler->filterXPath('/html/body/table[1]/tbody/tr[5]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('I' . $line, $cu_crawler->filterXPath('/html/body/table[1]/tbody/tr[6]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('J' . $line, $cu_crawler->filterXPath('/html/body/table[1]/tbody/tr[2]/td[3]/strong')->extract(['_text']));
    // $sheet->setCellValue('K' . $line, $cu_crawler->filterXPath('/html/body/table[2]/tbody/tr[2]/td[2]/strong')->extract(['_text']));
    // $sheet->setCellValue('L' . $line, $cu_crawler->filterXPath('/html/body/table[2]/tbody/tr[3]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('M' . $line, $cu_crawler->filterXPath('/html/body/table[2]/tbody/tr[4]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('N' . $line, $cu_crawler->filterXPath('/html/body/table[2]/tbody/tr[5]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('O' . $line, $cu_crawler->filterXPath('/html/body/table[2]/tbody/tr[6]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('P' . $line, $cu_crawler->filterXPath('/html/body/table[2]/tbody/tr[2]/td[3]/strong')->extract(['_text']));
    // $sheet->setCellValue('Q' . $line, $cu_crawler->filterXPath('/html/body/table[3]/tbody/tr[2]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('R' . $line, $cu_crawler->filterXPath('/html/body/table[3]/tbody/tr[3]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('S' . $line, $cu_crawler->filterXPath('/html/body/table[3]/tbody/tr[4]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('T' . $line, $cu_crawler->filterXPath('/html/body/table[3]/tbody/tr[5]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('U' . $line, $cu_crawler->filterXPath('/html/body/table[3]/tbody/tr[2]/td[3]')->extract(['_text']));
}

function beneficios($client, $ibge, $captcha, $spreadsheet, $line) {
    $ben_crawler = makeRequest(
        $client,
        'GET',
        'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/conteudo_modulo.php?id=589&
        ibge=' . $ibge . '&area=0&ano=&mes=&
        ct_captcha=' . $captcha . '&ctidr=153'
    );

    // echo $client->getResponse()->getContent();
    // $sheet = $spreadsheet->getActiveSheet();
    // $sheet->setCellValue('V' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[1]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('W' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[2]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('X' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[4]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('Y' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[5]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('Z' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[6]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AA' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[7]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AB' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[8]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AC' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[9]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AD' . $line, $ben_crawler->filterXPath('/html/body/table/tbody/tr[4]/td[3]')->extract(['_text']));
}

function condicionalidades($client, $ibge, $captcha, $spreadsheet, $line) {
    $cond_crawler = makeRequest(
        $client,
        'GET',
        'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/conteudo_modulo.php?id=448&
        ibge=' . $ibge . '&area=0&ano=&mes=&
        ct_captcha=' . $captcha . '&ctidr=153'
    );

    // echo $client->getResponse()->getContent();
    // $sheet = $spreadsheet->getActiveSheet();
    // $sheet->setCellValue('AE' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[2]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AF' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[3]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AG' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[4]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AH' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[6]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AI' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[7]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AJ' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[8]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AK' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[9]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AL' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[10]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AM' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[11]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AN' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[12]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AO' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[13]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AP' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[14]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AQ' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[15]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AR' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[16]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AS' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[17]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AT' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[18]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AU' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[19]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AV' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[20]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AW' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[21]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AX' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[22]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AY' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[24]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('AZ' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[25]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BA' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[26]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BB' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[27]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BC' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[28]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BD' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[29]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BE' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[30]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BF' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[31]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BG' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[32]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BH' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[33]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BI' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[35]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BJ' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[36]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BK' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[37]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BL' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[38]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BM' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[40]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BN' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[41]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BO' . $line, cond_crawler->filterXPath('/html/body/table/tbody/tr[42]/td[2]')->extract(['_text']));

}

function gestaoDescentralizada($client, $ibge, $captcha, $spreadsheet, $line) {
    $gd_crawler = makeRequest(
        $client,
        'GET',
        'http://aplicacoes.mds.gov.br/sagi/RIv3/geral/conteudo_modulo.php?id=464&
        ibge=' . $ibge . '&area=0&ano=&mes=&
        ct_captcha=' . $captcha . '&ctidr=153'
    );

    // echo $client->getResponse()->getContent();
    // $sheet = $spreadsheet->getActiveSheet();
    // $sheet->setCellValue('BP' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[1]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BQ' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[2]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BR' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[3]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BS' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[4]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BT' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[5]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BU' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[6]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BV' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[7]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BW' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[8]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BX' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[9]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BY' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[10]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('BZ' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[11]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CA' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[12]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CB' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[13]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CC' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[14]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CD' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[15]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CE' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[16]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CF' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[17]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CG' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[18]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CH' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[19]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CI' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[20]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CJ' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[21]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CK' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[22]/td[2]')->extract(['_text']));
    // $sheet->setCellValue('CL' . $line, gd_crawler->filterXPath('/html/body/table/tbody/tr[23]/td[2]')->extract(['_text']));
    
}

function buildSheet() {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'IBGE');
    $sheet->setCellValue('B1', 'Estimativa de famílias de baixa renda – Perfil Cadastro Único (Censo 2010)');
    $sheet->setCellValue('C1', 'Estimativa de famílias pobres - Perfil Bolsa Família (CENSO 2010)');
    $sheet->setCellValue('D1', 'Estimativas - Mês de Referência');
    $sheet->setCellValue('E1', 'Total de famílias cadastradas');
    $sheet->setCellValue('F1', 'Famílias cadastradas com renda per capita mensal de R$ 0,00 até R$ 89,00');
    $sheet->setCellValue('G1', 'Famílias cadastradas com renda per capita mensal entre R$ 89,01 e R$ 178,00');
    $sheet->setCellValue('H1', 'Famílias cadastradas com renda per capita mensal entre R$ 178,01 e 1/2 salário mínimo');
    $sheet->setCellValue('I1', 'Famílias cadastradas com renda per capita mensal acima de 1/2 salário mínimo');
    $sheet->setCellValue('J1', 'Famílias cadastradas - Mês de Referência');
    $sheet->setCellValue('K1', 'Total de pessoas cadastradas');
    $sheet->setCellValue('L1', 'Pessoas cadastradas em famílias com renda per capita mensal de R$ 0,00 até R$ 89,00');
    $sheet->setCellValue('M1', 'Pessoas cadastradas em famílias com renda per capita mensal entre R$ 89,01 e 178,00');
    $sheet->setCellValue('N1', 'Pessoas cadastradas em famílias com renda per capita mensal entre R$ 178,01 e 1/2 salário mínimo');
    $sheet->setCellValue('O1', 'Pessoas cadastradas em famílias com renda per capita mensal acima de 1/2 salário mínimo');
    $sheet->setCellValue('P1', 'Pessoas cadastradas - Mês de Referência');
    $sheet->setCellValue('Q1', 'Total de Famílias com cadastro atualizado');
    $sheet->setCellValue('R1', 'Famílias com cadastro atualizado e renda per capita até 1/2 salário mínimo');
    $sheet->setCellValue('S1', 'Taxa de atualização do total de famílias cadastradas');
    $sheet->setCellValue('T1', 'Taxa de atualização cadastral até 1/2 salário mínimo');
    $sheet->setCellValue('U1', 'Atualização Cadastral - Mês de Referência');
    $sheet->setCellValue('V1', 'Quantidade de famílias beneficiárias do Programa Bolsa Família');
    $sheet->setCellValue('W1', 'Valor total de recursos financeiros pagos em benefícios às famílias (em Reais - R$)');
    $sheet->setCellValue('X1', 'Benefício Básico');
    $sheet->setCellValue('Y1', 'Benefícios Variáveis');
    $sheet->setCellValue('Z1', 'Benefício Variável Jovem - BVJ');
    $sheet->setCellValue('AA1', 'Benefício Variável Nutriz - BVN');
    $sheet->setCellValue('AB1', 'Benefício Variável Gestante - BVG');
    $sheet->setCellValue('AC1', 'Benefício de Superação da Extrema Pobreza - BSP');
    $sheet->setCellValue('AD1', 'Benefícios - Mês de Referência');
    $sheet->setCellValue('AE1', 'Total de beneficiários com perfil educação (6 a 15 anos)');
    $sheet->setCellValue('AF1', 'Total de beneficiários com perfil educação (16 e 17 anos)');
    $sheet->setCellValue('AG1', 'Quantidade de pessoas com perfil saúde (crianças até 7 anos e mulheres de 14 a 44 anos)');
    $sheet->setCellValue('AH1', 'Total de beneficiários acompanhados pela educação (6 a 15 anos)');
    $sheet->setCellValue('AI1', 'Total de beneficiários acompanhados pela educação (16 a 17 anos)');
    $sheet->setCellValue('AJ1', 'Total de beneficiários acompanhados com frequência acima da exigida (6 a 15 anos - 85%)');
    $sheet->setCellValue('AK1', 'Total de beneficiários acompanhados com frequência abaixo da exigida (6 a 15 anos- 85%)');
    $sheet->setCellValue('AL1', 'Total de beneficiários com frequência acima da exigida (16 a 17 anos - 75%)');
    $sheet->setCellValue('AM1', 'Total de beneficiários com frequência abaixo da exigida (16 a 17 anos - 75%)');
    $sheet->setCellValue('AN1', 'Total de beneficiários sem informação de frequência escolar (6 a 15 anos)');
    $sheet->setCellValue('AO1', 'Total de beneficiários sem informação de frequência escolar (16 a 17 anos)');
    $sheet->setCellValue('AP1', 'Quantidade de pessoas acompanhadas pela saúde');
    $sheet->setCellValue('AQ1', 'Total de mulheres acompanhadas');
    $sheet->setCellValue('AR1', 'Total de gestantes acompanhadas');
    $sheet->setCellValue('AS1', 'Total de gestantes com pré natal em dia');
    $sheet->setCellValue('AT1', 'Total de crianças acompanhadas');
    $sheet->setCellValue('AU1', 'Total de crianças com vacinação em dia');
    $sheet->setCellValue('AV1', 'Total de crianças com dados nutricionais');
    $sheet->setCellValue('AW1', 'Quantidade de pessoas com perfil saúde não acompanhadas nas condicionalidades de saúde');
    $sheet->setCellValue('AX1', 'Quantidade de pessoas sem informação nas condicionalidades de saúde');
    $sheet->setCellValue('AY1', 'Total de Efeitos por descumprimento das condicionalidades (PBF saúde e educação) (sem BVJ)');
    $sheet->setCellValue('AZ1', 'Total de advertências');
    $sheet->setCellValue('BA1', 'Total de bloqueios');
    $sheet->setCellValue('BB1', 'Total de suspensões');
    $sheet->setCellValue('BC1', 'Total de cancelamentos');
    $sheet->setCellValue('BD1', 'Total de Efeitos por descumprimento de condicionalidades (BVJ)(16 e 17 anos)');
    $sheet->setCellValue('BE1', 'Total de advertências');
    $sheet->setCellValue('BF1', 'Total de bloqueios');
    $sheet->setCellValue('BG1', 'Total de suspensões');
    $sheet->setCellValue('BH1', 'Total de cancelamentos');
    $sheet->setCellValue('BI1', 'Total de recursos cadastrados e avaliados');
    $sheet->setCellValue('BJ1', 'Total de famílias com recursos avaliados e deferidos');
    $sheet->setCellValue('BK1', 'Total de famílias com recursos avaliados e indeferidos');
    $sheet->setCellValue('BL1', 'Total de famílias com recursos não avaliados');
    $sheet->setCellValue('BM1', 'Total de famílias em fase de suspensão');
    $sheet->setCellValue('BN1', 'Total de famílias com registro de acompanhamento familiar no Sistema de Condicionalidades (SICON)');
    $sheet->setCellValue('BO1', 'Total de municípios que utilizam o acompanhamento familiar do Sistema de Condicionalidades (SICON)');
    $sheet->setCellValue('BP1', 'Crianças e adolescentes das famílias do PBF com frequência escolar informada');
    $sheet->setCellValue('BQ1', 'Total de crianças e adolescentes das famílias do PBF no município');
    $sheet->setCellValue('BR1', 'TAFE - Taxa de Acompanhameto de Frequência Escolar (item 1 / item 2)');
    $sheet->setCellValue('BS1', 'Famílias do PBF com condicionalidades de saúde informada');
    $sheet->setCellValue('BT1', 'Total de famílias com perfil saúde no município');
    $sheet->setCellValue('BU1', 'TAAS - Taxa de Acompanhamento de Agenda de Saúde (item 4 / item 5)');
    $sheet->setCellValue('BV1', 'Atualizações de cadastros - Perfil CadÚnico até 1/2 salário mínimo');
    $sheet->setCellValue('BW1', 'Cadastros de Famílias com Perfil CadÚnico até 1/2 salário mínimo');
    $sheet->setCellValue('BX1', 'TAC - Taxa de Atualização Cadastral (item 7 / item 8)');
    $sheet->setCellValue('BY1', 'Fator 1: Operação ((TAFE+TAAS) / 2 + TAC / 2)');
    $sheet->setCellValue('BZ1', 'Fator 2: Adesão ao SUAS');
    $sheet->setCellValue('CA1', 'Fator 3: Comprovação de Gastos pelo FMAS');
    $sheet->setCellValue('CB1', 'Fator 4: Aprovação da Comprovação de Gastos pelo CMAS');
    $sheet->setCellValue('CC1', 'IGD-M (Fator 1 x Fator 2 x Fator 3 x Fator 4)');
    $sheet->setCellValue('CD1', 'Estimativa total de famílias de baixa renda no município - perfil CadÚnico');
    $sheet->setCellValue('CE1', 'Quantidade de famílias consideradas para cálculo do repasse');
    $sheet->setCellValue('CF1', 'Valor Calculado sem Incentivos (item 14 x R$ 3,25 x item 16)');
    $sheet->setCellValue('CG1', 'Incetivo 1 - Proporção de famílias em fase de suspenção em acompanhamento Familiar');
    $sheet->setCellValue('CH1', 'Incetivo 2 - Dados da gestão municipal no SIGPBF atualizados há menos de 1 ano');
    $sheet->setCellValue('CI1', 'Valor Total de Incentivos (item 18 + item 19)');
    $sheet->setCellValue('CJ1', 'Valor Calculado com Incetivos (item 17 + item 20)');
    $sheet->setCellValue('CK1', 'Teto de repasse do IGD-M');
    $sheet->setCellValue('CL1', 'Valor repassado no mês');
    return $spreadsheet;
}





