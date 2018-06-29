/**
 * Controlleur des matchs
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

angular.module('gamesController', [])

    .controller('gamesControllerList', ["$scope", "$rootScope", "$timeout", "$cookies", "$modal", "$filter", "games", "gamesPrevious", "bracket", "groups", "serviceGame", function($scope, $rootScope, $timeout, $cookies, $modal, $filter, games, gamesPrevious, bracket, groups, Game) {
        $scope.games = games.data;
        $scope.gamesPrevious = gamesPrevious.data;
        $scope.groups = groups.data;

        $("#rounds").gracket({
            src : bracket.data['rounds'],
            cornerRadius : 10,
            canvasLineGap : 10,
            roundLabels : bracket.data['labels']
        });

        $("#third").gracket({
            src : bracket.data['third'],
            cornerRadius : 10,
            canvasLineGap : 10
        });

        // add some labels
        $("#bracket .secondary-bracket .g_winner")
            .parent()
            .css("position", "relative")
            .prepend("<h4>3ème place</h4>")

        $("#bracket > div").eq(0).find(".g_winner")
            .parent()
            .css("position", "relative")
            .prepend("<h4>Gagnant</h4>")

        $('#groups').hide();
        $('#bracket').hide();
        $('#gamesPrevious').hide();
        $('#games').show();

        $scope.betColor = function(game, teamDisplay){

            if(!game.user_bet)
                return null;
            
            if(game.user_bet.team1_points == game.user_bet.team2_points && game.user_bet.winner_id != null) {
                if (game.user_bet.winner_id == game.team1_id && teamDisplay == 1)
                    return 'btn-success';

                if (game.user_bet.winner_id == game.team2_id && teamDisplay == 2)
                    return 'btn-success';

                return 'btn-warning';
            }

            if(teamDisplay == 1)
                return (game.user_bet.team1_points>game.user_bet.team2_points) ? 'btn-success' : ((game.user_bet.team1_points<game.user_bet.team2_points) ? 'btn-danger' : 'btn-warning');
            else
                return (game.user_bet.team2_points>game.user_bet.team1_points) ? 'btn-success' : ((game.user_bet.team2_points<game.user_bet.team1_points) ? 'btn-danger' : 'btn-warning');
        }

        $scope.filterList = function(){
            $('#filter-gamesPrevious').parent('li').removeClass('active');
            $('#filter-bracket').parent('li').removeClass('active');
            $('#filter-groups').parent('li').removeClass('active');
            $('#filter-list').parent('li').addClass('active');
            $('.bracket-header').hide();
            $('#bracket').hide();
            $('.game-header').show();
            $('#games').show();
            $('.game-previous-header').hide();
            $('#gamesPrevious').hide();
            $('#groups').hide();
            $('.groups-header').hide();
        };

        $scope.filterBracket = function(){
            $('#filter-list').parent('li').removeClass('active');
            $('#filter-gamesPrevious').parent('li').removeClass('active');
            $('#filter-groups').parent('li').removeClass('active');
            $('#filter-bracket').parent('li').addClass('active');
            $('.game-header').hide();
            $('#games').hide();
            $('.bracket-header').show();
            $('#bracket').show();
            $('.game-previous-header').hide();
            $('#gamesPrevious').hide();
            $('#groups').hide();
            $('.groups-header').hide();
        };

        $scope.filterGamesPrevious = function(){
            $('#filter-list').parent('li').removeClass('active');
            $('#filter-bracket').parent('li').removeClass('active');
            $('#filter-groups').parent('li').removeClass('active');
            $('#filter-gamesPrevious').parent('li').addClass('active');
            $('.game-header').hide();
            $('#games').hide();
            $('.bracket-header').hide();
            $('#bracket').hide();
            $('.game-previous-header').show();
            $('#gamesPrevious').show();
            $('#groups').hide();
            $('.groups-header').hide();
        };

        $scope.filterGroups = function(){
            $('#filter-gamesPrevious').parent('li').removeClass('active');
            $('#filter-bracket').parent('li').removeClass('active');
            $('#filter-groups').parent('li').addClass('active');
            $('#filter-list').parent('li').removeClass('active');
            $('.bracket-header').hide();
            $('#bracket').hide();
            $('.game-header').hide();
            $('#games').hide();
            $('.game-previous-header').hide();
            $('#gamesPrevious').hide();
            $('#groups').show();
            $('.groups-header').show();
        };


        $scope.open = function (game) {
            $modal.open({
                templateUrl: '/views/partials/gameInfo.html?v=' + VERSION,
                controller: 'gamesControllerModalInstance',
                resolve: {
                    game: function(){
                        return game;
                    },
                    bets: [ "serviceGame", "$cookies", function(Game, $cookies){
                        return Game.GetBets($cookies['token'], game.id);
                    }]
                }
            });
        };


        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher(PUSHER_APP_KEY, {
            cluster: 'eu',
            encrypted: true,
            authEndpoint: '/api/ws/auth?token=' + $cookies['token']
        });

        var channel = pusher.subscribe('private-user-' + $cookies['user_id']);
        var channelPresence = pusher.subscribe('presence-users');

        channel.bind('start', function(data) {

            object = $filter('filter')($scope.games, { id: parseInt(data.id) }, true)[0];

            object.status = "in_progress";
            object.time = data.time;

            object.team1_points = 0;
            object.team2_points = 0;
            object.team1_kick_at_goal = 0;
            object.team2_kick_at_goal = 0;

            $scope.$apply();
        });

        channel.bind('progress', function(data) {

            object = $filter('filter')($scope.games, { id: parseInt(data.id) }, true)[0];

            object.status = "in_progress";
            object.time = data.time;

            object.team1_points = data.team1_points;
            object.team2_points = data.team2_points;
            object.team1_kick_at_goal = data.team1_kick_at_goal;
            object.team2_kick_at_goal = data.team2_kick_at_goal;

            $scope.$apply();
        });


        channel.bind('finish', function(data) {

            object = $filter('filter')($scope.games, { id: parseInt(data.id) }, true)[0];
            $scope.games.splice($scope.games.indexOf(object), 1);

            $scope.gamesPrevious.push(object);

            objectNew = $filter('filter')($scope.gamesPrevious, { id: parseInt(data.id) }, true)[0];

            objectNew.status = "completed";
            objectNew.time = "full-time";
            objectNew.team1_points = data.team1_points;
            objectNew.team2_points = data.team2_points;
            objectNew.team1_kick_at_goal = data.team1_kick_at_goal;
            objectNew.team2_kick_at_goal = data.team2_kick_at_goal;
            objectNew.winner_id = data.winner_id;
            objectNew.winner = data.winner;
            objectNew.user_bet.win_points = data.user_bet_points;

            $rootScope.user.winPoints = data.user_points;

            $scope.$apply();
        });


        $scope.$on("$destroy", function() {
            pusher.disconnect();
        });
    }])


    .controller('gamesControllerModalInstance', ["$scope", "$modalInstance", "$cookies", "game", "bets", function ($scope, $modalInstance, $cookies, game, bets) {
        $scope.game = game;

        $scope.teams = [game.team1, game.team2];

        $scope.bets = bets.data;

        $scope.selector = $scope.user.rooms[0].id;

        $scope.select = function(id){
            $scope.selector = id;
        };

        $scope.showBet = function(bet){
            inRoom = false;

            angular.forEach(bet.user.rooms, function(room, key) {
                if(room.id == $scope.selector)
                    inRoom = true;
            });

            return inRoom;
        };

        $scope.gameDateIsBeforeNow = function(){
            now = moment();

            return now.isAfter(moment($scope.game.date));
        };
		
		//Affinement du tri des scores des participants 
		// en prennant en compte les écarts au but 
		// score * 100 - la valeure absolue des ecarts de points entre le pari et le réel
		$scope.detailedOrderedScore = function(bet) {
		   return -(bet.win_points*10000 - ((Math.abs($scope.game.team1_points - bet.team1_points) + Math.abs($scope.game.team2_points - bet.team2_points))*100 - bet.team1_points));
		};
		

        //Si MAJ du match en live, MAJ de l'interface
        $scope.$on('game_'+$scope.game.id, function(event, args) {

            var game = args;
            $scope.game = game;
        });

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    }])

    .controller('helpControllerModal', function ($scope, $modal) {

        $scope.open = function () {
            $modal.open({
                templateUrl: '/views/partials/help.html?v=' + VERSION,
                controller: 'helpControllerModalInstance',
                resolve: {
                }
            });
        };
    })


    .controller('helpControllerModalInstance', ["$scope", "$modalInstance", "$cookies", function ($scope, $modalInstance, $cookies) {

    }]);