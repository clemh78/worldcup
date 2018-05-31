/**
 * Module d'authentification AngularJS
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

angular.module('auth', [])

    .run(["$rootScope", "serviceUser", "serviceTransaction", "$state", "$cookies", function($rootScope, User, Transaction, $state, $cookies) {

        if($cookies['token'] != null){
            $rootScope.isConnected = true;
        }else{
            $rootScope.isConnected = false;
        }

        //Evennement lors du changement de page
        $rootScope.$on("$stateChangeStart", function (event, toState, toParams, fromState, fromParams) {
            $("#content").html("");

		console.log("test check perm");
		console.log($rootScope.isConnected);

            function setRole(user) {
                console.log(user);
                switch (user.role.access_level) {
                    case userRoles.admin      : $rootScope.user.isAdmin      = true; break;
                    case userRoles.user     : $rootScope.user.isMember     = true; break;
                    default                    : $rootScope.user.isGuest      = true; break;
                }
            }

            if($rootScope.isConnected){
                User.getUser($cookies['user_id'], $cookies['token'])
                    .success(function(data) {
                        $rootScope.user = data;
                        setRole(data);
                    });
            }


            if (!User.authorize(toState.access)) {
                if(!$rootScope.isConnected)event.preventDefault();

                if(!$rootScope.isConnected)	$state.transitionTo('login');
                else $rootScope.redirectAlterLoad = {"state" : toState, "params": toParams};
            }

            $rootScope.success = "";
            $rootScope.error = "";
        });

        $rootScope.$watch('isConnected', function() {

            if($rootScope.isConnected != null){
                if($rootScope.isConnected){
                    $('body').css("background-color", "#f5f5f5");
                }else{
                    $('body').css("background-color", "#333");
                    $state.transitionTo('login');
                }
            }
        })
    }])

    .controller('mainController', ["$rootScope", "$scope", "serviceUser", "$cookies", "$state", function($rootScope, $scope, User, $cookies, $state) {


        $rootScope.closeAlert = function(index) {
            $scope.alerts.splice(index, 1);
        };

        $scope.logout = function(){
            User.logout($cookies['token'])
                .success(function(){
                    if($cookies['primary_token'] && $cookies['primary_user_id']) {
                        $cookies['token'] = $cookies['primary_token'];
                        $cookies['user_id'] = $cookies['primary_user_id'];

                        delete $cookies['primary_token'];
                        delete $cookies['primary_user_id'];

                        location.reload();
                    }
                    else{
                        $rootScope.user = null;
                        $rootScope.isConnected = false;
                        delete $cookies['token'];
                        delete $cookies['user_id'];
                    }
                });
        }

    }]);
