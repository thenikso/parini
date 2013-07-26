'use strict'

describe 'Controller: PostCtrl', () ->

  # load the controller's module
  beforeEach module 'App'

  PostCtrl = {}
  scope = {}

  # Initialize the controller and a mock scope
  beforeEach inject ($controller, $rootScope) ->
    scope = $rootScope.$new()
    PostCtrl = $controller 'PostCtrl', {
      $scope: scope
    }

  it 'should attach a list of awesomeThings to the scope', () ->
    expect(scope.awesomeThings.length).toBe 3;
