<?php 
session_start();

require_once 'baseball.php';

$baseball = new Baseball();

$team1 = 'teams/team_fire.json';
$team2 = 'teams/team_water.json';


if(isset($_GET['action']) && $_GET['action'] == 'reset'){
    $info =  $baseball->resetGame();
}

$baseball->initGame($team1,$team2);


if(isset($_GET['action']) && $_GET['action'] == 'next'){
    $info =  $baseball->simNext();
}

$nextBatter = $baseball->getNextBatter();
$pitcher = $baseball->getTeamPitcher();



require ('assets/view.php');


 



//players
$hitterWeak = [
    'contact' => 50,
    'power' => 50,
    'speed' => 50,
];

$pitcherWeak = [
    'fastball' => 60,
    'control'  => 50,
    'movement' => 60
];

$hitterStrong = [
    'contact' => 90,
    'power' => 90,
    'speed' => 80,
];

$pitcherStrong = [
    'fastball' => 100,
    'control'  => 90,
    'movement' => 80
];


?>