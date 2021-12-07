<html>
	<head>
		<title></title>
		<link rel="stylesheet" href="zross/css/home.css" />
		<link rel="icon" type="image/x-icon" href="zross/favicon.ico">
		
		<?php
			require 'vendor/autoload.php';

			$settings = array('host' => '', 'port' => 8080);
			$elasticaClient = new \Elastica\Client(); //use settings if needed
		?>
	</head>
	<body>
        <div id="top">
            <button><a href="login.php">Register/Login</a></button>
            <form action="search.php">
                <button>Search</button>
                <input type="text" id="search" name="search">
            </form>
        </div>
		<div id="left">
			<?php
				$url = "https://www.cnn.com/2017/08/09/politics/us-cuba-acoustic-attack-embassy/index.html";
				$ch = curl_init();
				$timeout = 5;
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$html = curl_exec($ch);
				curl_close($ch);
			
				$doc = new DOMDocument();
				libxml_use_internal_errors(true);
				$doc->loadHTML($html);
				$xpath = new DOMXpath($doc);
				$class = 'single-body';
				$content = $xpath->query("//div[@class='article__content']/child::*");
				#echo($text->ownerDocument->saveHTML($text));
				#$puretext = null;
				foreach($content as $element)
				{
					echo($element->ownerDocument->saveHTML($element));
					#$puretext = $puretext . $element->nodeValue;
					#echo $element->nodeValue;
				}
			?>
		</div>
		<div id="right">
            <div class="table">
                <p><a href="#tab1">Credibility</a></p>
                <p><a href="#tab2">Snopes</a></p>
                <p><a href="#tab3">Survey</a></p>
            </div>
            <div id="tab1" class="tab-content">
            <?php
				$metadata =
				'[
					[
					"title": "Second-order topology and multidimensional topological transitions in sonic crystals",
					"author(s)": ["Xiujiuan Zhang", "Hai-Xiao Wang", "Zhi-Kang Lin", "Y. Tian", "Biye Xie", "M. Lu", "Yan-Feng Chen", "J. Jiang"],
					"year": "2019",
					"venue": "Nature Physics"
					],

					[
					"title": "Cyclopia and defective axial patterning in mice lacking Sonic hedgehog gene function",
					"author(s)": ["C. Chiang", "Y. Litingtung", "Eric Lee", "K. E. Young", "Jeffrey L Corden", "H. Westphal", "P. Beachy"],
					"year": "1996",
					"venue": "Nature"
					],

					[
					"title": "Observation of topological valley transport of sound in sonic crystals",
					"author(s)": ["Jiuyang Lu", "Chunyin Qiu", "Liping Ye", "Xiying Fan", "M. Ke", "F. Zhang", "Zhengyou Liu"],
					"year": "2017",
					"venue": "N/A"
					],

					[
					"title": "Targeting the Sonic Hedgehog Signaling Pathway: Review of Smoothened and GLI Inhibitors"
					"author(s)": ["Tadas K. Rimkus", "R. Carpenter", "Shadi A Qasem", "M. Chan", "H. Lo"],
					"year": "2016",
					"venue": "Cancers"
					],

					[
					"title": "A highlight on Sonic hedgehog pathway",
					"author(s)": ["G. Carballo", "J. Honorato", "Giselle Pinto Farias de Lopes", "T. C. Spohr"],
					"year": "2018",
					"venue": "Cell Communication and Signaling"
					],

					[
					"title": "Sonic hedgehog mediates the polarizing activity of the ZPA",
					"author(s)": ["R. D. Riddle", "Randy L. Johnson", "E. Laufer", "C. Tabin"],
					"year": "1993",
					"venue": "Cell"
					],

					[
					"title": "Sonic hedgehog, a member of a family of putative signaling molecules, is implicated in the regulation of CNS polarity",
					"author(s)": ["Y. Echelard", "D. Epstein", "B. St.-Jacques", "Liya Shen", "J. Mohler", "J. McMahon", "A. McMahon"],
					"year": "1993",
					"venue": "Cell"
					],

					[
					"title": "Valley vortex states in sonic crystals",
					"author(s)": ["Jiuyang Lu", "Chunyin Qiu", "M. Ke", "Zhengyou Liu"],
					"year": "2016",
					"venue": "2016 Progress in Electromagnetic Research Symposium (PIERS)"
					],

					[
					"title": "Locally Resonant Sonic Materials",
					"author(s)": ["Zhengyou Liu", "Xixiang Zhang", "Y. Mao", "Yongyuan Zhu", "Z. Yang", "C. Chan", "P. Sheng"],
					"year": "2000",
					"venue": "N/A"
					],

					[
					"title": "The Sonic Color Line: Race and the Cultural Politics of Listening",
					"author(s)": ["J. Stoever"],
					"year": "2016",
					"venue": "N/A"
					]
				]';
				#$metadata = json_decode($metadata);
				echo "
				<button type='button' class='collapsible'>Show Metadata</button>
				<p class='content'>$metadata</p>
				";
				
				#foreach($metadata as $entry)
				{
					#echo "<p>$entry</p>";	
				}
			?>
            </div>
            <div id="tab2" class="tab-content">
            	<?php
				#use Textrank;
				use \crodas\TextRank\Config;
				use \crodas\TextRank\TextRank;
				//these need to be installed using composer???
				
				require 'vendor/autoload.php';

				$url = "https://www.snopes.com/fact-check/do-sonic-weapons-explain-the-health-diplomats-cuba/";
				$ch = curl_init();
				$timeout = 5;
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$html = curl_exec($ch);
				curl_close($ch);

				$doc = new DOMDocument();
				libxml_use_internal_errors(true);
				$doc->loadHTML($html);
				$xpath = new DOMXpath($doc);
				$class = 'single-body';
				$content = $xpath->query("//main[@role='main']/article/child::*");
				$text = array($content->item(1), $content->item(3), $content->item(4), $content->item(6), $content->item(7));
				#echo($text->ownerDocument->saveHTML($text));
				#$puretext = null;
				foreach($text as $element)
				{
					echo($element->ownerDocument->saveHTML($element));
					#$puretext = $puretext . $element->nodeValue;
					#echo $element->nodeValue;
				}

				/*$api = new TextRankFacade();
				$stopwords = new English();
				$api->setStopWords($stopwords);

				$result = $api->getOnlyKeywords($text);
				$keywords = array_slice($result, 0, 5);
				
				$config = new Config;
				$textrank = new TextRank($config);
				
				echo $puretext;
				$keywords = $textrank->getKeywords($puretext); //grab text from article and feed it to function
				vardump($keywords);*/

				$keywords = array("sonic", "disease", "dizzy", "drug", "sound");
				$url = 'http://api.semanticscholar.org/graph/v1/paper/search?query=';
				foreach($keywords as $item)
				{
					$url = $url . $item . '+';
				}
				//shave off last plus here
				$url = rtrim($url, "+");
				echo $url;

				$json = file_get_contents($url);
				$obj = json_decode($json);
				var_dump($obj); //YATTAA!!!!

				//from here, grab paper IDs then use the following url to grab metadata
				#$metaurl = 'http://api.semanticscholar.org/v1/paper/' . ID . '?fields=';
				//title
				//author(s):name (up to 500)
				//year
				//venue
				?>
            </div>
            <div id="tab3" class="tab-content">
            This is where we survey you.
            </div>
		</div>
		<footer>Powered by Apache2, MySQL, and PHP 8.</footer>
	</body>
	<script>
		var coll = document.getElementsByClassName("collapsible");
		var i;

		for (i = 0; i < coll.length; i++) {
		  coll[i].addEventListener("click", function() {
			this.classList.toggle("active");
			var content = this.nextElementSibling;
			if (content.style.display === "block") {
			  content.style.display = "none";
			} else {
			  content.style.display = "block";
			}
		  });
		}
		</script>
</html>
