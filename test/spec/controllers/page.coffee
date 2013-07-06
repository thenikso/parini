'use strict'

describe 'Controller: PageCtrl', () ->

  # load the controller's module
  beforeEach module 'angularthemeApp'

  PageCtrl = {}
  scope = {}

  # Initialize the controller and a mock scope
  beforeEach inject ($controller, $rootScope) ->
    scope = $rootScope.$new()
    PageCtrl = $controller 'PageCtrl', {
      $scope: scope
    }

  it 'should attach a list of awesomeThings to the scope', () ->
    expect(scope.awesomeThings.length).toBe 3;
