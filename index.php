<?php 
  session_start();
  date_default_timezone_set("Europe/Moscow");
  // class for timing of working script
  class Timer {
      /**
       * @var float a time of starting work 
       */
      private static $start = .0;

      // start work
      static function start()
      {
          self::$start = microtime(true);
      }


    /**
     * @return string
     */
    static function getStartTime() {
      return date("H:i:s $startTime");
    }

      /**
       * Difference between the marked time and mark of self::$start
       * @return float
       */
      static function getWorkTime()
      {
          return round((microtime(true) - self::$start) * 1e6, 1);
      }
  }
  // start timing
  Timer::start();
  
  // check coordinates point with coordinates of figures.
  function checkPoint($x, $y, $r) {
    if ($y >= 0) {
      // 1 quarter(rectangle).
      if ($x >= 0)
        return $x <= $r / 2 && $y <= $r;
      // 2 quarter(triangle).
      else
        return $y - $x <= $r / 2;
    } else {
      // 3 quarter(empty).
      if ($y >= 0)
        return false;
      // 4 quarter(circle).
      else
        return $x * $x + $y * $y <= $r * $r / 4;
    }
  }

  // get and process POST form's data
  // get checked X
  $arrayCheckedX = [];
  if (isset($_POST["x"]))
    $arrayCheckedX = $_POST["x"];
  // get Y
  $y = '';
  if (isset($_POST["y"]))
    $y = $_POST["y"];
  // get checked R
  $arrayCheckedR = [];
  if (isset($_POST["r"]))
    $arrayCheckedR = $_POST["r"];
  // get max R
  $maxR = 0;
  if (!empty($arrayCheckedR))
    foreach ($arrayCheckedR as $key => $r)
      $maxR = $r;

  // add new results
  $arrayNewResults = [];
  // if the command "reset results" or the first request - clear session's results
  if (isset($_POST["reset-results"]) || !isset($_SESSION["results"]))
    $_SESSION["results"] = [];
  // if the session with form's data - add processing data to the session
  else if (isset($_POST["submit"]))
    foreach ($arrayCheckedX as $key => $x)
      array_push($_SESSION["results"], array(x => $x, y => $y, r => $maxR, st => Timer::getStartTime(), wt => Timer::getWorkTime(), res => checkPoint($x, $y, $maxR)));
?>
<!DOCTYPE html>
<html>
  <head>
    <!-- Type of content -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
    <!-- Title -->
    <title>Web-1</title>

    <!-- Icon -->
    <link rel="shorcut icon" href="img/wheat.ico" type="image/x-icon"/>

    <!-- Styles -->
    <style>
      /*background of the site*/
      body {
        background-repeat: no-repeat;
        background-image: url("img/background.jpg");
        -moz-background-size: 100%; /* Firefox 3.6+ */
        -webkit-background-size: 100%; /* Safari 3.1+ и Chrome 4.0+ */
        -o-background-size: 100%; /* Opera 9.6+ */
        background-size: cover;
        user-select: none;
      }

      /*Header with my name*/
      .header {
        position: static;
        font: 2.5vw italic cursive;
        text-align: center;
        margin: 1vw 1vw 0.5vw 1vw;
        clear: both;
        background-color: rgba(255, 255, 255, 0.3);
        border: 0.5vw solid rgba(0, 0, 0, 0.5);
      }
      /*Graph with coordinate grid*/
      .graph {
        position: static;
        float: left;
        opacity: 1;
        background-color: rgba(255, 255, 255, 0.3);
        margin: 0.5vw 0.5vw 0.5vw 1vw;
        border: 0.5vw solid rgba(0, 0, 0, 0.5);
        width: 45.5vw;
        height: 45.5vw;
      }
      /*POST-form*/
      .form {
        font: 2.5vw serif;
        text-align:center;
        position: static;
        float: left;
        margin: 0.5vw 1vw 0.5vw 0.5vw;
        width: 47.5vw;
        height: 43.5vw;
        clear: right;
      }

      .svgGraph {
        width: 100%;
        height: 100%;
      }
      /*axises arrows and line in axises*/
      .vector line {
        stroke: rgb(0,0,0);
        stroke-width: 0.4%;
        fill-opacity: 1;
        stroke-opacity: 1;
      }
      /*names of axises (X, Y)*/
      .vector .axisName {
        font: italic 3vw serif;
      }
      /*ones on orts (OX and OY)*/
      .vector .ort {
        font: italic 3vw serif; 
      }

      /*for figures on coordinate grid*/
      .figure {
        stroke: #000;
        fill: yellow;
        fill-opacity: 0.3;
        stroke-opacity: 1;
      }
      .figure:hover {
        fill: orange;
      }
      /*figure circle in 3-rd coordinate quartel*/ 
      .figure circle {
        clip-path: polygon(50% 50%, -10% 50%, -10% 110%, 50% 110%);
      }

      /*Dangerous-unvalid stars*/
      /*Red stars*/
      .redtext {
        color: red;
      }
      /*Red points*/
      .point {
        stroke: #000;
        fill: red;
      }
      .point:hover {
        fill: white;
      }

      /*X Form*/
      .coordinateForm {
        background-color: rgba(255, 255, 255, 0.3);
        margin: 0.2vw 0.2vw 0.2vw 0.2vw;
        border: 0.1vw solid rgba(0, 0, 0, 0.7);
      }
      .boxForm {
        background-color:rgba(0, 255, 255, 0.3);
        margin: 0.2vw 0.2vw 0.2vw 0.2vw;
        width: 2vw;
        height: 2vw;
        border: 0.1vw solid rgba(0, 0, 0, 0.7);
        align-items: center;
        cursor: pointer;
      }
      .boxForm:hover{
        background-color: rgba(255, 255, 0, 0.7);
      }

      .boxForm input {
        display: none;
      }
      label:hover span {
        background-color: rgba(255, 255, 0, 0.7);
        cursor: pointer;
      }

      .boxForm input[type="checkbox" checked]:checked {  
        background-color: rgba(0, 255, 255, 0.7);
      }

      .boxForm input[type="checkbox"]:checked+span {  
        background-color: rgba(0, 255, 255, 0.7);
      }

      input[name="submit"] {
        margin: 2vw;
        width: 6vw;
        height: 3vw;
        border: 2px solid gray;
      }

      input[name="submit"]:hover {
        background-color: rgba(0, 255, 0, 0.3);
        border: 2px solid;
        border-color: rgb(0, 255, 0);
      }
      input[name="reset-form"] {
        margin: 2vw;
        width: 10vw;
        height: 3vw;
        border: 2px solid gray;
      }

      input[name="reset-form"]:hover {
        background-color: rgba(255, 255, 0, 0.3);
        border: 2px solid;
        border-color: rgb(255, 255, 0);
      }
      input[name="reset-results"] {
        margin: 2vw;
        width: 12vw;
        height: 3vw;
        border: 2px solid gray;
      }

      input[name="reset-results"]:hover {
        background-color: rgba(0, 0, 255, 0.3);
        border: 2px solid;
        border-color: rgb(0, 0, 255);
      }

      table {
        width: 100%;
        height: 100%;
        border: 1;
        background-color: rgba(0, 0, 0, 0.2);
      }
      .results {
        width: 102.5%;
        max-height: 32vh;
        overflow-y: scroll;
      }
      th {
        width: 14.3%;
        font-size: 1.7vw;
        color: rgba(255, 255, 100, 0.9);
        background-color: rgba(0, 0, 0, 0.2);
      }
      td {
        width: 14.3%;
        font-size: 1.7vw;
        background-color: rgba(255, 255, 255, 0.5);
      }
      .result-true {
        background-color: rgba(0, 255, 0, 0.5);
      }
      .result-false {
        background-color: rgba(255, 0, 0, 0.5);
      }
    </style>
  </head>
  <body>
    <!-- Header -->
    <div class="header">
      <span>Работу выполнил: Дёмин Д.П., группа: P3213, вариант №2404.</span>
    </div>

     <!-- Coordinate grid -->
    <div class="graph">
      <svg class="svgGraph">
        <!-- OX -->
        <g class="vector">
          <line x1="4%" y1="48%" x2="92%" y2="48%"/>

          <!-- Arrow-right -->
          <line x1="88.8%" y1="46.4%" x2="92%" y2="48%"/>
          <line x1="88.8%" y1="49.6%" x2="92%" y2="48%"/>

          <!-- name X -->
          <text class="axisName" x="89.6%" y="44%">x</text>

          <!-- streaks OX -->
          <?php for ($x = -5; $x <= 5; ++$x): ?>
          <line x1="<?= 48 + $x * 8 ?>%" y1="47.2%" x2="<?= 48 + $x * 8 ?>%" y2="48.8%"/>
          <?php endfor ?>
          
          <!-- 1 on OX -->
          <text class="ort" x="55.2%" y="45.6%">1</text>
        </g>

        <!-- OY -->
        <g class="vector">        
          <line x1="48%" y1="92%" x2="48%" y2="4%"/>
          
          <!-- Arrow-up -->
          <line x1="46.4%" y1="7.2%" x2="48%" y2="4%"/>
          <line x1="49.6%" y1="7.2%" x2="48%" y2="4%"/>
          
          <!-- name Y -->
          <text class="axisName" x="52%" y="6.4%">y</text>
          
          <!-- streaks OY -->
          <?php for ($i = -5; $i <= 5; ++$i): ?>
          <line x1="47.2%" y1="<?= 48 + $i * 8 ?>%" x2="48.8%" y2="<?= 48 + $i * 8?>%"/>
          <?php endfor ?>

          <!-- 1 on OY -->
          <text class="ort" x="50.4%" y="40.8%">1</text>
        </g>

        <!-- Figures  -->
        <g id="figures">
          <?php for ($r = 1; $r <= 3; $r += 0.5):
                $display = (!empty($arrayCheckedR) && array_search($r, $arrayCheckedR) !== false) ? "inline": "none"; ?>
          <g id="figure<?= $r ?>" class="figure" style="display: <?= $display ?>">
            <!-- Circle -->
            <circle r="<?= $r * 4 ?>%" cx="47.8%" cy="47.8%"/>
            <!-- Triangle -->
            <svg viewBox="0 0 100 100">
              <polygon style="vector-effect: non-scaling-stroke;" points="48,48  <?= 48 - $r * 4 ?>,48 48,<?= 48 - $r * 4 ?>"/>
            </svg>
            <!-- Rectangle -->
            <rect x="48%" y="<?= 48 - $r * 8 ?>%" width="<?= $r * 4 ?>%" height="<?= $r * 8 ?>%"/>
          </g>
          <?php endfor ?>
        </g>

        <!-- Points -->
        <g id="points">
          <?php for ($x = -3; $x <= 5; ++$x):
                $id = "pointx".($x < 0 ? "m".-$x : $x);
                $display = (!empty($arrayCheckedX) && array_search($x, $arrayCheckedX) !== false) ? "inline": "none"; 
                $cy = isset($_POST["y"]) ? 48 - $y * 8 : 48?>
          <circle id = "<?= $id ?>" class="point" style="display: <?= $display ?>" r="0.8%" cx="<?= 48 + $x * 8 ?>%" cy="<?= $cy ?>%"/>
          <?php endfor ?>
        </g>
      </svg>
    </div>


    <!-- Form -->
    <div class="form">
      <form method="POST">

        <!-- X -->
        <div class="coordinateForm">
          <u>Check X coordinate:</u>
          <br>
          <?php for($x = -3; $x <= 5; ++$x): 
                $checked = (!empty($arrayCheckedX) && array_search($x, $arrayCheckedX) !== false) ? "checked": ""; ?>
          <label class="boxForm">
            <input id="x<?= $x < 0 ? "m".-$x : $x ?>" type="checkbox" name="x[]" onchange="changeX(<?= $x ?>);" value="<?= $x ?>" <?= $checked ?>>
            <span><?= $x ?></span>
          </label>
          <?php endfor ?>
          <br>
        </div>

        <!-- Y -->
        <div class="coordinateForm">
          <u>Input Y coordinate from -3 to 5:</u>
          <br>
          <input id="y" required type="number" name="y" step="1e-12" oninput="changeY();" min="-3" max="5" value="<?= $y ?>">
          <br>
        </div>

        <!-- R -->
        <div class="coordinateForm">  
          <u>Check R:</u>
          <br>
          <?php for($r = 1; $r <= 3; $r += 0.5): 
                $checked = (!empty($arrayCheckedR) && array_search($r, $arrayCheckedR) !== false) ? "checked": ""; ?>
          <label class="boxForm">
            <input id="checkboxR<?= $r; ?>" type="checkbox" name="r[]" onchange="changeR(<?= $r; ?>);" value="<?= $r ?>" <?= $checked ?>>
            <span><?= $r ?></span>
          </label>
          <?php endfor ?>
        </div>

        <!-- Submit button -->
        <div>
          <input type="submit" class="btn btn-submit" name="submit" value="submit">
          <input type="reset" class="btn btn-reset-form" name="reset-form" value="reset form">
          <input type="submit" class="btn btn-reset-results" name="reset-results" value="reset results">
        </div>

        <!-- Table with results -->
        <div>
          <table>
            <tr>
              <th>x</th>
              <th>y</th>
              <th>r</th>
              <th>start</th>
              <th>work</th> <!-- mcs -->
              <th>result</th>
            </tr>
          </table>
          <div class="results">
          <table>
            <?php if (!empty($_SESSION["results"])):
                foreach ($_SESSION["results"] as $key => $result_tuple):
                  $result_class = "result-".($result_tuple[res] ? "true" : "false"); ?>
              <tr class="result-fields <?= $result_class ?>">
                <td class="field"><?= $result_tuple[x] ?></td>
                <td class="field"><?= round($result_tuple[y], 3) ?></td>
                <td class="field"><?= $result_tuple[r] ?></td>
                <td class="field"><?= $result_tuple[st] ?></td>
                <td class="field"><?= $result_tuple[wt] ?></td>
                <td class="field"><?= $result_tuple[res] ? "yes" : "no" ?></td>
              </tr>
            <?php endforeach; endif ?>
          </table>
          </div>
        </div>
      </form>
    </div>

    <!--download jQuery-->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- JavaScript -->
    <script src="js/script.js"></script>  
  </body>
</html>