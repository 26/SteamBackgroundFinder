<?php

/*

	Steam Background Finder
    Copyright (C) 2018 Xxmarijnw

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published
    by the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
*/

if(!isset($_POST)) 
{
	exit();
}
else 
{ 
	$link = basename($_POST['url']);
}

if(empty($_POST['url']))
{
	echo 'Oops! You forgot to enter something.';
	exit();
}

$link = ((strlen($link) === 17) && (is_numeric($link)) && (substr( $link, 0, 7 ) === '7656119' )) ? 'https://steamcommunity.com/profiles/'.$link : 'https://steamcommunity.com/id/'.$link;

$profile = file_get_contents($link);
	
preg_match('/<div class="error_ctn">/', $profile, $profile_exists);
if($profile_exists)
{
	echo 'Oops! This profile was not found.';
	exit();
}
	
preg_match('/<div class="profile_private_info">/', $profile, $profile_private);
if($profile_private)
{
	echo 'Oops! This profile is set to private.';
	exit();
}
	
preg_match('/(?<=background-image: url\(https:\/\/)(.+)(?=\);")/', $profile, $background);
if(empty($background))
{
	echo 'Oops! This profile has no background.';
	exit();
}

$background = 'https://'.$background[0];
$segments   = explode('/', parse_url($background, PHP_URL_PATH));
$appid      = $segments[5];

$steam_card_exchange = 'https://www.steamcardexchange.net/index.php?gamepage-appid-'.$appid;
$steamdesign         = 'https://steam.design/#'.$background;
$market              = 'https://steamcommunity.com/market/search?q=&category_753_item_class[]=tag_item_class_3&appid=753&category_753_Game[]=tag_app_'.$appid;

$results = json_decode(file_get_contents('https://www.googleapis.com/customsearch/v1?key=&cx=006499585084668756177:3iopj7jzexa&siteSearch=https://steamcommunity.com&q='.$background), true);
$count   = $results['queries']['request'][0]['count'];
			
$price = '';

if(!(array_key_exists('error', $results)) || ($count != '0')) 
{
	$check = $results['items'][0]['formattedUrl'];
	
	if(strpos($check, $appid))
	{
		$market           = $results['items'][0]['link'];
		$segments         = explode('/', parse_url($market, PHP_URL_PATH));
		$market_hash_name = $segments[4];
		$price_overview   = json_decode(file_get_contents("https://steamcommunity.com/market/priceoverview/?appid=753&currency=1&market_hash_name=".$market_hash_name), true);
		
		if(($price_overview['success'] === true) && ($price_overview['lowest_price'] !== null)) $price = ' ('.$price_overview['lowest_price'].')';
	}
}

$app_details = json_decode(file_get_contents('https://store.steampowered.com/api/appdetails?appids='.$appid), true);
$app_name    = $app_details[$appid]['data']['name'];

$app_name = ($app_name !== null) ? 'This background is from '.$app_name : '';

echo json_encode(array('background'=>$background, 'steam_card_exchange'=>$steam_card_exchange, 'market'=>$market, 'app_name'=>$app_name, 'steamdesign'=>$steamdesign, 'price'=>$price));

?>