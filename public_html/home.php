<!-- 
  PROJECT: Utilizing Data Mining Technologies to Analyze the Physical and
           Cognitive Benefits of Golf Therapy on Seniors
  AUTHORS: Steven Merino, McKay Russell,
           Clemente Rodriguez, Zachary Scholefield
  ADVISOR: Dr. Chengwei Lei
  SEMESTERS: Fall 2021, Spring 2022
 -->

 <!-- Page that displays correlation tables and search function-->
<?php
    include "./nav.php";
    include "./header.php";
?>
 <body>
	<!-- Autocomplete search bar -->
	<div class="container-md">
		<div class="container-md">
			<?php include "connect.php";?>
			<div data-aos="zoom-out-down" data-aos-anchor-placement="top-bottom"></div>
			<?php include "header.php";?>
			<!-- Search table -->
			<h4 class="rando"><br>Type in a Measurement Below to Search</h4>
			<div class="searchbar">
				<html lang="en" dir="ltr">
					<!--Make sure the form has the autocomplete function switched off:-->
					<form autocomplete="off" action="home.php" method="post">
					<div class="autocomplete" style="width:300px;">
						<input id="myInput" type="text" name="myMeasurement" placeholder="Measurement">
					</div>
					<input type="submit" value="Search">
					</form>
				</html>
			</div>
<?php
	// Line that appears above table of search results
	if (isset($_POST['myMeasurement'])) {
		$selection = htmlspecialchars($_POST['myMeasurement']); ?>
		<h5 class="rando"><br>From greatest to least, <strong><?php echo $selection; ?></strong> and its correlations:</h5>
		<?php
		echo '<div class="container-md scroller">';
	} else {
		echo '<div class="container-md scroller" style="display: none">';
	}
	?>
<!-- <div class="container-md scroller"> -->
	<!-- Correlation Table Header -->
	<table>
		<!-- Table that appears when search result is entered -->
		<tr>
			<th>Measurement</th>
			<th>Correlation</th>
		</tr>
			<?php
				$row = 1;
				$data = [];
				if (($handle = fopen("3cols.csv", "r")) !== FALSE) {
					while (($dataInit = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($dataInit);
						$row++;
						for ($c=0; $c < $num; $c++) {
							array_push($data, $dataInit[$c]);
						}
					}
					fclose($handle);
				}
				
				$rows = [];

				if (isset($_POST['myMeasurement'])) {
					$selection = htmlspecialchars($_POST['myMeasurement']);
					$i = 0;
					$temp = 0;
					while ($i < count($data)) {
						
						if ($data[$i] == $selection && $temp == 0) {
							array_push($rows, $data[$i + 1]);
							array_push($rows, $data[$i + 2]);
						}
						elseif ($data[$i] == $selection && $temp == 1) {
							array_push($rows, $data[$i - 1]);
							array_push($rows, $data[$i + 1]);
						}
						
						if ($temp == 0) {
							$temp++;
						}
						elseif($temp == 1) {
							$temp++;
						}
						elseif($temp == 2) {
							$temp = 0;
						}
						$i++;
					}
				}
				// Sort the array from highest correlation to lowest
				$measTemp = '';
				$corrTemp = 0;
				$num = count($rows);
				for ($c=0; $c < $num; $c+=2) {
					for ($d = $c + 2; $d < $num; $d+=2) {
						if ($rows[$c + 1] < $rows[$d + 1]){
							$measTemp = $rows[$c];
							$corrTemp = $rows[$c + 1];

							$rows[$c] = $rows[$d];
							$rows[$c + 1] = $rows[$d + 1];
							
							$rows[$d] = $measTemp;
							$rows[$d + 1] = $corrTemp;
						}
					}
				}
				for ($c=0; $c < $num; $c+=2) {
					?>
					<div class="" style="margin:auto">
					<?=
					// Correlation Coefficient Data
					'</td><td>' . 
					$rows[$c] . 
					'</td><td>' . 
					$rows[$c + 1] . 
					'</td></tr>'?>
					</div><?php
				}
			?>		
	</table>
</div>

	<script>
		// Autocomplete functionality

		// Read in measurement names from file
		var data = new XMLHttpRequest();
		data.open("GET", "./firstCol.csv", false);
		data.onreadystatechange = function ()
		{
		if(data.readyState === 4)
		{
			if(data.status === 200 || data.status == 0)
			{
			var allText = data.responseText;
			// alert(allText);
			}
		}
		}
		data.send(null);

		// create the chart and set the data
		// document.write(data.responseText);
		// chart = anychart.heatMap(data.responseText);
		var measurements = data.responseText.split('\n');
		
		autocomplete(document.getElementById("myInput"), measurements);
		
		function autocomplete(inp, arr) {
		/*the autocomplete function takes two arguments,
		the text field element and an array of possible autocompleted values:*/
		var currentFocus;
		/*execute a function when someone writes in the text field:*/
		inp.addEventListener("input", function(e) {
			var a, b, i, val = this.value;
			/*close any already open lists of autocompleted values*/
			closeAllLists();
			if (!val) { return false;}
			currentFocus = -1;
			/*create a DIV element that will contain the items (values):*/
			a = document.createElement("DIV");
			a.setAttribute("id", this.id + "autocomplete-list");
			a.setAttribute("class", "autocomplete-items");
			a.setAttribute("style", "overflow: auto");
			/*append the DIV element as a child of the autocomplete container:*/
			this.parentNode.appendChild(a);
			/*for each item in the array...*/
			for (i = 0; i < arr.length; i++) {
				/*check if the item starts with the same letters as the text field value:*/
				if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
				/*create a DIV element for each matching element:*/
				b = document.createElement("DIV");
				/*make the matching letters bold:*/
				b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
				b.innerHTML += arr[i].substr(val.length);
				/*insert a input field that will hold the current array item's value:*/
				b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
				/*execute a function when someone clicks on the item value (DIV element):*/
					b.addEventListener("click", function(e) {
					/*insert the value for the autocomplete text field:*/
					inp.value = this.getElementsByTagName("input")[0].value;
					/*close the list of autocompleted values,
					(or any other open lists of autocompleted values:*/
					closeAllLists();
				});
				a.appendChild(b);
				}
			}
		});
		/*execute a function presses a key on the keyboard:*/
		inp.addEventListener("keydown", function(e) {
			var x = document.getElementById(this.id + "autocomplete-list");
			if (x) x = x.getElementsByTagName("div");
			if (e.keyCode == 40) {
				/*If the arrow DOWN key is pressed,
				increase the currentFocus variable:*/
				currentFocus++;
				/*and and make the current item more visible:*/
				addActive(x);
			} else if (e.keyCode == 38) { //up
				/*If the arrow UP key is pressed,
				decrease the currentFocus variable:*/
				currentFocus--;
				/*and and make the current item more visible:*/
				addActive(x);
			} else if (e.keyCode == 13) {
				/*If the ENTER key is pressed, prevent the form from being submitted,*/
				e.preventDefault();
				if (currentFocus > -1) {
				/*and simulate a click on the "active" item:*/
				if (x) x[currentFocus].click();
				}
			}
		});
		function addActive(x) {
			/*a function to classify an item as "active":*/
			if (!x) return false;
			/*start by removing the "active" class on all items:*/
			removeActive(x);
			if (currentFocus >= x.length) currentFocus = 0;
			if (currentFocus < 0) currentFocus = (x.length - 1);
			/*add class "autocomplete-active":*/
			x[currentFocus].classList.add("autocomplete-active");
		}
		function removeActive(x) {
			/*a function to remove the "active" class from all autocomplete items:*/
			for (var i = 0; i < x.length; i++) {
			x[i].classList.remove("autocomplete-active");
			}
		}
		function closeAllLists(elmnt) {
			/*close all autocomplete lists in the document,
			except the one passed as an argument:*/
			var x = document.getElementsByClassName("autocomplete-items");
			for (var i = 0; i < x.length; i++) {
			if (elmnt != x[i] && elmnt != inp) {
			x[i].parentNode.removeChild(x[i]);
			}
		}
		}
		/*execute a function when someone clicks in the document:*/
		document.addEventListener("click", function (e) {
			closeAllLists(e.target);
		});
		}

	</script>
	
	<!-- </body> -->
	<br><br><br>
</div>
<h3 class="rando"><br>Correlation Coefficient Above Zero</h3>
<div class="container-md scroller">   
	<!-- Correlation Table Header -->
	<table>
		<tr>
			<th>Measurement One</th>
			<th>Measurement Two</th>
			<th>Correlation</th>
		</tr>
		<?php
			$row = 1;
			if (($handle = fopen("CorrCoAboveZero.csv", "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					$row++;
					for ($c=0; $c < $num; $c+=3) {
						?>
						<div class="" style="margin:auto">
						<?=
						// Correlation Coefficient Data
						'<tr><td>' . 
						$data[$c] . 
						'</td><td>' . 
						$data[$c + 1] . 
						'</td><td>' . 
						$data[$c + 2] . 
						'</td></tr>'?>
						</div><?php
					}
				}
				fclose($handle);
			}
		?>
	</table>
</div>
<!-- Less than or equal to 0 correlation -->
<h3 class="rando"><br>Correlation Coefficient Below Zero</h3>
<div class="container-md scroller">
	<!-- Correlation Table Header -->
	<table>
		<tr>
			<th>Measurement One</th>
			<th>Measurement Two</th>
			<th>Correlation</th>
		</tr>
			<?php 
				$row = 1;
				if (($handle = fopen("CorrCoBelowZero.csv", "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						$row++;
						for ($c=0; $c < $num; $c+=3) {
							?>
							<div class="" style="margin:auto">
							<?=
							// Correlation Coefficient Data
							'<tr><td>' . 
							$data[$c] . 
							'</td><td>' . 
							$data[$c + 1] . 
							'</td><td>' . 
							$data[$c + 2] . 
							'</td></tr>'?>
							</div><?php
						}
					}
					fclose($handle);
				}
			?>		
	</table>
</div>
</div>
<?php include "./footer.php";?>
</footer>


