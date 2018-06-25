<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class setWinnersOfGroup extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'wc:setWinnersOfGroup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Permet de définir les deux premiers de chaque groupe.';

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
        $group_code = $this->argument('group_code');
        $position = $this->argument('position');
        $team_code = $this->argument('team_code');

        $group = Group::whereRaw("code = ?", array($group_code))->first();

        if($group){
            if($position == 1 || $position == 2){
                $team = Team::whereRaw("code = ?", array($team_code))->first();

                if($team){

                    if($team->group_id != $group->id)
                        $this->error('Cette équipe n\'est pas dans ce groupe !');
                    else{
                        $groupTmp = Group::whereRaw("winner_id = ?", array($team->id))->first();
                        if($groupTmp){
                            $groupTmp->winner_id = null;
                            $groupTmp->save();
                        }

                        $groupTmp = Group::whereRaw("runnerup_id = ?", array($team->id))->first();
                        if($groupTmp) {
                            $groupTmp->runnerup_id = null;
                            $groupTmp->save();
                        }

                        if($position == 1)
                            $group->winner_id = $team->id;

                        if($position == 2)
                            $group->runnerup_id = $team->id;

                        $group->save();

                        $this->info('Equipe ' . $team->name . ' définie ' . (($position == 1)?'première':'seconde') . ' du ' . $group->name);
                    }
                }else{
                    $this->error('L\'équipe indiquée n\'existe pas !');
                }
            }else{
                $this->error('Cette position n\'existe pas (1 ou 2) !');
            }
        }else{
            $this->error('Le groupe indiqué n\'existe pas !');
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('group_code', InputArgument::REQUIRED, 'Lettre représentant le group. (A, B, C ...)'),
            array('position', InputArgument::REQUIRED, 'Premier ou deuxième place à définir (1 ou 2)'),
            array('team_code', InputArgument::REQUIRED, 'Code représentant le pays à définir.'),
		);
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
