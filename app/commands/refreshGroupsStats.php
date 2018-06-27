<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class refreshGroupsStats extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
    protected $name = 'wc:refreshGroupsStats';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Permet de recalculer l\'ensemble des stats concernant les équipes.';

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
        $teams = Team::get();

        foreach($teams as $team){
            $this->info('MAJ stats de l\'équipe '.$team->name);

            $played = 0;
            $wins = 0;
            $draws = 0;
            $losses = 0;

            $goals_for = 0;
            $goals_against = 0;
            $goals_diff = 0;

            $games = Game::whereRaw('(team1_id = ? OR team2_id = ?) AND stage_id IS NULL AND status = ?', array($team->id, $team->id, 'completed'))->get();

            foreach($games as $game) {
                $played++;

                if($game->winner_id != null){
                    if($game->winner_id == $team->id)
                        $wins++;
                    else
                        $losses++;
                }

                if($game->winner_id == null)
                    $draws++;

                if($game->team1_id == $team->id){
                    $goals_for = $goals_for + $game->team1_points;
                    $goals_against = $goals_against + $game->team2_points;
                }else{
                    $goals_for = $goals_for + $game->team2_points;
                    $goals_against = $goals_against + $game->team1_points;
                }
            }

            $points = ($wins * 3) + $draws;
            $goals_diff = $goals_for - $goals_against;

            $team->games_played = $played;
            $team->wins = $wins;
            $team->draws = $draws;
            $team->losses = $losses;
            $team->goals_for = $goals_for;
            $team->goals_against = $goals_against;
            $team->goals_diff = $goals_diff;
            $team->points = $points;
            $team->save();
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
