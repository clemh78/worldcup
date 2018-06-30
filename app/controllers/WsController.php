<?php


class WsController extends BaseController {

    public function authChannel()
    {
        $user = User::getUserWithToken($_GET['token']);

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

        $channel_name = $_POST['channel_name'];

        if($channel_name == "presence-users"){
            echo $pusher->presence_auth($_POST['channel_name'], $_POST['socket_id'], $user->id, array('token' => $_GET['token']));
        }else{
            $id = str_replace('private-user-', '', $channel_name);
            if($user->id == $id)
                echo $pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
            else
                return Response::json(
                    array('success' => false,
                        'message' => 'Ce channel ne correspond pas à votre compte.',
                    ), 403, [], JSON_NUMERIC_CHECK);
        }
    }

}