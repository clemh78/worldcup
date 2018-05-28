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
                templateUrl: '/views/partials/rankingList.html',
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
                templateUrl:'/views/partials/accountForm.html',
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
                templateUrl:'/views/partials/room.html',
                controller: 'usersControllerRoomModalInstance',
                resolve:{
                    user: function(){
                        return user;
                    }
                }
            });
        };

    }])

    .controller('usersControllerListModalInstance', ["$scope", "serviceUser","$cookies", "$modalInstance", "users", function($scope, User, $cookies, $modalInstance, users) {
        $scope.users = users.data;
        $scope.rooms = [];

        angular.forEach($scope.users, function(user, key) {
            angular.forEach(user.rooms, function(room, key) {
                if(room.id){
                    if(!$scope.rooms[room.id]){
                        $scope.rooms[room.id] = {
                            'room' : room,
                            'users' : []
                        }
                    }

                    $scope.rooms[room.id].users.push(user);
                }
            });
        });

        $scope.rooms.shift();
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

    .controller('usersControllerRoomModalInstance', ["$scope", "$rootScope", "$modalInstance", "$cookies", "serviceUser", "user", function($scope, $rootScope, $modalInstance, $cookies, User, user) {
        $scope.user = user;

        $scope.addRoom = function(){
            User.join($cookies['token'], $scope.roomCode)
                .success(function(data){
                    $rootScope.user = data;
                    $scope.user = data;
                });
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    }]);
