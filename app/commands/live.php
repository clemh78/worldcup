<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Unirest\Request;


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

    //ID compet et saison FIFA
    private $ID_COMPETITION = 17;
    private $ID_SEASON = 254645;

    //Renvoi l'ID fifa pour un ID kupi
    private function getStageId($idStage){
        if($idStage == null)
            return 275073;
        else if($idStage == 5)
            return 275099;
        else if($idStage == 4)
            return 275093;
        else if($idStage == 3)
            return 275095;
        else if($idStage == 2)
            return 275097;
        else if($idStage == 1)
            return 275101;
    }

    /**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        //Ouverture WS
        $options = array(
            'cluster' => 'eu',
            'encrypted' => true
        );
        $pusher = new Pusher\Pusher(
            Config::get('app.pusher_app_key'),
            Config::get('app.pusher_app_secret'),
            Config::get('app.pusher_app_id'),
            $options
        );

        while(true){
            //On récupère la liste des utilisateurs connecté sur les web-sockets
            $response = $pusher->get( '/channels/presence-users/users' );
            $users = json_decode($response['body'])->users;

            $date = new DateTime();
            $games = Game::whereRaw('date < ? && status != "completed"', array(new DateTime()))->get();
            if(count($games) > 0){
                foreach($games as $value){
                    $oldStatus = $value->status;

                    $response = Unirest\Request::get("https://api.fifa.com/api/v1/live/football/".$this->ID_COMPETITION."/".$this->ID_SEASON."/".$this->getStageId($value->stage_id)."/".$value->fifa_match_id."");
                    $match = $response->body;

                    if($value->team1()->first()->code == $match->HomeTeam->IdCountry && $value->team2()->first()->code == $match->AwayTeam->IdCountry) {
                        if ($match->MatchStatus == 10 || $match->MatchStatus == 0 || $match->MatchStatus == 3) {
                            $value->status = "in_progress";
                            //Dans tous les cas : MAJ du score
                            $value->team1_points = $match->HomeTeam->Score;
                            $value->team2_points = $match->AwayTeam->Score;
                            if ($value->kick_at_goal == 1) {
                                $value->team1_kick_at_goal = $match->HomeTeamPenaltyScore;
                                $value->team2_kick_at_goal = $match->AwayTeamPenaltyScore;
                            }
                            $value->time = $match->MatchTime;
                            if($match->Period == 4)
                                $value->time = "half-time";
                            $this->info('[' . $date->format('Y-m-d H:i:s') . '] MAJ scores : ' . $value->team1()->first()->name . ' ' . $value->team1_points . '-' . $value->team2_points . ' ' . $value->team2()->first()->name . ' ('.$value->time.').');
                        }
                        //Si match terminé, on fige les infos et on distribue les points
                        if ($match->MatchStatus == 10 || $match->MatchStatus == 0) {
                            $value->time = "full-time";
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

                        //Le match vient de commencer
                        if($oldStatus == "future" && $value->status == "in_progress"){
                            $matchJson = (object) [
                                "id" => $value->id,
                                "time" => $value->time
                            ];

                            foreach($users as $user){
                                $pusher->trigger('private-user-'.$user->id, 'start', $matchJson);
                            }
                        }

                        //match en cours
                        if($oldStatus == "in_progress" && $value->status == "in_progress"){
                            $matchJson = (object) [
                                "id" => $value->id,
                                "time" => $value->time,
                                "team1_points" => $value->team1_points,
                                "team2_points" => $value->team2_points,
                                "team1_kick_at_goal" => $value->team1_kick_at_goal,
                                "team2_kick_at_goal" => $value->team2_kick_at_goal,
                            ];

                            foreach($users as $user){
                                $pusher->trigger('private-user-'.$user->id, 'progress', $matchJson);
                            }
                        }

                        //match terminé
                        if($oldStatus != "completed" && $value->status == "completed"){
                            $matchJson = (object) [
                                "id" => $value->id,
                                "team1_points" => $value->team1_points,
                                "team2_points" => $value->team2_points,
                                "team1_kick_at_goal" => $value->team1_kick_at_goal,
                                "team2_kick_at_goal" => $value->team2_kick_at_goal,
                                "winner_id" => $value->winner_id,
                                "winner" => $value->winner,
                                "user_bet_points" => 0,
                                "user_points" => 0
                            ];

                            foreach($users as $user){

                                $token = Token::whereRaw('user_id = ?', array($user->id))->first();
                                $_GET['token'] = $token->id;
                                $userBdd = User::getUserWithToken($_GET['token']);

                                if($value->user_bet != null)
                                    $matchJson->user_bet_points = $value->user_bet->win_points;
                                $matchJson->user_points = $userBdd->winPoints;

                                $pusher->trigger('private-user-'.$user->id, 'finish', $matchJson);
                            }
                        }
                    }else{
                        $this->error('[' . $date->format('Y-m-d H:i:s') . '] Problème sur le match ' . $value->team1()->first()->name . ' - ' . $value->team2()->first()->name . ', les équipes correpondent pas avec la FIFA.');
                    }
                }
            }else
                $this->info('[' . $date->format('Y-m-d H:i:s') . '] Aucun match à surveiller.');

            sleep(10);
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
