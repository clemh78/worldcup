<?php
/**
 * Modèle de donnée des groupes du tournoi
 *
 * PHP version 5.5
 *
 * @category   Modèles
 * @package    worldcup\app\models
 * @author     Clément Hémidy <clement@hemidy.fr>, Fabien Côté <fabien.cote@me.com>
 * @copyright  2014 Clément Hémidy, Fabien Côté
 * @version    1.0
 * @since      0.1
 */

class Group extends Eloquent {

    /**
     * Table corespondant sur le SGBD
     *
     * @var string
     */
    protected $table = 'group';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        //Si le champ winner ou runnerup est renseigné, on assigne l'équipe dans l'arbre
        User::saving(function($group)
        {
            if($group->winner_id != null){
                $code = '1'.$group->code;

                $game = Game::whereRaw('team1_tmp_name = ? || team2_tmp_name = ?', array($code, $code))->first();
                if($game->team1_tmp_name == $code)
                    $game->team1_id = $group->winner_id;
                else if($game->team2_tmp_name == $code)
                    $game->team2_id = $group->winner_id;
                $game->save();
            }

            if($group->runnerup_id != null){
                $code = '2'.$group->code;

                $game = Game::whereRaw('team1_tmp_name = ? || team2_tmp_name = ?', array($code, $code))->first();
                if($game->team1_tmp_name == $code)
                    $game->team1_id = $group->runnerup_id;
                else if($game->team2_tmp_name == $code)
                    $game->team2_id = $group->runnerup_id;
                $game->save();
            }
        });
    }


    /**
     * Table corespondant au champ caché sur les retours JSON
     *
     * @var array
     */
    protected $hidden = array('created_at', 'updated_at');

    /**
     * Définition des règles de vérifications pour les entrées utilisateurs et le non retour des erreur mysql
     *
     * @var array
     */
    public static $rules = array(
        'name' => 'required|alpha_num|max:255',
    );

    protected $with = array('teams');

    /**
     * Récupère toutes les équipe du groupe
     *
     * @var Team[]
     */
    public function teams()
    {
        return $this->hasMany('Team', 'group_id', 'id');
    }


}