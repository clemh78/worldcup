/**
 * Services angularJS
 *
 * AngularJS version 1.2.0
 *
 * @category   angular controller
 * @package    worldcup\public\js
 * @author     Clément Hémidy <clement@hemidy.fr>, Fabien Côté <fabien.cote@me.com>
 * @copyright  2014 Clément Hémidy, Fabien Côté
 * @version    1.0
 * @since      0.1
 */


angular.module('services', [])

    .factory('serviceUser', function($http, $rootScope) {
        return {

            authorize: function(accessLevel, role) {
                if(role === undefined)
                    if($rootScope.user != null)
                        role = $rootScope.user.role.access_level;
                    else
                        role = userRoles.public;

                return accessLevel & role;
            },

            getUser : function(id, token) {
                return $http.get('/api/users/' + id + '?token=' + token);
            },

            getRanking : function(token) {
              return $http.get('/api/users?token=' + token /*+ '&orderby=points&order=DESC'*/);
            },

            login : function(login, pass) {
                return $http({
                    method: 'POST',
                    url: '/api/users/login',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"login" : login, "password" : pass})
                });
            },

            logout : function(token) {
                return $http.get('/api/users/logout?token=' + token);
            },

            register : function(login, pass, firstname, lastname, code, email) {
                return $http({
                    method: 'POST',
                    url: 'api/users',
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"login" : login, "password" : pass, 'firstname' : firstname, 'lastname' : lastname, 'room_code' : code, 'email' : email})
                });
            },

            join : function(token, room_code) {
                return $http({
                    method: 'POST',
                    url: 'api/users/join?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"room_code" : room_code})
                });
            },

            update : function(token,userId,userData) {
                return $http({
                    method: 'PUT',
                    url: 'api/users/' + userId + '?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param(userData)
                })
            }

        }
    })

    .factory('serviceAdmin', function($http) {
        return {
            loginWithoutPassword : function(token, login) {
                return $http({
                    method: 'POST',
                    url: '/api/admin/login?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"login" : login})
                });
            },

            registerWithoutPassword : function(token, login, role_id, firstname, lastname) {
                return $http({
                    method: 'POST',
                    url: 'api/admin/register?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"login" : login, "role_id" : role_id, 'firstname' : firstname, 'lastname' : lastname})
                });
            },

            getUsers : function(token) {
                return $http.get('/api/admin/users?token=' + token);
            },
        }
    })

    .factory('serviceGame', function($http) {
        return {
            GetNext : function(token, gameId){
                return $http.get('api/games/'+gameId+'?token=' + token + '');
            },
            GetNext : function(token){
                return $http.get('api/games?token=' + token + '&finished=0&team1_id!=null&team2_id!=null&orderby=date&order=ASC');
            },
            GetPrevious : function(token){
                return $http.get('api/games?token=' + token + '&finished=1&orderby=date&order=DESC');
            },
            GetBets : function(token, gameId){
                return $http.get('api/games/' + gameId + '/bets?token=' + token);
            }
        }
    })

    .factory('serviceTransaction', function($http) {
        return {
            GetTransactions : function(token){
                return $http.get('api/transactions?token=' + token + '&orderby=updated_at&order=DESC');
            }
        }
    })

    .factory('serviceBracket', function($http) {
        return {
            GetBracket : function(token){
                return $http.get('api/bracket?token=' + token);
            }
        }
    })

    .factory('serviceGroup', function($http) {
        return {
            getGroups : function(token){
                return $http.get('api/groups?token=' + token);
            }
        }
    })

    .factory('serviceRoom', function($http) {
        return {
            create : function(token, name, code) {
                return $http({
                    method: 'POST',
                    url: 'api/rooms?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"name" : name, "code" : code})
                });
            },
        }
    })

    .factory('serviceTeam', function($http) {
        return {
            getTeams : function(token){
                return $http.get('api/teams?token=' + token);
            }
        }
    })

    .factory('serviceBetBonusType', function($http) {
        return {
            getBbts : function(token){
                return $http.get('api/bbts?token=' + token);
            }
        }
    })

    .factory('serviceRole', function($http) {
        return {
            getRoles : function(token){
                return $http.get('api/roles?token=' + token);
            }
        }
    })

    .factory('serviceBet', function($http) {
        return {
            placeBet : function(token, userId, gameId, winnerId, team1_points, team2_points){
                return $http({
                   method: 'POST',
                    url: 'api/bets?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"user_id" : userId, "game_id" : gameId, "team1_points" : team1_points, "team2_points" : team2_points, "winner_id" : winnerId})
                });
            },
            updateBet : function(token, betId, winnerId, team1_points, team2_points){
                return $http({
                    method: 'PUT',
                    url: 'api/bets/'+betId+'?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"team1_points" : team1_points, "team2_points" : team2_points, "winner_id" : winnerId})
                });
            },
            GetBet : function(token, gameId){
                return $http.get('api/bets?token=' + token + '&game_id=' + gameId);
            }
        }
    })

    .factory('serviceBonusBet', function($http) {
        return {

            GetBonusBets : function(token){
                return $http.get('api/bets_bonus?token=' + token);
            },
            storeBet : function(token, userId, bbtId, teamId){
                return $http({
                    method: 'POST',
                    url: 'api/bets_bonus?token=' + token,
                    headers: { 'Content-Type' : 'application/x-www-form-urlencoded' },
                    data: $.param({"user_id" : userId, "bbt_id" : bbtId, "team_id" : teamId})
                });
            },
        }
    })