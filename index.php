<?php
    $url = $_POST['url'];
    $response = null;
    $error = null;
    $elementCounts = array();

    if ($url && !preg_match('/^https?:\/\/.*/', $url)) {
        $error = 'Please enter a full URL including http:// or https:// at the beginning.';
    }

    if (!$error && $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);

        curl_close($ch);

        $dom = new DOMDocument();
        $dom->loadHTML($response, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); // prevent phantom tags
        $allElements = $dom->getElementsByTagName('*');
 
        foreach($allElements as $element) {
            if (array_key_exists($element->tagName, $elementCounts)) {
                $elementCounts[$element->tagName] += 1;
            } 
            else {
                $elementCounts[$element->tagName] = 1;
            }
        }

        ksort($elementCounts); // so there's consistent order
    }
    
    if (!$error && $response && !$elementCounts['html'])  { // so we know if we got at least semi-valid html
        $error = 'Sorry, the URL provided did not return HTML content.';
    }

?>

<!DOCTYPE html>
<html>
<head>
	<title> Hire Emily </title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/highlighter.js"></script>
	<script type="text/javascript" src="js/demo.js"></script>
	<link rel="stylesheet" type="text/css" media="all" href="css/demo.css">
</head>
<body>
	<form method="post">
		<input type="text" name="url" value="" placeholder="Enter URL Here">
	</form>

	<?php if ($response && !$error) { ?>
		<pre id="response">
		    <?php echo htmlentities($response); ?>
		</pre>
		<table>
			<thead>
				<tr><th>Element</th><th>Count</th></tr>
			</thead>
			<tbody>
			   <?php foreach ($elementCounts as $element => $count) { ?>
				 <tr><td><?php echo $element ?></td><td><?php echo $count ?></td></tr>
			   <?php } ?>
			</tbody>
		</table>
	<?php } ?> 
    <?php if ($error) { ?>
		<div class="error">
			<?php echo $error ?>
		</div>
	<?php }  ?>
</body>
</html>
