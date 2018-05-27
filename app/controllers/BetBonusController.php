<?php
/**
 * Controlleur permetant la gestion des paris bonus
 *
 * PHP version 5.5
 *
 * @category   Controller
 * @package    worldcup\app\controllers
 * @author     Clément Hémidy <clement@hemidy.fr>, Fabien Côté <fabien.cote@me.com>
 * @copyright  2014 Clément Hémidy, Fabien Côté
 * @version    1.0
 * @since      0.1
 */


class BetBonusController extends BaseController {


    /**
     * Renvoi tout les paris en JSON
     *
     * @return Response
     */
    public function index()
    {
        $user = User::getUserWithToken($_GET['token']);

        $bet_bonus = new BetBonus();
        $_GET['user_id'] = $user->id;

        return Response::json(
            array('success' => true,
                'payload' => $this->query_params($bet_bonus)->toArray(),
            ));
    }

    /**
     * Renvoi un paris
     *
     * @return Response
     */
    public function show($id)
    {

        $user = User::getUserWithToken($_GET['token']);

        return Response::json(
            array('success' => true,
                'payload' => BetBonus::with('game')->whereRaw('user_id = ? && id = ?', array($user->id), $id)->toArray(),
            ));
    }

    /**
     * Enregistre/modifie un pari bonus
     *
     * @return Response
     */
    public function store()
    {
        $user = User::getUserWithToken($_GET['token']);

        $input = Input::all();
        $input['user_id'] = $user->id;

        $validator = Validator::make($input, BetBonus::$rules, BaseController::$messages);

        if ($validator->fails())
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => $this->errorsArraytoString($validator->messages())
                ),
                400);

        //On vérifie si la date du match n'est pas dépassé
        if(new DateTime() > new DateTime(BetBonusType::find($input['bbt_id'])->date))
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => "Le date du pari est dépassée !"
                ),
                400);

        $bet = BetBonus::whereRaw('user_id = ? && bbt_id = ?', array($input['user_id'], $input['bbt_id']))->first();
        //Si un paris sur le même pari bonus pour cet utilisateur existe, erreur envoyée.
        if($bet) {
            $bet->team_id = $input['team_id'];
            $bet->save();
        }else
            $bet = BetBonus::create($input);

        return Response::json(
            array('success' => true,
                'payload' => $bet->toArray(),
                'message' => 'Pari bonus enregistré'
            ));
    }
}