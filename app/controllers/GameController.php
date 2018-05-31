<?php
/**
 * Controlleur permetant la gestion des matches
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


class GameController extends BaseController {


    /**
     * Renvoi tout les matches en JSON
     *
     * @return Response
     */
    public function index()
    {
        return Response::json(
            array('success' => true,
                'payload' => $this->query_params(new Game())->toArray(),
            ));
    }

    /**
     * Renvoi un matche
     *
     * @return Response
     */
    public function show($id)
    {
        return Response::json(
            array('success' => true,
                'payload' => Game::find($id)->toArray(),
            ));
    }

    public function getBets($id){
        $ids = [];

        if(new DateTime() > new DateTime(Game::find($id)->date)){
            $user = User::getUserWithToken($_GET['token']);
            $rooms = $user->rooms;
            foreach($rooms as $room){
                foreach($room->users as $user) {
                    $ids[] = $user->id;
                }
            }
        }

        return Response::json(
            array('success' => true,
                'payload' => Bet::whereRaw('game_id = ?', array($id))->whereIn('user_id', $ids)->get()->toArray(),
            ));
    }

}