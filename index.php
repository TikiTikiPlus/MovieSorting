<?php
	ini_set("error_reporting",E_ALL);
	ini_set("log_errors","1");
	ini_set("error_log","../php_errors.txt");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200&display=swap" rel="stylesheet">
    <script type="text/javascript" src="../js/outputSummary.js"></script>
  </head>
  <!--<body onload="onLoad();">-->
  <body>
    <?php
      if(isset($_COOKIE["search"])){$search = $_COOKIE["search"];} else {$search = "";} 
      if(isset($_COOKIE["sortBy"])){$sortBy = $_COOKIE["sortBy"];} else {$sortBy = "";} 
      if(isset($_COOKIE["sortOrder"])){$sortOrder = $_COOKIE["sortOrder"];} else {$sortOrder = "";} 
      
      // Check if the xml catalog exists and load the data
      if (file_exists("xml/catalog.xml")) {
        $movies = simplexml_load_file("xml/catalog.xml");
      } else {
        exit("Failed to open catalog.xml.");
      }

      // Duplicate the data into an array to be used for sorting
      $sorted = array();

      foreach($movies->children() as $movie) {
        if (strpos(strtolower($movie->title), strtolower($search)) !== false || 
            strpos(strtolower($movie->genre), strtolower($search)) !== false ||
            strpos(strtolower($movie->year), strtolower($search)) !== false || 
            strpos(strtolower($movie->director), strtolower($search)) !== false || $search == "") 
        {
          $sorted[] = $movie;
        }
      }

      //Sort the array
      if(!isset($_COOKIE["sortBy"])){
        usort($sorted, function($a, $b) {
          return $a->year - $b->year;
        });
      }
      else{
        if($_COOKIE["sortOrder"]== "Ascending"){
          usort($sorted, function ($a, $b) {
            return strcmp($a[$_COOKIE["sortBy"]], $b[$_COOKIE["sortBy"]]);
          });
        }
        else{
          usort($sorted, function ($a, $b) {
           return strcmp($b[$_COOKIE["sortBy"]], $a[$_COOKIE["sortBy"]]);
          });
        }
      }
	    ?> 
    <div id="wrapper">
      <div id="sidebar">
        <h1>Movie Catalog</h1>
          <form name="searchSortForm" id="form" action="/index.php" method="POST">
            <label for="search">Search : </label>
            <input type="text" name="search" id="searchBox" placeholder="e.g. Harry Potter" value="<?php echo $search; ?>">
            <br>
            <label for="sortBy">Sort: </label>
            <select id="SortBy" name="sortBy">
              <option value="year" <?php if($sortBy == "year") echo "selected"; ?>)>Year</option>
              <option value="title" <?php if($sortBy == "title") echo "selected"; ?>>Title</option>
              <option value="score" <?php if($sortBy == "score") echo "selected"; ?>>Score</option>
            </select>
            <select id="SortOrder" name="sortOrder">
              <option value="Ascending" <?php if($sortOrder == "Ascending") echo "selected"; ?>>Ascending</option>
              <option value="Descending" <?php if($sortOrder == "Descending") echo "selected"; ?>>Descending</option>
            </select>
            <br><br>
            <input type="submit" value="Submit" onclick="onClick();">
          </form>
          <br><br>

      </div>
      <div id="catalog"> <?php
				foreach($sorted as $movie) {
					echo "<div class=\"item\" id=\"".trim($movie->title)."\">";
					echo "<h2>$movie->title</h2>\n";
					echo "<img src=\"images/$movie->image\">\n";
          echo "<p>$movie->year, $movie->length</p>\n";
					echo "<p>$movie->genre</p>\n";
					echo "<p>$movie->director</p>\n";
					echo "<p>$movie->score</p>\n";
					echo "</div>";
				}
			?> </div>
    </div>
    <script>
      function onLoad(){
        document.getElementById("sidebar").style.height = document.getElementById("catalog").offsetHeight + "px";
      }

      function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/; secure";
      }

      function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
          let c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1);
          }
          if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
          }
        }
        return "";
      }

      function onClick(){
        var sortBy = document.getElementById("SortBy");
        var sortOrder = document.getElementById("SortOrder");
        var searchBox = document.getElementById("searchBox");
        setCookie("sortBy", sortBy.options[sortBy.selectedIndex].value, 1)
        getCookie("sortBy");
        setCookie("sortOrder", sortOrder.options[sortOrder.selectedIndex].value, 1);
        setCookie("search", searchBox.value, 1)
        window.location.reload();
      }
      window.onload = function() {
      var test = document.getElementsByClassName("item");
      var catalog = document.getElementById("catalog");
      var array = <?php echo json_encode($sorted); ?>;
      if(test !== undefined)
      {
        for(var i = 0, length = test.length; i < length; i++) {
            //test[i].value = "\""+i+"\"";
            test[i].style.cursor = 'pointer';
            test[i].addEventListener("click",function(e)
            {
              this.onClick = outputSummary(array, this.id, catalog);
            })
      }
    };
  }
    </script>
  </body>
</html>