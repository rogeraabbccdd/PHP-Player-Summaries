<?php
$mysql_ip = "";
$mysql_db = "";
$mysql_user = "";
$mysql_pass = "";
$player_table = "";
$SteamAPI_Key = "";

$link = mysqli_connect($mysql_ip, $mysql_user, $mysql_pass, $mysql_db);
?>

<table>
	<thead>
		<tr>
			<th>Steam64</th>
			<th>Name</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Steam64</th>
			<th>Name</th>
		</tr>
	</tfoot>
	<tbody>
<?php
$member_result=mysqli_query($link, "SELECT steam_id_64 FROM ".$player_table.""); 
mysqli_set_charset ($link , "utf-8");
$member_numb=mysqli_num_rows($member_result); 
if (!empty($member_numb)) 
{
	$i=1;
	while ($member_row = mysqli_fetch_array($member_result))
	{
		// get steam id and name
		$steam[$i] = $member_row['steam_id_64'];
		$i++;
	}
	
	$i-=1;
	
	$query_count=ceil($i/100);
	
	// $i = 資料筆數
	// $query_count = api總共要查詢的次數
	// $k=已查詢的次數
	// $j = api查詢次數loop 
	// $l = api查詢url的steamid筆數
	for ($j=1;$j<=$query_count;$j++) 
	{
		$url[$j] = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$SteamAPI_Key."&steamids=";
		
		// 如果只要查一次
		if($query_count==1)
		{
			for ($l=1;$l<=$i;$l++) 
			{
				if($l == 1)	$url[$j].="".$steam[$l]."";
				else $url[$j].=",".$steam[$l]."";
			}
		}
		// 如果多於一次
		else
		{
			// 第一次查詢
			if($j == 1)
			{
				for($l=1;$l<=100;$l++) 
				{
					if($l == 1)	$url[$j].="".$steam[$l]."";
					else $url[$j].=",".$steam[$l]."";
				}
			}
			// 最後一次查詢
			elseif($j == $query_count)
			{
				$k= 1;
				for($l=($j*100)-99;$l<=$i;$l++) 
				{
					if($k == 1)	$url[$j].="".$steam[$l]."";
					else $url[$j].=",".$steam[$l]."";
					$k++;
				}
			}
			// 中間查詢
			else
			{ 
				$k=1;
				for($l=($j*100)-99;$l<=($j*200);$l++) 
				{
					if($k == 1)	$url[$j].="".$steam[$l]."";
					else $url[$j].=",".$steam[$l]."";
					$k++;
				}
			}
		}
		
		$cURL = curl_init();
		curl_setopt($cURL, CURLOPT_URL, $url[$j]);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($cURL);
		curl_close($cURL);
		$json = json_decode($result, true);
		
		foreach($json['response']['players'] as $item)
		{
			$personname[$item['steamid']] = $item['personaname'];
			//$avatarfull[$item['steamid']] = $item['avatarfull'];
		}
	}
	
	for ($j=1;$j<=$i;$j++) 
	{
		echo "
		<tr height='40px'>
			<td>".$steam[$j]."</td>
			<td>".$personname[$steam[$j]]."</td>
		</tr>";
	}
}
?>
	</tbody>
</table>