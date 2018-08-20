<?php


class Baseball
{
    
     
    public function simNext(){

        //check what team is batting and what team is pitching
        if(strpos((string)$_SESSION['inning'],'.')){
            $teamBatting = 'homeTeam';
            $teamPitching = 'roadTeam';
        }else{
            $teamBatting = 'roadTeam';
            $teamPitching = 'homeTeam';

        }
        //get player that is batting and pitcher
        $hitter = $this->getCurrentBatter($teamBatting);
        $pitcher = $this->getTeamPitcher();
     
        

        //get result of atbat
        $result = $this->simAtBat($hitter, $pitcher); 

        //check if it was an out
        if(in_array($result,['flyout', 'groundout','strikeout'] )){
            $_SESSION['outs'] += 1;
            if($_SESSION['outs'] > 2){
                $_SESSION['inning'] += 0.5;
                $_SESSION['outs'] = 0;
                //reset runners
                foreach ($_SESSION['runners'] as $key => $value) {
                    if ($value){
                        $_SESSION['runners'][$key] = false;
                    }
                }
            }
        }
        //if it was a single 
        if($result == 'single' ){
            $this->advanceRunner('single');
        }
        //if it was a double
        if($result == 'double' ){
            $this->advanceRunner('double');
        }
        //if it was a triple
        if($result == 'triple' ){
            $this->advanceRunner('triple');
        }
        //if it was a homerun
        if($result == 'homerun' ){
            $this->advanceRunner('homerun');
        }
        //if it was a base on ball
        if($result == 'baseonballs' ){
            $this->advanceRunner('baseonballs');
        }


        if ($_SESSION['inning'] > 9.5 && $_SESSION['hometeam_score'] != $_SESSION['roadteam_score']){
                if ($_SESSION['hometeam_score'] > $_SESSION['roadteam_score'])
                $winner = $_SESSION['homeTeam']['info']['name'];
                else{
                 $winner = $_SESSION['roadTeam']['info']['name'];

                }
            
            $_SESSION['message'] = $winner." won the Game! ".$_SESSION['hometeam_score']." VS ".$_SESSION['roadteam_score'];
             $this->resetGame();

        }
    
        

        return ["result" => $result, "pitcher" => $pitcher];
    }

    public function initGame($homeTeamFile,$roadTeamFile){
        
        if(!isset($_SESSION['outs'])){
            $_SESSION['outs'] = 0;
        }
        if(!isset($_SESSION['inning'])){
            $_SESSION['inning'] = 1;
        }
        if(!isset($_SESSION['runners'])){
            $_SESSION['runners'] = ['1b' => false,'2b' => false,'3b' => false];
        }
        if(!isset($_SESSION['hometeam_score'])){
            $_SESSION['hometeam_score'] = 0;
        }
        if(!isset($_SESSION['roadteam_score'])){
            $_SESSION['roadteam_score'] = 0;
        }
        if(!isset($_SESSION['homeTeam'])){
            $_SESSION['homeTeam'] = $this->loadTeam($homeTeamFile);
            $_SESSION['home_current_batter'] = 0;
        }
        if(!isset($_SESSION['roadTeam'])){
            $_SESSION['roadTeam'] = $this->loadTeam($roadTeamFile);
            $_SESSION['road_current_batter'] = 0;
        }

    }
    


    public function simAtBat($hitter, $pitcher){
    
        $singleChance = (100 - ($pitcher['fastball']+$pitcher['control'])/2 + $hitter['contact'])/10 ;
        $doubleChance = (100 - ($pitcher['fastball']+$pitcher['control'])/2 + ($hitter['power']+$hitter['speed'])/2)/15 ;
        $tripleChance = (100 - ($pitcher['fastball']+$pitcher['control'])/2 + ($hitter['contact']+$hitter['speed']*2)/2)/30 ;
        $homerunChance = (100 - ($pitcher['fastball']+$pitcher['control'])/2 + ($hitter['contact']+$hitter['power']*2)/2)/25 ;
        $baseonballChance = (100 - $pitcher['control'] + $hitter['power'])/25 ;
        $flyoutChance = ( ($pitcher['fastball']+$pitcher['movement']) - ($hitter['contact']+$hitter['power'])/2)/4 ;
        $strikeoutChance = (200 - $hitter['contact']*2 - $pitcher['fastball']+$pitcher['movement'])/4 ;
        $groundoutChance = (200 - $hitter['contact']*2 - $pitcher['control']+$pitcher['movement'])/4 ;
        
        $chances = ['single' => $singleChance , 'double' => $doubleChance , 'triple' => $tripleChance , 'homerun' => $homerunChance , 'baseonballs' => $baseonballChance , 'flyout' => $flyoutChance , 'strikeout' => $strikeoutChance ,'groundout' => $groundoutChance];
        
        $rand = mt_rand(1, (int) array_sum($chances));
        foreach ($chances as $key => $value) {
              $rand -= $value;
              if ($rand <= 0) {
                return $key;
            }
        }

    }

    public function advanceRunner($action){
        
        if ($action == 'single'){
        if($_SESSION['runners']['3b'] == true){
            $this->scoreRun();
            $_SESSION['runners']['3b'] = false;
        }

        if($_SESSION['runners']['2b']== true ){
            $_SESSION['runners']['3b'] = true;
            $_SESSION['runners']['2b'] = false;
        }

        if($_SESSION['runners']['1b']== true){
            $_SESSION['runners']['2b'] = true;
            $_SESSION['runners']['1b'] = false;
        }
        $_SESSION['runners']['1b'] = true;

    }

    if ($action == 'double'){
        if($_SESSION['runners']['3b'] == true){
            $this->scoreRun();
            $_SESSION['runners']['3b'] = false;
        }

        if($_SESSION['runners']['2b']== true ){
            $this->scoreRun();
            $_SESSION['runners']['2b'] = false;
        }

        if($_SESSION['runners']['1b']== true){
            $_SESSION['runners']['3b'] = true;
            $_SESSION['runners']['1b'] = false;
        }
        $_SESSION['runners']['2b'] = true;

    }
    if ($action == 'triple'){
        foreach ($_SESSION['runners'] as $key => $value) {
            if ($value){
                $_SESSION['runners'][$key] = false;
                $this->scoreRun();
            }
        }
        $_SESSION['runners']['3b'] = true;

    }
    if ($action == 'homerun'){
        $this->scoreRun();

        foreach ($_SESSION['runners'] as $key => $value) {
            if ($value){
                $_SESSION['runners'][$key] = false;
                $this->scoreRun();
            }
        }

    }

    if ($action == 'baseonballs'){
        //if there's nobody in first base
        if(!$_SESSION['runners']['1b']){
            $_SESSION['runners']['1b'] = true;
        }//if there is somebody in first base and nobody in second(avance runner in first base)
        else if(!$_SESSION['runners']['2b']){
            $_SESSION['runners']['2b'] = true;
            $_SESSION['runners']['1b'] = true;
        }//if there is somebody in first and  second base and nobody in third(avance runner in second base)
        else if(!$_SESSION['runners']['3b']){
            $_SESSION['runners']['3b'] = true;
            $_SESSION['runners']['1b'] = true;

        }else{
            $this->scoreRun();
        }
    }

    

    
}


    public function scoreRun(){
        if(strpos((string)$_SESSION['inning'],'.')){
            $_SESSION['hometeam_score'] += 1;
        }else{
            $_SESSION['roadteam_score'] += 1;

        }
    }

    public function loadTeam($fileTeam){
        $str = file_get_contents($fileTeam);
        $json = json_decode($str, true);
        ksort($json['players']);
        return  $json; 
        

    }


    public function getCurrentBatter($team){
        if ($_SESSION['home_current_batter'] == 0){
            $_SESSION['home_current_batter'] = 1;
        }
        if($_SESSION['road_current_batter'] == 0){
            $_SESSION['road_current_batter'] =1;
        }
        if ($team == 'homeTeam'){
            $player = $_SESSION['home_current_batter'];
            $_SESSION['home_current_batter'] = ($_SESSION['home_current_batter'] > 8) ? 1 : $_SESSION['home_current_batter']+ 1;
        }else{
            $player = $_SESSION['road_current_batter'];
            $_SESSION['road_current_batter'] = ($_SESSION['road_current_batter'] > 8) ? 1 : $_SESSION['road_current_batter']+ 1;

        }
        return $_SESSION[$team]['players'][$player];
    }

    public function getTeamPitcher(){
        $teamFuctions = $this->getTeamsFunctions();
        return $_SESSION[$teamFuctions['teamPitching']]['pitcher'];
    }

    public function getNextBatter(){
        $teamFuctions = $this->getTeamsFunctions();
        $teamBatting = $teamFuctions['teambatting'];
        if ($teamFuctions['teambatting'] == 'homeTeam'){
            $player =  ($_SESSION['home_current_batter']<9 && $_SESSION['home_current_batter'] != 0) ? $_SESSION['home_current_batter']: 1;
            
        }else if($teamFuctions['teambatting'] == 'roadTeam'){
            $player = ($_SESSION['road_current_batter']<9 && $_SESSION['road_current_batter'] != 0) ? $_SESSION['road_current_batter']: 1;

        }
        
        return $_SESSION[$teamBatting]['players'][$player];
    }

    public function getTeamsFunctions(){
        if(strpos((string)$_SESSION['inning'],'.')){
            return ["teambatting" => 'homeTeam', "teamPitching" => 'roadTeam' ];
        }else{
            return ["teambatting" => 'roadTeam', "teamPitching" => 'homeTeam' ];

        }

    }

    public function resetGame(){
        
            $_SESSION['outs'] = 0;
        
            $_SESSION['inning'] = 1;
        
            $_SESSION['runners'] = ['1b' => false,'2b' => false,'3b' => false];
        
            $_SESSION['hometeam_score'] = 0;
        
            $_SESSION['roadteam_score'] = 0;
        
            $_SESSION['home_current_batter'] = 0;
        
            $_SESSION['road_current_batter'] = 0;
        }
    
}

