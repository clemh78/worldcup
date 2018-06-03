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

angular.module('adminController', [])

    .controller('adminControllerList', ["$scope", "users", "$modalInstance", "serviceAdmin", "$state", "$cookies", "serviceRole", "roles", function($scope, users, $modalInstance, Admin, $state, $cookies, Role, roles) {
        $scope.users = users.data;
        $scope.roles = roles.data;

        $scope.usersList = function(){
            $("#users-list").show();
            $("#users-add").hide();
            $('#users-list-menu').parent('li').addClass('active');
            $('#users-add-menu').parent('li').removeClass('active');

            $("#users-welcome").hide();
        };

        $scope.usersAdd = function(){
            $("#users-list").hide();
            $("#users-add").show();
            $('#users-list-menu').parent('li').removeClass('active');
            $('#users-add-menu').parent('li').addClass('active');

            $("#users-welcome").hide();

            $scope.newUser = null;
        };

        $scope.usersWelcome = function(){
            $("#users-list").hide();
            $("#users-add").hide();
            $('#users-list-menu').parent('li').removeClass('active');
            $('#users-add-menu').parent('li').removeClass('active');

            $("#users-welcome").show();
        };

        $scope.login = function(user){
            Admin.loginWithoutPassword($cookies['token'], user.login)
                .success(function(data) {

                    $cookies['primary_token'] = $cookies['token'];
                    $cookies['primary_user_id'] = $cookies['user_id'];

                    $cookies['token'] = data.id;
                    $cookies['user_id'] = data.user_id;

                    location.reload();
                });
        };

        $scope.registerSubmit = function(){
            Admin.registerWithoutPassword($cookies['token'], $scope.newUser.login, $scope.newUser.role_id, $scope.newUser.firstname, $scope.newUser.lastname)
                .success(function(data) {
                    $scope.users.push(data);
                    $scope.newUser.password = data.password;
                    $scope.usersWelcome();
                });
        };

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };
    }])

    .controller('adminControllerModal', ["$scope", "serviceUser", "$cookies", "$modal", function($scope, User, $cookies, $modal) {

        $scope.admin = function(){
            $modal.open({
                templateUrl: '/views/partials/admin.html?v=' + VERSION,
                controller: 'adminControllerList',
                size: 'lg',
                resolve: {
                    users: [ "serviceAdmin", "$cookies", function(Admin, $cookies){
                        return Admin.getUsers($cookies['token']);
                    }],
                    roles: [ "serviceRole", "$cookies", function(Role, $cookies){
                        return Role.getRoles($cookies['token']);
                    }]
                }
            });
        };

    }])

    .controller('adminControllerPrimary', ["$scope", "$rootScope", "serviceUser", "$cookies", function($scope, $rootScope, User, $cookies) {

        $scope.isPrimary = false;

        if($cookies['primary_token'] && $cookies['primary_user_id']){
            $scope.isPrimary = true;

            User.getUser($cookies['primary_user_id'], $cookies['primary_token'])
                .success(function(data){
                    $rootScope.primary = data;
                });
        }

    }]);