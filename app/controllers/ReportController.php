<?php

class ReportController extends BaseController {


    /**
     * Renvoi tout les utilisateurs en JSON
     *
     * @return Response
     */
    public function index()
    {
        $date = new DateTime("2018-06-16");
        $dateMinus1 = clone $date;
        date_sub($dateMinus1, date_interval_create_from_date_string('1 days'));

        $usersTmp = User::get()->toArray();
        $usersTmp2 = array();
        $users = array();
        $usersSortByWinPointsLastDay = array();

        $posUsersBeforeDay = array();

        function cmp($a, $b)
        {
            return intval($b["winPoints"])-intval($a["winPoints"]);
        }

        function cmp2($a, $b)
        {
            return intval($b["winPointsBeforeDay"])-intval($a["winPointsBeforeDay"]);
        }

        function cmp3($a, $b)
        {
            return intval($b["winPointsLastDay"])-intval($a["winPointsLastDay"]);
        }

        foreach($usersTmp as $user){
            $user['winPointsLastDay'] = $this->getWinPointsDay($user['id'], $dateMinus1, $date);
            $user['winPointsBeforeDay'] = $this->getWinPointsBeforeDay($user['id'], $dateMinus1);
            $usersTmp2[] = $user;
        }

        usort($usersTmp2, "cmp2");

        $pos = 1;
        foreach($usersTmp2 as $user){
            $posUsersBeforeDay[$user['id']] = $pos++;
        }

        usort($usersTmp2, "cmp");

        $pos = 1;
        foreach($usersTmp2 as $user){
            $user['lastPlace'] = $posUsersBeforeDay[$user['id']];
            $user['newPlace'] = $pos;
            $user['diffPlace'] = $posUsersBeforeDay[$user['id']] - $pos++;
            $users[] = $user;
            $usersSortByWinPointsLastDay[] = $user;
        }

        usort($usersSortByWinPointsLastDay, "cmp3");

        $games = Game::whereRaw('date < ? && date > ?', array($date, $dateMinus1))->get()->toArray();

        return View::make('pdf.dailyReport', array('date' => $date, 'users' => $users, 'games' => $games, 'MVP' => $usersSortByWinPointsLastDay));
    }

    public function getWinPointsDay($user_id, $dateStart, $startEnd)
    {

        $total = DB::table('transaction')->where('user_id', '=', $user_id)->where('created_at', '<', $startEnd)->where('created_at', '>', $dateStart)->where(function($req){$req->where('type', '=', 'gain')->orWhere('type', '=', 'bonus');})->sum('value');
        $total = $total - DB::table('transaction')->where('created_at', '<', $startEnd)->where('created_at', '>', $dateStart)->where('user_id', '=', $user_id)->where(function($req){$req->where('type', '=', 'bet');})->sum('value');

        return $total;
    }

    public function getWinPointsBeforeDay($user_id, $date)
    {

        $total = DB::table('transaction')->where('user_id', '=', $user_id)->where('created_at', '<', $date)->where(function($req){$req->where('type', '=', 'gain')->orWhere('type', '=', 'bonus');})->sum('value');
        $total = $total - DB::table('transaction')->where('created_at', '<', $date)->where('user_id', '=', $user_id)->where(function($req){$req->where('type', '=', 'bet');})->sum('value');

        return $total;
    }

}