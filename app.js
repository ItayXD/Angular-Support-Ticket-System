 var app = angular.module('app', ['ngRoute', 'ngAnimate', 'ngSanitize']);

//Get all tickets
app.controller('tickets', function($http, $scope, $rootScope) {
    $http.get('server.php?alltickets=true')
    .then(function (res) {
      $scope.tickets = res.data;
    }, function (err) {
      console.log(err);
    });
    $scope.$on('newTicket', function (event, args) {
      $scope.tickets.push(args.ticket);
    });
});

app.controller('getMessages', function($http, $scope, $routeParams, $httpParamSerializerJQLike) {
  //Get all messages for ticket id
  $http.get("server.php?ticketid=" + $routeParams.ticketid)
  .then(function (res) {
    $scope.messages = res.data.messages;
    $scope.ticket = res.data.ticket;
  }, function (err) {
    console.log(err);
  });

  //Create new message
  $scope.newMsg = function () {
    var post = {'new': 'msg', 'ticketid': $routeParams.ticketid, 'user': $scope.newusr, 'content': $scope.newcntnt};
    $http({
      url: 'server.php',
      method: 'POST',
      data: $httpParamSerializerJQLike(post),
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    })
      .then (function success (res) {
        $scope.messages.push(res.data);
      }, function fail (res) {
        console.log(res);
        $scope.messages.push({id: res.status, user: res.statusText, content: res.data});
      });
  };
});

app.controller('newTicket', function($scope, $rootScope, $http, $httpParamSerializerJQLike){
  $scope.createTicket = function () {
    var post = {'new': 'tckt', 'user': $scope.usr, 'priority': $scope.prio, 'subject': $scope.sbj, 'content': $scope.cntnt};
    $http({
      url: 'server.php',
      method: 'POST',
      data: $httpParamSerializerJQLike(post),
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    })
      .then (function success (res) {
//        $rootScope.tickets.push(res.data);
          $scope.$emit('newTicket', {ticket: res.data});
      }, function fail (res) {
        console.log(res);
        $scope.json =res;
      });
  };
});
//Set the routing
app.config(function( $routeProvider, $locationProvider ) {
  //Enable links without '#'
  $locationProvider.html5Mode(true);
  $routeProvider
    .when('/ticket/:ticketid', {
        templateUrl: 'ticket.html',
        controller: 'getMessages'
    })
    .when('/newTicket', {
      templateUrl: 'newTicket.html',
      controller: 'newTicket'
    });
});