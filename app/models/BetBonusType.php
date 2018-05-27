<?php
/**
 * Modèle de donnée des types de pari bonus
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

class BetBonusType extends Eloquent {

    /**
     * Table corespondant sur le SGBD
     *
     * @var string
     */
    protected $table = 'bet_bonus_type';

    public $timestamps = false;


    /**
     * Table corespondant au champ caché sur les retours JSON
     *
     * @var array
     */
    protected $hidden = array('created_at', 'updated_at', 'trigger_data_type', 'trigger_data_id', 'trigger_points', 'trigger_condition');

    /**
     * Définition des règles de vérifications pour les entrées utilisateurs et le non retour des erreur mysql
     *
     * @var array
     */
    public static $rules = array(
        'label' => 'required|alpha_num|max:255',
    );
}