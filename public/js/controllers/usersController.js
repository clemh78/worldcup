/**
 * Controlleur des utilisateurs
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

angular.module('usersController', [])

    .controller('usersControllerModal', ["$scope", "serviceUser", "$cookies", "$modal", function($scope, User, $cookies, $modal) {

        $scope.ranking = function(){
            $modal.open({
                templateUrl: '/views/partials/rankingList.html?v=' + VERSION,
                controller: 'usersControllerListModalInstance',
                size: 'lg',
                resolve: {
                    users: [ "serviceUser", "$cookies", function(User, $cookies){
                        return User.getRanking($cookies['token']);
                    }]
                }
            });
        };

        $scope.account = function(user){
            $modal.open({
                templateUrl:'/views/partials/accountForm.html?v=' + VERSION,
                controller: 'usersControllerAccountModalInstance',
                resolve:{
                    user: function(){
                        return user;
                    }
                }
            });
        };

        $scope.room = function(user){
            $modal.open({
                templateUrl:'/views/partials/room.html?v=' + VERSION,
                controller: 'usersControllerRoomModalInstance',
                resolve:{
                    user: function(){
                        return user;
                    }
                }
            });
        };

    }])

    .controller('usersControllerListModalInstance', ["$scope", "$rootScope", "$filter", "serviceUser","$cookies", "$modalInstance", "users", function($scope, $rootScope, $filter, User, $cookies, $modalInstance, users) {
        $scope.users = users.data;
        $scope.roomsTmp = [];
        $scope.rooms = [];

        angular.forEach($rootScope.user.rooms, function(room, key) {
            $scope.roomsTmp[room.id] = room;
            $scope.roomsTmp[room.id].users = [];
        });

        angular.forEach($scope.users, function(user, key) {
            angular.forEach(user.rooms, function(room, key) {
                if(room.id){
                    if($scope.roomsTmp[room.id] != undefined){
                        $scope.roomsTmp[room.id].users.push(angular.copy(user));
                    }
                }
            });
        });

        //TRIE + gestion des ex æquo
        angular.forEach($scope.roomsTmp, function(room, key) {
            if(room != undefined){
                users = $filter('orderBy')(room.users, ['-winPoints', 'lastname', 'firstname', 'id']);

                index = 1;
                rank = null;
                lastScore = null;
                angular.forEach(users, function(user, key) {
                    if(lastScore != user.winPoints)
                        rank = index;
                    user.rank = rank;

                    index++;
                    lastScore = user.winPoints;
                });
            }
        });

        $scope.usersSelect = $filter('orderBy')($rootScope.user.rooms[0].users, ['-winPoints', 'lastname', 'firstname', 'id']);
        $scope.selector = $rootScope.user.rooms[0].id;

        $scope.select = function(selector, users){
            $scope.selector = selector;
            $scope.usersSelect = $filter('orderBy')(users, ['-winPoints', 'lastname', 'firstname', 'id']);
        };

        angular.forEach($scope.roomsTmp, function(room, key) {
            if(room != undefined)
                $scope.rooms.push(room);
        });

        $scope.showRank = function(index){
            if(index == 0)
                return true;
            if($scope.usersSelect[index-1].rank != $scope.usersSelect[index].rank)
                return true;
            return false;
        }
    }])

    .controller('usersControllerAccountModalInstance', ["$scope", "$rootScope", "$modalInstance", "$cookies", "serviceUser", "user", function($scope, $rootScope, $modalInstance, $cookies, User, user) {
        $scope.user = user;

        $scope.userData = {};

        $scope.ok = function () {
            User.update($cookies['token'], $cookies['user_id'], $scope.userData)
                .success(function(data){
                    $rootScope.user = data;
                });
            $modalInstance.dismiss('ok');
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    }])

    .controller('usersControllerRoomModalInstance', ["$scope", "$rootScope", "$modalInstance", "$cookies", "serviceUser", "user", "serviceRoom", function($scope, $rootScope, $modalInstance, $cookies, User, user, Room) {
        $scope.user = user;

        $scope.addRoom = function(){
            User.join($cookies['token'], $scope.roomCode)
                .success(function(data){
                    $rootScope.user = data;
                    $scope.user = data;
                });
        };

        $scope.createRoom = function(){
            Room.create($cookies['token'], $scope.newRoomName, $scope.newRoomCode)
                .success(function(data){
                    $rootScope.user.rooms.push(data);
                });
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    }]);
