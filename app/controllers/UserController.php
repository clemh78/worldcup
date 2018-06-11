<?php
/**
 * Controlleur permetant la gestion des utilisateurs
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


class UserController extends BaseController {


    /**
     * Renvoi tout les utilisateurs en JSON
     *
     * @return Response
     */
    public function index()
    {
        $ids = [];
        $user = User::getUserWithToken($_GET['token']);
        $rooms = $user->rooms;
        foreach($rooms as $room){
            foreach($room->users as $user) {
                $ids[] = $user->id;
            }
        }

        return Response::json(
            array('success' => true,
                'payload' => (new User())->whereIn('id', $ids)->get()->toArray(),
            ), 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * Renvoi un utilisateur
     *
     * @return Response
     */
    public function show($id)
    {

        return Response::json(
            array('success' => true,
                'payload' => User::find($id)->toArray(),
            ));
    }

    /**
     * Met à jour un bt
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $input = Input::all();
        $user = User::find($id);

        if(isset($input['password'])){
            if(Hash::check($input["oldpassword"], $user->password)){
                $input['password'] = Hash::make($input['password']);
            }else{
                return Response::json(
                    array('success' => false,
                        'payload' => array(),
                        'error' => "Le mot de passe actuel ne correspond pas"
                    ),
                    400);
            }
        }

        $validator = Validator::make($input, User::$rulesUpdate, BaseController::$messages);

        if ($validator->fails())
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => $this->errorsArraytoString($validator->messages())
                ),
                400);



        $user->fill($input);
        $user->save();

        return Response::json(
            array('success' => true,
                'payload' => $user->toArray(),
                'message' => 'Profil modifié'
            ));
    }

    /**
     * Enregistre un nouvel utilisateur
     *
     * @return Response
     */
    public function store()
    {
        if(!Config::get('app.register_on'))
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => "Inscription désactivée !"
                ),
                400);

        $input = Input::all();
        $input['password'] = Hash::make($input['password']);
        $input['role_id'] = 2;

        if (!$input['room_code'])
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => "Veuillez indiquer un salon"
                ),
                400);

        $validator = Validator::make($input, User::$rules);

        if ($validator->fails())
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => $validator->messages()
                ),
                400);

        $room = Room::where('code', '=', $input['room_code'])->first();

        if (!$room)
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => "Ce salon n'existe pas !"
                ),
                400);

        $user = User::create($input);
        $user->save();

        $user->rooms()->attach($room->id);
        $user->save();

        return Response::json(
            array('success' => true,
                'payload' => $user->toArray(),
            ));
    }

    /**
     * Enregistre un nouvel utilisateur
     *
     * @return Response
     */
    public function joinRoom()
    {
        $user = User::getUserWithToken($_GET['token']);
        $input = Input::all();

        $room = Room::where('code', '=', $input['room_code'])->first();

        if (!$room)
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => "Ce salon n'existe pas !"
                ),
                400);

        $exist = false;
        foreach($user->rooms()->get() as $roomTest){
            if($roomTest->id == $room->id)
                $exist = true;
        }

        if ($exist)
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => "Vous êtes déjà dans ce salon !"
                ),
                400);

        $user->rooms()->attach($room->id);
        $user->save();

        $user = User::getUserWithToken($_GET['token']);
        return Response::json(
            array('success' => true,
                'payload' => $user->toArray(),
                'message' => 'Vous avez intégré le salon ' . $room->name
            ));
    }

    /**
     * Enregistre un nouvel utilisateur en mode admin
     *
     * @return Response
     */
    public function storeWithoutPassword()
    {
        $input = Input::all();
        $password = $this->randomPassword();
        $input['password'] = Hash::make($password);

        $validator = Validator::make($input, User::$rules);

        if ($validator->fails())
            return Response::json(
                array('success' => false,
                    'payload' => array(),
                    'error' => $validator->messages()
                ),
                400);

        $user = User::create($input);
        $user->save();

        $newUser = User::find($user->id)->toArray();
        $newUser['password'] = $password;

        return Response::json(
            array('success' => true,
                'payload' => $newUser,
            ));
    }

    public function indexAdmin()
    {

        return Response::json(
            array('success' => true,
                'payload' => (new User())->get()->toArray(),
            ), 200, [], JSON_NUMERIC_CHECK);
    }

    private function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

}