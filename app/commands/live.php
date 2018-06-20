<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class live extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'wc:live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tache qui permet de récupérer les scores en direct.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        while(true){
            $date = new DateTime();
            $games = Game::whereRaw('date < ? && status != "completed"', array(new DateTime()))->get();
            if(count($games) > 0){
                $response = Unirest\Request::get("http://worldcup.sfg.io/matches");

                foreach($games as $value){
                    foreach($response->body as $gamesWS) {
                        //Var locales
                        $id_fifa = $gamesWS->fifa_id;
                        $home_team = $gamesWS->home_team;
                        $away_team = $gamesWS->away_team;
                        $time = $gamesWS->time;
                        $status = $gamesWS->status;

                        //Si c'est notre match !
                        if($value->fifa_match_id == $id_fifa){

                            //Si c'est bien les équipes de notre match
                            if($home_team->code == $value->team1()->first()->code && $away_team->code == $value->team2()->first()->code){

                                if($status == "in progress" || $status == "completed"){
                                    //Dans tous les cas : MAJ du score
                                    $value->team1_points = $home_team->goals;
                                    $value->team2_points = $away_team->goals;
                                    /*if($value->kick_at_goal == 1){
                                        $value->team1_kick_at_goal = $match->HomeTeamPenaltyScore;
                                        $value->team2_kick_at_goal = $match->AwayTeamPenaltyScore;
                                    }*/
                                    $value->time = $time;
                                    $value->status = "in_progress";
                                    $this->info('[' . $date->format('Y-m-d H:i:s') . '] MAJ scores : ' . $value->team1()->first()->name . ' ' . $value->team1_points . '-' . $value->team2_points . ' ' . $value->team2()->first()->name . '.');
                                }

                                //Si match terminé, on fige les infos et on distribue les points
                                if ($status == "completed") {
                                    if ($value->team1_points > $value->team2_points) {
                                        $value->setFinished(1);
                                        $this->info('[' . $date->format('Y-m-d H:i:s') . '] Match fini : ' . $value->team1()->first()->name . ' gagnant.');
                                    } else if ($value->team1_points < $value->team2_points) {
                                        $value->setFinished(2);
                                        $this->info('[' . $date->format('Y-m-d H:i:s') . '] Match fini : ' . $value->team2()->first()->name . ' gagnant.');
                                    } else if($value->team1_points == $value->team2_points){
                                        if($value->kick_at_goal == 1){
                                            if($value->team1_kick_at_goal > $value->team2_kick_at_goal){
                                                $value->setFinished(1);
                                                $this->info('[' . $date->format('Y-m-d H:i:s') . '] Match fini : '.$value->team1()->first()->name.' gagnant.');
                                            }else{
                                                $value->setFinished(2);
                                                $this->info('[' . $date->format('Y-m-d H:i:s') . '] Match fini : '.$value->team2()->first()->name.' gagnant.');
                                            }
                                        }
                                        else{
                                            $value->setFinished(null);
                                            $this->info('[' . $date->format('Y-m-d H:i:s') . '] Match fini : match nul.');
                                        }
                                    }
                                }

                                $value->save();
                            }else{
                                $this->error('[' . $date->format('Y-m-d H:i:s') . '] Problème sur le match ' . $value->team1()->first()->name . ' - ' . $value->team2()->first()->name . ', les équipes correpondent pas avec la FIFA.');
                            }
                        }
                    }
                }
            }else
                $this->info('[' . $date->format('Y-m-d H:i:s') . '] Aucun match à surveiller.');

            sleep(50);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
