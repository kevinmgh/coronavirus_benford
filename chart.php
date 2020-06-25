<html>
  <head>
    Data collected from <a href=https://usafacts.org/visualizations/coronavirus-covid-19-spread-map/>usafacts.org</a> on 06/25/2020: Confirmed cases by county<br>
    Showing the frequency distribution of the first digit of the total coronavirus cases per county in the US as of 6/23/20 (the last entry in the csv file generated as of 06/25)<br>
    To see if if follows <a href=https://en.wikipedia.org/wiki/Benford%27s_law>Benford's Law</a><br>
    For an accessible layman's explanation, see <a href=https://www.youtube.com/watch?v=XXjlR2OK1kM>this video</a><br>

    <p id="output"></p>
    <p><?php printcsvfile(); ?></p>
  
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);
      
      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          ['Digit', 'Count'],
          ["1", <?php findPrefix(1); ?>],
          ["2", <?php findPrefix(2); ?>],
          ["3", <?php findPrefix(3); ?>],
          ["4", <?php findPrefix(4); ?>],
          ["5", <?php findPrefix(5); ?>],
          ["6", <?php findPrefix(6); ?>],
          ["7", <?php findPrefix(7); ?>],
          ["8", <?php findPrefix(8); ?>],
          ["9", <?php findPrefix(9); ?>]
        ]);

        var options = {
          width: 800,
          legend: { position: 'none' },
          chart: {
            title: 'First Digit of cumulative confirmed coronavirus cases',
            subtitle: 'US counties as of 6/23/20' },
          axes: {
            x: {
              0: { side: 'top', label: 'First Digit'} // Top x-axis.
            },
            y: {
              0: {side: "left", label: "Total Count"} 
            }
          },
          bar: { groupWidth: "90%" }
        };

        var chart = new google.charts.Bar(document.getElementById('top_x_div'));
        // Convert the Classic options to Material options.
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };
    </script>
    <?php
   
       function loadFile(&$csvArray)
       {
         $filename = "covid_confirmed_usafacts.csv";
         $csvfile = fopen($filename, "r") or die("Unable to open file");
         
         while(($element = fgetcsv($csvfile)) !== FALSE)
         {
            $csvArray[] = $element;
         }
         
         fclose($csvfile);           
       }
   
       function printcsvfile()
       {
         $csvArray = array();
         loadFile($csvArray);
         
         $arrayLength = count($csvArray);
         //for($i = 0; $i < $arrayLength; ++$i)
         //{
         //    $length = count($csvArray[$i]);
         //    for($j = 0; $j < $length; ++$j)
         //    {
         //       echo($csvArray[$i][$j]); 
         //       echo(" ");
         //    }
         //    echo("<br>");
         //}
       }
       
       function startsWithDigit($number, $digit)
       {
           return $digit == substr($number, 0, 1);
       }       
    
       function findPrefix($digit)
       {
         $csvArray = array();
         loadFile($csvArray);
         
         //A little bit of foreknowledge here about the nature of our data file:
         // the first array element is going to be the column headers (eg, "county", "name", etc, then all the dates), 
         // so we'll skip that and start at index 1
         
         //For the subsequent rows, the numeric data will start at index 4.
         // For now, though, we're just going to check the last column (most recent date)
         
         //Possible future revisions:
         // Query specific dates
         // Include all dates?
         // Different bases (I really want to do this one)
         // 
         
         $numOccurrences = 0;
         $arrayLength = count($csvArray);
         for($i = 1; $i < $arrayLength; ++$i)
         {
             $length = count($csvArray[$i]);
             
             //for now, just checking the last column (maybe we'll analyze different data)
             $lastColumnValue = $csvArray[$i][$length - 1];
             if(startsWithDigit($lastColumnValue, $digit))
             {
                ++$numOccurrences;
             }
             
             
             //for($j = 0; $j < $length; ++$j)
             //{
             //   if(startsWithDigit($lastColumnValue, $digit))
             //   {
             //       ++$numOccurrences;
             //   }
             //}
         }
  
         echo($numOccurrences);
       }
    ?>
  </head>
  <body>
    <div id="top_x_div" style="width: 800px; height: 600px;"></div>
  </body>
</html>
