/**
 * Controlleur des paris
 *
 * AngularJS version 1.2.0
 *
 * @category   angular controller
 * @package    worldcup\public\js\controllers
 * @author     Clément Hémidy <clement@hemidy.fr>, Fabien Côté <fabien.cote@me.com>
 * @copyright  2014 Clément Hémidy, Fabien Côté
 * @version    1.0
 * @since      0.1
 */

angular.module('betsController', [])

    .controller('betsControllerModal', function ($scope, $modal) {

        $scope.open = function (game, user) {
            $modal.open({
                templateUrl: '/views/partials/betForm.html',
                controller: 'betsControllerModalInstance',
                resolve: {
                    game: function(){
                        return game;
                    },
                    user: function(){
                        return user;
                    },
                    bet: [ "serviceBet", "$cookies", function(Bet, $cookies){
                        return Bet.GetBet($cookies['token'], game.id);
                    }]
                }
            });
        };
    })

    .controller('betsControllerModalInstance', ["$scope", "$modalInstance", "$cookies", "game", "user", "serviceBet", "bet" , function ($scope, $modalInstance, $cookies, game, user, Bet, bet) {
        $scope.game = game;

        if(bet.data[0] != undefined){
            $scope.bet = bet.data[0];
        }else{
            $scope.bet = {};
        }

        $scope.teams = [game.team1, game.team2];

        if($scope.game.kick_at_goal == 0)
            $scope.teams.push({id: null, name: "- Match nul -", code: "NULL"});

        $scope.ok = function () {
            if(bet.data[0] == undefined){
                $modalInstance.close(
                    Bet.placeBet($cookies['token'], user.id, game.id, $scope.bet.winner_id, $scope.bet.team1_points, $scope.bet.team2_points)
                        .success(function() {
                            $scope.game.user_has_bet = true;
                        })
                );
            }else{
                $modalInstance.close(
                    Bet.updateBet($cookies['token'], $scope.bet.id, $scope.bet.winner_id, $scope.bet.team1_points, $scope.bet.team2_points)
                        .success(function(data) {
                            $scope.game.user_has_bet = true;
                        })
                );
            }
        };

        $scope.updateWinner = function(){
            console.log($scope.bet);
            if($scope.bet.team1_points > $scope.bet.team2_points){
                $scope.bet.winner_id = $scope.game.team1.id;
            }
            else if($scope.bet.team1_points < $scope.bet.team2_points){
                $scope.bet.winner_id = $scope.game.team2.id;
            }
            else{
                $scope.bet.winner_id = null;
            }
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    }])

    .controller('betBonusControllerModal', function ($scope, $modal) {

        $scope.open = function () {
            $modal.open({
                templateUrl: '/views/partials/betBonus.html',
                controller: 'betBonusControllerModalInstance',
                resolve: {
                    bbts: [ "serviceBetBonusType", "$cookies", function(BetBonusType, $cookies){
                        return BetBonusType.getBbts($cookies['token']);
                    }],
                    teams: [ "serviceTeam", "$cookies", function(Team, $cookies){
                        return Team.getTeams($cookies['token']);
                    }],
                    bets: [ "serviceBonusBet", "$cookies", function(BonusBet, $cookies){
                        return BonusBet.GetBonusBets($cookies['token']);
                    }]
                }
            });
        };
    })

    .controller('betBonusControllerModalInstance', ["$scope", "$rootScope", "$modalInstance", "$cookies", "bbts" , "teams", "bets", "serviceBonusBet", function ($scope, $rootScope, $modalInstance, $cookies, bbts, teams, bets, BonusBet) {
        $scope.bbts = bbts.data;
        $scope.teams = teams.data;
        $scope.bets_bonus = [];

        angular.forEach(bets.data, function(value, key) {
            console.log(value);
            $scope.bets_bonus[value.bbt_id] = value.team_id;
        });

        $scope.updateBet = function(bbt_id){
            BonusBet.storeBet($cookies['token'], $rootScope.id, bbt_id, $scope.bets_bonus[bbt_id])
                .success(function() {
                })
        }

        $scope.isActive = function(bbt){
            now = moment();

            return now.isBefore(moment(bbt.date));
        }
    }]);

