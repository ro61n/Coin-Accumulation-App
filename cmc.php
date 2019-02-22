<?php
	session_start();
	
	$ip = $_SESSION["randomCode"];
	if ($ip == null){
		$_SESSION["randomCode"] = mt_rand();
		$ip = $_SESSION["randomCode"];
	}
	
?>
<html>
<head>
<title> Coin Accumulation App</title>
	
	<link rel="shortcut icon" href="Admin/favicon.ico" type="image/x-icon"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
<style>
	.loader {
		border: 16px solid #f3f3f3;
		border-radius: 50%;
		border-top: 16px solid #3498db;
		width: 120px;
		height: 120px;
		-webkit-animation: spin 2s linear infinite; /* Safari */
		animation: spin 2s linear infinite;
	}

	/* Safari */
		@-webkit-keyframes spin {
			0% { -webkit-transform: rotate(0deg); }
			100% { -webkit-transform: rotate(360deg); }
		}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}


	tr {
		height: 25px;
	}

</style>
	
	
</head>

<body onload='addBase()'>



<script>


function addBase(){

	var xmlhttp = new XMLHttpRequest();
	var url = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd"; // < --- EXPLANATION: Current API URL (The commented ones below were also previously used).

	//var url = "https://api.coinmarketcap.com/v2/listings/";
	//var url = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids=bitcoin%2Clitecoin%2Cethereum";
	//var url = "https://api.coinmarketcap.com/v2/ticker/";

	var d;
	
// < --- EXPLANATION: The XML ajax request below is to retrieve the coin data from coingecko.com.
	xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var myArr = JSON.parse(this.responseText);
			jcontent=JSON.parse(this.responseText);
				
			for (j = 0; j < jcontent.length; j++) {
				jcontent[j].roi = null;
			}
			
			jstring = JSON.stringify(jcontent);
				
			phpPart(jstring); // < --- EXPLANATION: The recieved data gets sent to phpPart function (below) to get processed by the php code.
				
	var currentDt = new Date();
    var mm = currentDt.getMonth() + 1;
    var dd = currentDt.getDate();
    var yyyy = currentDt.getFullYear();
    var dateNow = yyyy + '-' + mm + '-' + dd;  
				
				document.getElementById("menu").innerHTML = " <input type='text' id='coins' placeholder='coins'/> <input type='text' id='sym' placeholder='sym'/> <input type='date' min='2018-10-13' max='"+dateNow+"' id='dateStart'>[Date of Investment]   <button onclick='compute()' >Compute</button>   ";
				console.log(jcontent);
				console.log(jcontent.name[1]);
				d = JSON.parse(this.responseText);
				
    }
	};

	xmlhttp.open("GET", url, true);
	xmlhttp.send();

}




function phpPart(jstr){ // < --- EXPLANATION: The recieved data from the above ajax request. It gets sent to call_data.php which then stores the relevant info in a SQL table

	alsoToProcess=jstr;

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			//document.getElementById("demoB").innerHTML = this.responseText;
		}
	};
	xhttp.open("POST", "call_data.php", true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send("d="+jstr);

}

function compute(){// < --- EXPLANATION: When the compute button is pressed, this function is executed which sends the 3 input values to 'process_data.php'.
	var coins = document.getElementById('coins').value;
	var givenSym = document.getElementById('sym').value.toLowerCase();
	var dateStart = document.getElementById('dateStart').value;
	
	document.getElementById("ALL_DATA").innerHTML = " <div class='loader' style='margin-left:20px; margin-top:20px;'  ></div><p style='margin-left:20px; margin-top:20px;' > Calculating data for 3000 coins (Shouldn't take more than 3 mins)...</p>";
	
	
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("ALL_DATA").innerHTML = this.responseText;
			//document.getElementById("ALL_DATA").innerHTML = alsoToProcess;
		}
	};
  xhttp.open("POST", "process_data.php", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send("d="+alsoToProcess+'&coins='+coins+'&sym='+givenSym+'&dateStart='+dateStart);
	
}

function selectPrice(s,c,b){
	
	
	if (s!='btc'){
		var btcP = c/b;
	}
	else {
		var btcP = c;
	}
	
	
	document.getElementById('o_p').value = btcP;
	calculateStops();
}

function calculateStops(){//This function is executed by HTML code printed in the process_data.php page.
	var o_p = document.getElementById('o_p').value;
	var ansA = o_p*(1-0.05);
	var ansB = o_p*(1+0.10);
	var ansC = o_p*(1+0.20);
	
	document.getElementById("stopA").innerHTML = ansA;
	document.getElementById("stopB").innerHTML = ansB;
	document.getElementById("stopC").innerHTML = ansC;
	
}

function calculateSpread(){//This function is executed by HTML code printed in the process_data.php page.
	var coins = document.getElementById('coins').value;
	var o_p = document.getElementById('o_p').value;
	
	var c_p = document.getElementById('c_p').value;
		var ret_perc = ((((c_p/o_p)-1)*100)-0.2).toFixed(2);
	document.getElementById("c_p_output").innerHTML = "Return: "+ ret_perc + "%";
	
	var ro_p = document.getElementById('ro_p').value;
		var coin_NewAmount = (coins/(ro_p/c_p))*(1-0.002);
		var coin_NewAmount_perc = (((coin_NewAmount/coins)-1)*100).toFixed(2);
		var gain_lost = coin_NewAmount - coins;
	document.getElementById("ro_p_output").innerHTML = "New Amount: "+coin_NewAmount+ " coins Return: "+ coin_NewAmount_perc +"% ("+gain_lost+" coins)";
	
	var m_p = document.getElementById('m_p').value;
		var m_accumulation = gain_lost*m_p;
		var m_coin_NewAmount = +coins + +m_accumulation;
		var m_percReturn = (coin_NewAmount_perc*m_p).toFixed(2);
		
	document.getElementById("m_p_output").innerHTML = "New Amount: "+m_coin_NewAmount+ " coins Return: "+ m_percReturn +"% ("+m_accumulation+" coins)";
}




</script>


<!--<button onclick='addBase()' >Get New Base</button>
<input type='text' id='coins' placeholder='coins'/>
<input type='text' id='sym' placeholder='sym'/>
<button onclick='compute()' >Compute</button>
//-->

<div id='menu'>
	<div class='loader' style='margin-left:20px; margin-top:20px;'  ></div><p style='margin-left:20px; margin-top:20px;' >Loading Data...</p>
</div>
<div id='ALL_DATA'>

</div>






</body>
</html>