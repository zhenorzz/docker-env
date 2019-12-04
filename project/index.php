<?php
// 注意该文件不能热重启，请修改后手动重启
define('MEMORY_SIZE', '2G');
function readCSV($file)
{
  $row      = 0;
  $csvArray = array();
  if (($handle = fopen($file, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
      $num = count($data);
      for ($c = 0; $c < $num; $c++) {

        $csvArray[$row] = explode(',', str_replace('"', '', $data[$c]));
      }
      $row++;
    }
  }
  $first = [];
  $first = $csvArray[0];
  $csvArray = array_splice($csvArray, 1); //cut off the first row (names of the fields)
  foreach ($csvArray as &$csv) {
    $csv = array_combine($first, $csv);
  }
  return $csvArray;
}

$csvData = readCSV(__DIR__ . '/test1130.csv'); //This is your array with the data

//495658
$time1 = microtime(true);
array_multisort(array_column($csvData, 'login_days'),  SORT_DESC, array_column($csvData, 'game_reg_date'),  SORT_ASC, $csvData);
$time2 =  microtime(true);
for ($i = 0; $i < 1000; $i++) {
  print_r($csvData[$i]);
}
print_r(round($time2 - $time1, 4));
