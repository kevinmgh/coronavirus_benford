<html>
  <head>
    Data collected from <a href=https://usafacts.org/visualizations/coronavirus-covid-19-spread-map/>usafacts.org</a> on 06/25/2020: Confirmed cases by county<br>
    Source: <a href="https://github.com/kevinmgh/coronavirus_benford/blob/master/chart.php">This script</a>, <a href="covid_confirmed_usafacts.csv">Raw CSV file</a><br>
    Showing the frequency distribution of the first digit of coronavirus cases in US counties as of 6/23/20 (the last entry in the csv file generated as of 06/25)<br>
    To see if if follows <a href=https://en.wikipedia.org/wiki/Benford%27s_law>Benford's Law</a><br>
    For an accessible layman's explanation, see <a href=https://www.youtube.com/watch?v=XXjlR2OK1kM>this video</a><br>
    <br><br><br>
  
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawCumulative);
      google.charts.setOnLoadCallback(drawNewCases);
      
      var cumulativeArray = <?php tallyPrefixes_cumulative(); ?>;
      var newCasesArray = <?php tallyPrefixes_newcases(); ?>;
      
      function drawChart(dataArray, dataTitle, dataSubtitle, chartDiv)
      {
          var data = new google.visualization.arrayToDataTable([
                ['Digit', 'Count'],
                ["1", dataArray[0]],
                ["2", dataArray[1]],
                ["3", dataArray[2]],
                ["4", dataArray[3]],
                ["5", dataArray[4]],
                ["6", dataArray[5]],
                ["7", dataArray[6]],
                ["8", dataArray[7]],
                ["9", dataArray[8]]
        ]);

        var options = {
          width: 800,
          legend: { position: 'none' },
          chart: {
            title: dataTitle,
            subtitle: dataSubtitle },
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

        var chart = new google.charts.Bar(document.getElementById(chartDiv));
        // Convert the Classic options to Material options.
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
      
      function drawCumulative() 
      {
        drawChart(cumulativeArray, 'First Digit of cumulative confirmed coronavirus cases', 'US counties as of 6/23/20', 'cumulative_div');
      }
      
      function drawNewCases() 
      {
        drawChart(newCasesArray, 'First Digit of daily new coronavirus cases', 'US counties from 1/22/20 until 6/23/20', 'newcases_div');
      }
      
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
       
       function startsWithDigit($number, $digit)
       {
           return $digit == substr($number, 0, 1);
       }     
       
       function getFirstDigit($number)
       {
           return (int)substr($number, 0, 1);
       }
       
       function tallyPrefixes_cumulative()
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
         // Different bases
         $numOccurrences = array();
         
         for($digit = 1; $digit < 10; ++$digit)
         {
             $numOccurrences[$digit - 1] = 0;
         }
         
         $arrayLength = count($csvArray);
         for($i = 1; $i < $arrayLength; ++$i)
         {
             $length = count($csvArray[$i]);
             
             //for now, just checking the last column (maybe we'll analyze different data)
             $lastColumnValue = $csvArray[$i][$length - 1];
             
             for($digit = 1; $digit < 10; ++$digit)
             {
                if(getFirstDigit($lastColumnValue) == $digit)
                {
                    ++$numOccurrences[$digit - 1];
                }
             }   
         }
         
         echo json_encode($numOccurrences);
       }
       
       function tallyPrefixes_newcases()
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
         // Different bases 
         $numOccurrences = array();
         
         for($digit = 1; $digit < 10; ++$digit)
         {
             $numOccurrences[$digit - 1] = 0;
         }
         
         $arrayLength = count($csvArray);
         for($i = 1; $i < $arrayLength; ++$i)
         {
             $length = count($csvArray[$i]);
             
             //for now, just checking the last column (maybe we'll analyze different data)
             //$lastColumnValue = $csvArray[$i][$length - 1];
             
             //The first numbers in our dataset start at index 4.
             // Since they represent the total cumulative cases as of that date, the
             // number of new daily cases would be the difference between that day and the previous one
             // so start at index 5.
             $previousColumnValue = $csvArray[$i][4];
             for($j = 5; $j < $length; ++$j)
             {
                $thisColumnValue = $csvArray[$i][$j];
                
                //Don't bother checking the digits if the cumulative value up until now is 0
                if($thisColumnValue == 0)
                {
                    continue;
                }
                
                for($digit = 1; $digit < 10; ++$digit)
                {
                    $difference = $thisColumnValue - $previousColumnValue;
                    if(getFirstDigit($difference) == $digit)
                    {
                        ++$numOccurrences[$digit - 1];
                    }
                }                 
             }
         }
         
         echo json_encode($numOccurrences);
       }
    ?>
  </head>
  <body>
    <div id="cumulative_div" style="width: 800px; height: 600px;"></div>
    <div id="space" style="width: 800px; height: 100px;"></div>
    <div id="newcases_div" style="width: 800px; height: 600px;"></div>
  </body>
</html>
