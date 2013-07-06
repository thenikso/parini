'use strict'

describe 'Controller: HomeCtrl', () ->

  # load the controller's module
  beforeEach module 'angularthemeApp'

  HomeCtrl = {}
  scope = {}

  # Initialize the controller and a mock scope
  beforeEach inject ($controller, $rootScope) ->
    scope = $rootScope.$new()
    HomeCtrl = $controller 'HomeCtrl', {
      $scope: scope
    }

  it 'should attach a list of awesomeThings to the scope', () ->
    expect(scope.awesomeThings.length).toBe 3;
