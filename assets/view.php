<head>
<link href="assets/css/style.css" rel="stylesheet" type="text/css">

</head>
<div class="bar">
<img  class="mlb_logo"src="https://upload.wikimedia.org/wikipedia/en/thumb/8/82/ESPN_MLB_logo.png/200px-ESPN_MLB_logo.png">
<div class="scores">
    <span><?php echo $_SESSION['homeTeam']['info']['name'];?></span>
    <div class="runs"><span><?php echo $_SESSION['hometeam_score'];?></span></div>
</div>
<div class="scores">
    <span><?php echo $_SESSION['roadTeam']['info']['name'];?></span>
    <div class="runs"><span><?php echo $_SESSION['roadteam_score'];?></span></div>
</div>
<div class="info">
 <p> Outs: <?php echo $_SESSION['outs'];?></p>
 <p> Inning: <?php echo $_SESSION['inning'];?></p>
</div>
<div class="bases">
<div class="diamond" style="<?php if($_SESSION['runners']['2b'])echo 'background:yellow'?>"></div>
 <div class="diamond" style="<?php if($_SESSION['runners']['1b'])echo 'background:yellow'?>"></div>
 <div class="diamond" style="<?php if($_SESSION['runners']['3b'])echo 'background:yellow'?>"></div>
</div>
<p class="result">  <?php if (isset($info['result']))echo $info['result'];?></p>
<div  class="button">
<a href="index.php?action=reset" style="margin:auto;color:white;text-decoration:none;">Reset</a>
</div>
</div>

<div class="container">

    <div class="cards">
        <div class="batter card">
        <p> Name: <?php echo $nextBatter['name'];?></p>
        <p> Contact: <?php echo $nextBatter['contact'];?></p>
        <p> Power: <?php echo $nextBatter['power'];?></p>
        <p> Speed: <?php echo $nextBatter['speed'];?></p>
        </div>
        <div class="button">
        <a href="index.php?action=next">Next</a>
        </div>
        <div class="pitcher card">
        <p> Name: <?php echo $pitcher['name'];?></p>
        <p> fastBall: <?php echo $pitcher['fastball'];?></p>
        <p> Control: <?php echo $pitcher['control'];?></p>
        <p> Movement: <?php echo $pitcher['movement'];?></p>
        </div>
    </div>
</div>

 <?php if (isset($_SESSION['message'])){
            echo "<script>alert('".$_SESSION['message']."')</script>";
            unset($_SESSION['message']);
        }?>

</body>
</html>