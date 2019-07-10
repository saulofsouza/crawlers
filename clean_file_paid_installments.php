<?php
ini_set('max_execution_time', 3 * 60 * 60);

$header = ["IBGE","Nome do Grupo","Piso","Prefeitura/Governo/Fundo","CNPJ","Parcela","Canal","Data da Ordem","Nº da Ordem Bancária","Agência/Conta","Valor Bruto","Valor Desconto","Valor Bloqueio/ Suspensão","Valor Líquido"];

$i = 2006;
for ($i; $i <= 2019; $i++) {
    if (($input = fopen('paid_installments_' . $i . '.csv', 'r')) !== false) {
        $output = fopen('temporary.csv', 'w'); //open for writing 
        // Add Header to the CSV file
        // fputcsv($output, $header);
        while (($data = fgetcsv($input)) !== false) {  //read each line as an array
            if (is_numeric($data[0])) {
                // Remove " from  the file
                foreach ($data as $v) {
                    var_dump($v);
                    exit();
                    echo "------------\n";                
                    $v = str_replace('\"', '', $v);
                }
                fputcsv($output, $data);
                exit();
            } else {
                echo 'Removed: \n';
                var_dump ($data);
            }
        }
    
        //close both files
        fclose( $input );
        fclose( $output );
    
        //clean up
        unlink('paid_installments_' . $i . '.csv'); // Delete obsolete BD
        rename('temporary.csv', 'paid_installments_' . $i . '.csv'); //Rename temporary to new
    }    
}

?>