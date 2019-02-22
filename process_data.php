<?php

$d = $_POST['d'];//all live data

//$originalDate = $_POST['dateStart'];
//$inputDate = date("Ymd", strtotime($originalDate));

$time = strtotime($_POST['dateStart']);
if ($time) {
  $input_date = date('Ymd', $time);
  $input_date_display = date('d-m-Y', $time);
  //print  "Date::: $input_date<br/><br/>";
}




preg_match_all("/\{(.*?)\}/", $d, $result_array);


$coins = $_POST['coins'];
$sym = $_POST['sym'];

	
	$baseTableA = "base$input_date";
	
	$baseTableB = "base20181013";
	$baseADate = substr($baseTableA,4);
	$dateNow=date('Ymd');
	$timeDif = $dateNow-$baseADate;
	//print "$timeDif day(s)";

for ($q=0; $q<count($result_array[0]); $q++){
	$line_of_text = json_decode(stripslashes($result_array[0][$q]), true);
	
	if ($line_of_text['symbol']==$sym){
		$CurrentCoin=$line_of_text['name'];
		$CurrentCoinPrice=$line_of_text['current_price'];
		$l_marketCap=$line_of_text['market_cap'];
		$l_marketCapChange=$line_of_text['market_cap_change_24h'];
		$l_marketCapChangePerc=round($line_of_text['market_cap_change_percentage_24h'],2);
	}
	
	if ($line_of_text['symbol']=='btc'){
		$btc_CurrentCoin=$line_of_text['name'];
		$btc_CurrentCoinPrice=$line_of_text['current_price'];
		$btc_l_marketCap=$line_of_text['market_cap'];
		$btc_l_marketCapChange=$line_of_text['market_cap_change_24h'];
		$btc_l_marketCapChangePerc=round($line_of_text['market_cap_change_percentage_24h'],2);
	}
	
	
	
}

$db_found = mysqli_connect("localhost", "username", "password", "table");//info changed for security
				
				if($db_found === false) {
					// Handle error - notify administrator, log to a file, show an error screen, etc.
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}

			if ($db_found){
			
			$SQL = "SELECT * FROM $baseTableA WHERE symbol='$sym' ";
							$result = mysqli_query($db_found,$SQL);
							$db_field = mysqli_fetch_assoc($result);
							//$name = $db_field["name"];
							$historical_priceAA = $db_field["current_price"];
			
			mysqli_close($db_found);
			}




$currentCoinValue=$CurrentCoinPrice*$coins;
$currentReturn = round((($CurrentCoinPrice-$historical_priceAA)/$historical_priceAA)*100,2);
$currentReturnPL = round((($CurrentCoinPrice-$historical_priceAA)/$historical_priceAA),4);
$profitLoss = $currentCoinValue*$currentReturnPL;

print "

	
		<br/><br/><b>$CurrentCoin ($sym)</b>
		<br/>
		Current Value: $$currentCoinValue
		<br/>
		Investment Period: $timeDif day(s)
		<br/>
		Price at entry: $historical_priceAA
		<br/>
		Current price: $CurrentCoinPrice
		<br/>
		Return for Period: $currentReturn % ($$profitLoss)

	<div style='width:300px;  background-color:rgba(255,255,255,0.0);'>
		<br/><br/>
		
		<table>
			<tr>
				<td>
					<input type='text' id='o_p' oninput='calculateStops()' placeholder='opening position'/>
					<br/>
					-5% stop loss:
					<p id='stopA'></p>
					<br/>
					+10% stop loss:
					<p id='stopB'></p>
					<br/>
					+20% stop loss:
					<p id='stopC'></p>
				</td>
			</tr>
		</table>
	</div>
	
	<div>
		SPREAD Accumulation:
		<input type='text' id='c_p' oninput='calculateSpread()' placeholder='closing position'/>
		<p id='c_p_output'></p>
		
		<br/>
		<input type='text' id='ro_p' oninput='calculateSpread()' placeholder='reopening position'/>
		<p id='ro_p_output'></p>
		
		<br/>
		<input type='text' id='m_p' oninput='calculateSpread()' placeholder='multiply'/>
		<p id='m_p_output'></p>
		
	</div>

";

print "

	<br/><br/><br/>

		<table style='border-collapse: collapse;'>
			<colgroup>
				<col  style='width:180px;'>
				<col  style='width:180px;'>
				<col  style='width:180px;'>
				<col style='width:180px; background-color:#F9F8F6;'>
			</colgroup>
			<tr>
				<td/>
					SYMBOL:
				</td>
				<td/>
					NAME:
				</td>
				<td/>
					HISTORICAL PRICE ($input_date_display):
				</td>
				<td/>
					CURRENT PRICE:
				</td>
			</tr>

";

for ($q=0; $q<count($result_array[0]); $q++){		//This for loopo gets the information from all the coins in the table (of the selected date).
	$line_of_text = json_decode(stripslashes($result_array[0][$q]), true);
	
	$db_found = mysqli_connect("localhost", "username", "password", "tablename");//info changed for security
				
				if($db_found === false) {
					// Handle error - notify administrator, log to a file, show an error screen, etc.
					echo "Failed to connect to MySQL: " . mysqli_connect_error();
				}

			if ($db_found){
			
			//base20180928
			
							
				
				$l_symbol = $line_of_text['symbol'];
			
					$SQL = "SELECT * FROM $baseTableA WHERE symbol='$l_symbol' ";
							$result = mysqli_query($db_found,$SQL);
							$db_field = mysqli_fetch_assoc($result);
							$name = $db_field["name"];
							$historical_priceA = $db_field["current_price"];
							
							$SQL = "SELECT * FROM $baseTableA WHERE symbol='$sym' ";
							$result = mysqli_query($db_found,$SQL);
							$db_field = mysqli_fetch_assoc($result);
							$name_SYMA = $db_field["name"];
							$historical_priceA_SYM = $db_field["current_price"];
							
							
							
				$live_price = $line_of_text['current_price'];
				$PChangeA = round( ((($live_price-$historical_priceA)/$historical_priceA)*100),2);
					$PChangeA_SYM = round( ((($CurrentCoinPrice-$historical_priceA_SYM)/$historical_priceA_SYM)*100),2);
				
				
				if ($PChangeA>0){
					$PColorA='green';
				}
				else {
					$PColorA='red';
				}
				
				
				
				//$CoinChange=''
				
				
				
				
				
				$lA_H_CoinChange = $coins/($historical_priceA/$historical_priceA_SYM);
				$lA_Current_CoinChange = $coins/($live_price/$CurrentCoinPrice);
				
				$changelA= round(($lA_Current_CoinChange-$lA_H_CoinChange)/$lA_H_CoinChange*100,2);
				if ($changelA>0){
					$CColorA='green';
				}
				else {
					$CColorA='red';
				}
			
				
				//CoinChange
				$aCoinChange =  ($coins / ($live_price/$historical_priceA) ) ;
				$aaCoinChange = $aCoinChange + $aCoinChange*($PChangeA_SYM/100);//change PChangeA
				
				
				if ( ($PChangeA<-20) ){
					$highlight="style='background-color:yellow;'";
					$extraInfo ="
						
						<tr style='background-color:yellow;' >
											<td>
											</td>
											<td>
												Market Cap: 
											</td>
											<td>
												$l_marketCap
											</td>
											<td>
											</td>
											
						</tr>
						<tr style='background-color:yellow;' >
											
											<td>
											</td>
											<td>
												Market Cap Change (24 hrs)):
											</td>
											<td>
												$l_marketCapChange ($l_marketCapChangePerc%)
											</td>
											<td>
											</td>
										</tr>
						
					";
					
				}
				else {
					$highlight="";
					$extraInfo ="";
				}
					
				
				if ($historical_priceA!=0){
							//($name_SYMB -> $PChangeA_SYM : $aCoinChange-->}|{ $PChangeB_SYM --- )//-->
							print "
									
										<tr>
											<td $highlight >
												$l_symbol 
											</td>
											<td>
												$name
											</td>
											<td>
												$historical_priceA
											</td>
											<td>
												$live_price
											</td>
										</tr>
										
										<tr>
											<td>
											</td>
											<td>
												Price % Change:
											</td>
											<td>
												<p style='color:$PColorA'>$PChangeA%</p>
											</td>
											<td>
											</td>
										</tr>
										
										
										
										
										
										<tr>
											<td>
											</td>
											<td>
												<p><b>$l_symbol COIN CHANGE:</b></p>
											</td>
											<td>
												<p style='color:grey'>$lA_H_CoinChange</p>
											</td>
											<td>
												<p style='color:grey; cursor:pointer;' onclick='selectPrice(\"$l_symbol\",\"$live_price\",\"$btc_CurrentCoinPrice\")' ><b>$lA_Current_CoinChange</b></p>
											</td>
										</tr>
										
										<tr>
											<td>
											</td>
											<td>
												eff Coin % Change:
											</td>
											<td>
												<p style='color:$CColorA'>$changelA%</p>
											</td>
											<td>
											</td>
										</tr>
										
										
									<!--	
										<tr>
											<td>
											</td>
											<td>
												<p><b>Extra $sym value (Conversion)</b></p>
											</td>
											<td>
												<p style='color:lightblue'>$coins</p>
											</td>
											<td>
												<p style='color:lightblue'>$aaCoinChange $change_aa</p>
											</td>
										</tr>
									//-->	
										
										
										$extraInfo
										
										<tr height='25px'>
											<td>
											</td>
											<td>
											</td>
											<td>
											</td>
											<td>
											</td>
										</tr>
										
										
									
								
									";
							
							
				}
							
			
			mysqli_close($db_found);
			}
	
	

}

print "</table><br/><br/>";



?>