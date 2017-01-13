<?PHP

/**
 * @link https://github.com/OpenACalendar/OpenACalendar-Twitter
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) JMB Technology Limited, http://jmbtechnology.co.uk/
 */

require __DIR__ . DIRECTORY_SEPARATOR. 'vendor' . DIRECTORY_SEPARATOR. 'autoload.php';
require __DIR__ . DIRECTORY_SEPARATOR. 'libs.php';

// Get config
$configFile = $argv[1];
if (!$configFile  || !file_exists($configFile)) {
    die("You must provide a config file!\n");
}
// load config file, and check
$config = parse_ini_file($configFile);
foreach(array('site_url','twitter_app_key','twitter_app_secret','twitter_user_key','twitter_user_secret') as $var) {
    if (!isset($config[$var]) || !$config[$var]) {
        die("Missing config variable: ".$var."\n");
    }
}


// Get JSON URL
$url = $config['site_url'];
if (substr($url, -1) != '/') {
    $url .= '/';
}
$url .= 'api1';
if (isset($config['area_slug']) && $config['area_slug']) {
    $url .= '/area/'.$config['area_slug'];
}
$url .= "/events.json";

// Get the Data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'OpenACalendar Twitter');
$dataString = curl_exec($ch);
$response = curl_getinfo( $ch );
curl_close($ch);
if ($response['http_code'] != 200) {
    die("Not a 200 response from ".$url."\n");
}
$data = json_decode($dataString);


// which events to include?
$today = new DateTime("",new DateTimeZone("UTC"));
$today->setTime(0,0,0);
$todayStarts = $today->getTimestamp();
$today->setTime(23,59,59);
$todayEnds = $today->getTimestamp();
$events = array();
foreach($data->data as $event) {
    if (!$event->deleted && !$event->cancelled && $event->start->timestamp >= $todayStarts && $event->start->timestamp <= $todayEnds) {
        $events[] = $event;
    }
}

// Let's go!

$connection = new \Abraham\TwitterOAuth\TwitterOAuth($config['twitter_app_key'], $config['twitter_app_secret'], $config['twitter_user_key'], $config['twitter_user_secret']);

foreach($events as $event) {
    $tweetContent = getTweetContent($config['prefix'], $event->summaryDisplay, $event->siteurl);
    print $tweetContent;
    $statues = $connection->post("statuses/update", ["status" => $tweetContent]);
    if (property_exists($statues, 'errors') && $statues->errors) {
        print "ERROR\n";
        var_dump($statues);
        die();
    }
    if (intval($config['seconds_between_tweets']) > 0) {
        sleep( $config['seconds_between_tweets'] );
    }
}
