<?php
/**
 * Modèle de donnée des transactions
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

class Transaction extends Eloquent {

    /**
     * Table corespondant sur le SGBD
     *
     * @var string
     */
    protected $table = 'transaction';

    /**
     * Tableau indiquant les sous élements à imbriquer
     *
     * @var array
     */
    protected $with = array('game');

    /**
     * Récupère l'objet Bet indiqué dans cette transaction
     *
     * @var Stage
     */
    public function game()
    {
        return $this->belongsTo('Game', 'game_id', 'id');
    }

    /**
     * Définition des règles de vérifications pour les entrées utilisateurs et le non retour des erreur mysql
     *
     * @var array
     */
    public static $rules = array(
        'user_id' => 'exists:stage,id',
        'game_id' => 'exists:game,id',
        'value' => 'integer',
        'type' => 'in:bet,gain,bonus',
    );

    /**
     * Liste des champs qui peuvent servir de filter dans l'url
     *
     * @var array
     */
    public $filters = array('user_id');

    /**
     * Ajoute une transaction
     * Utilisé uniquement par le code
     *
     * @param $user_id
     * @param $game_id
     * @param $value
     * @param $type
     * @return bool
     */
    public static function addTransaction($user_id, $game_id, $value, $type, $desc){
        if(in_array($type, array('gain', 'bet', 'bonus')) && Game::find($game_id)){
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->game_id = $game_id;
            $transaction->value = $value;
            $transaction->type = $type;
            $transaction->desc = $desc;

            $transaction->save();
        }

        return false;
    }
}