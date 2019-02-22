<?php

//This php form basically adds all the data of all the coins to a new table in the SQL database. The table heading is the date. 


$d = $_POST['d'];


preg_match_all("/\{(.*?)\}/", $d, $result_array);

$db_found = mysqli_connect("localhost", "user", "password", "table");//These values have been changed for security purposes
				
				if($db_found === false) {
					// Handle error - notify administrator, log to a file, show an error screen, etc.
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}

			if ($db_found){
				
				$date=date('Ymd');
				$tableName = "base$date";
				
				
				$SQLA = "SHOW TABLES LIKE '$tableName'" ;
				$resultA = mysqli_query($db_found,$SQLA);
				
				if (mysqli_num_rows($resultA) != 0 ){
							//table exists
				}
				else {

					for ($q=0; $q<count($result_array[0]); $q++){

						$line_of_text = json_decode(stripslashes($result_array[0][$q]), true);

						$SQL = "CREATE TABLE $tableName  LIKE baseTable" ;
						$result = mysqli_query($db_found,$SQL);
				
						$SQL = "INSERT INTO $tableName(ath, ath_change_percentage, ath_date, circulating_supply, current_price, high_24h, id, last_updated, low_24h, market_cap, market_cap_change_24h, market_cap_change_percentage_24h, market_cap_rank, name, price_change_percentage_24h, symbol, total_volume) 
							VALUES ('" . $line_of_text['ath'] . "','" . $line_of_text['ath_change_percentage'] . "','" . $line_of_text['ath_date'] . "','" . $line_of_text['circulating_supply'] . "','" . $line_of_text['current_price'] . "','" . $line_of_text['high_24h'] . "','" . $line_of_text['id'] . "','" . $line_of_text['last_updated'] . "','" . $line_of_text['low_24h'] . "','" . $line_of_text['market_cap'] . "','" . $line_of_text['market_cap_change_24h'] . "','" . $line_of_text['market_cap_change_percentage_24h'] . "','" . $line_of_text['market_cap_rank'] . "','" . $line_of_text['name'] . "','" . $line_of_text['price_change_percentage_24h'] . "','" . $line_of_text['symbol'] . "','" . $line_of_text['total_volume'] . "')";
						$result = mysqli_query($db_found,$SQL);
				
					}//for loop end


				}//if table exists end

			mysqli_close($db_found);
			}//database end

?>