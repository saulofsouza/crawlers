<?php
ini_set('max_execution_time', 3 * 60 * 60);

for ($i = 2011; $i <= 2019; $i++) {
    if (($input = fopen('balance_' . $i . '.csv', 'r')) !== false) {
        $output = fopen('temporary.csv', 'w'); //open for writing 
        $ibge = '';
        while (($data = fgetcsv($input, 0, ";", ",")) !== false) {  //read each line as an array
            
            if (strpos($data[0], 'IBGE') !== false) {
                $ibge = $data[0];
            }

            if (sizeof($data) < 5 || $data[4] == 'SALDO') {
                echo 'Removed: ';
                var_dump($data);
                echo '</br>';
            } else {
                $data[5] = $ibge;
                fputcsv($output, $data);
            }
        }
        
        //close both files
        fclose( $input );
        fclose( $output );
    
        //clean up
        unlink('balance_' . $i . '.csv'); // Delete obsolete BD
        rename('temporary.csv', 'balance_' . $i . '.csv'); //Rename temporary to new
    }    
}

?>