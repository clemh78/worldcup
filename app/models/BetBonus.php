<?php
/**
 * Modèle de donnée des paris bonus
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

class BetBonus extends Eloquent {

    /**
     * Table corespondant sur le SGBD
     *
     * @var string
     */
    protected $table = 'bet_bonus';

    /**
     * Tableau indiquant les sous élements à imbriquer
     *
     * @var array
     */
    protected $with = array('user');

    /**
     * Liste des champs assignable en masse
     *
     * @var array
     */
    protected $fillable = array('user_id', 'bbt_id', 'team_id');

    /**
     * Liste des champs qui peuvent servir de filter dans l'url
     *
     * @var array
     */
    public $filters = array('bbt_id', 'user_id');

    /**
     * Table corespondant au champ caché sur les retours JSON
     *
     * @var array
     */
    protected $hidden = array('updated_at');

    /**
     * Récupère l'objet Match indiqué
     *
     * @var Stage
     */
    public function bbt()
    {
        return $this->belongsTo('BetBonusType', 'bbt_id', 'id');
    }

    /**
     * Récupère l'objet User
     *
     * @var Stage
     */
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    /**
     * Définition des règles de vérifications pour les entrées utilisateurs et le non retour des erreur mysql
     *
     * @var array
     */
    public static $rules = array(
        'user_id' => 'exists:user,id',
        'bbt_id' => 'exists:bet_bonus_type,id',
        'team_id' => 'exists:team,id',
    );
}