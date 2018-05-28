<?php
/**
 * Modèle de donnée des salons de joueurs
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

class Room extends Eloquent {

    /**
     * Table corespondant sur le SGBD
     *
     * @var string
     */
    protected $table = 'room';

    public $timestamps = false;

    public function toArray()
    {
        $array = parent::toArray();
        $array['nbUsers'] = $this->nbUsers;
        return $array;
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
        'code' => 'required|alpha_num|max:10',
    );

    public function users()
    {
        return $this->belongsToMany('User');
    }

    public function getNbUsersAttribute()
    {
        return count($this->users());
    }

}