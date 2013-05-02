<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Google Adwords Tester | Results</title>
    </head>
    <body>
<?php

// Format input text
$inputTextArray = textToArray($_POST['inputtext']);

$search_string = 'google.com';

set_time_limit(0);

class wSpider {

    var $ch; /// going to used to hold our cURL instance
    var $html; /// used to hold resultant html data
    var $binary; /// used for binary transfers
    var $url; /// used to hold the url to be downloaded

    function wSpider() {
        $this->html = "";
        $this->binary = 0;
        $this->url = "";
    }

    function fetchPage($url) {
        $this->url = $url;
        if (isset($this->url)) {
            $this->ch = curl_init(); /// open a cURL instance
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); // tell cURL to return the data
            curl_setopt($this->ch, CURLOPT_URL, $this->url); /// set the URL to download
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true); /// Follow any redirects
            curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, $this->binary); /// tells cURL if the data is binary data or not
            $this->html = curl_exec($this->ch); // pulls the webpage from the internet
            curl_close($this->ch); /// closes the connection
        }
    }

}

?>
<table>
<tr>
<th>Search term</th>
<th>Top</th>
<th>Right</th>
</tr>
<?php

foreach ($inputTextArray as $search_term) {
	
	$page = "http://www.google.com/search?q=" . urlencode(trim($search_term));
	
	// Ignore blank lines
    if ($search_term == '') {
        continue;
	}
	
	$mySpider = new wSpider(); //// creates a new instance of the wSpider
	$mySpider->fetchPage($page); /// fetches the page
	#echo $mySpider->html; /// DEBUG: prints out the html
	$html = $mySpider->html;

	$tads = get_between('<div id="center_col"><div style="margin', '</div>', $html);
	$rhs = get_between('<div id="rhs_block"', '</div>', $html);
	#print_r($rhs); /// DEBUG: prints out the array
	
	// Count number of occurences
	$tadsNum = substr_count($tads[0], $search_string)/2; // Divided by 2 because they come in multiples of twos e.g. <a href='apps.google.com'>Apps.google.com</a>
	if (empty($rhs)) { $rhsNum = 'missing'; } // Sometimes rhs ads don't show up
	else { $rhsNum = substr_count($rhs[0], $search_string)/2; }
	
	echo "<tr><td><a href='$page' target='_blank'>" . $search_term . "</a></td><td>";
	echo ($tadsNum == '0' ? '0' : "<font color='red'>".$tadsNum."</font>");
	echo "</td><td>";
	echo ($rhsNum == '0' ? '0' : "<font color='red'>".$rhsNum."</font>"); 
	echo "</td>";
}


// FUNCTIONS

function textToArray($inputText) {
    $inputTextReplaced = str_replace(chr(13) . chr(10), chr(10), $inputText);
    $inputTextReplaced = str_replace(chr(13), chr(10), $inputText);
    $inputTextArray = explode(chr(10), $inputTextReplaced);
    return $inputTextArray;
}

function get_between($beg_tag, $close_tag, $input) {
    preg_match_all("($beg_tag.*$close_tag)siU", $input, $matching_data);
    return $matching_data[0];
}

?>
</table>
</body>
</html>