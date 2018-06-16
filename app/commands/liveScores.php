<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Unirest\Request;


class liveScores extends Command {

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
	protected $description = 'Tache qui permet de récupérer les scores en direct depuis la FIFA.';

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
            $games = Game::whereRaw('date < ? && winner_id IS NULL', array(new DateTime()))->get();
            if(count($games) > 0){
                foreach($games as $value){
                    $response = Unirest\Request::get("https://api.fifa.com/api/v1/live/football/17/254645/275073/".$value->fifa_match_id."");
                    $match = $response->body;

                    if($value->team1()->first()->code == $match->HomeTeam->IdCountry && $value->team2()->first()->code == $match->AwayTeam->IdCountry) {
                        //Dans tous les cas : MAJ du score
                        $value->team1_points = $match->HomeTeam->Score;
                        $value->team2_points = $match->AwayTeam->Score;
						if($value->kick_at_goal == 1){
							$value->team1_kick_at_goal = $match->HomeTeamPenaltyScore;
							$value->team2_kick_at_goal = $match->AwayTeamPenaltyScore;
						}
                        $value->minute = $match->MatchTime;
                        $this->info('[' . $date->format('Y-m-d H:i:s') . '] MAJ scores : ' . $value->team1()->first()->name . ' ' . $value->team1_points . '-' . $value->team2_points . ' ' . $value->team2()->first()->name . '.');

                        //Si match terminé, on fige les infos et on distribue les points
                        if ($match->MatchStatus == 10 || $match->MatchStatus == 0) {
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
                                    $value->setFinished();
                                    $this->info('[' . $date->format('Y-m-d H:i:s') . '] Match fini : match nul.');
                                }
                            }
                        }

                        $value->save();
                    }else{
                        $this->error('[' . $date->format('Y-m-d H:i:s') . '] Problème sur le match ' . $value->team1()->first()->name . ' - ' . $value->team2()->first()->name . ', les équipes correpondent pas avec la FIFA.');
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
