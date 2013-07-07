'use strict'

describe 'Directive: wpHref', () ->
  beforeEach module 'angularthemeApp'

  element = {}

  it 'should make hidden element visible', inject ($rootScope, $compile) ->
    element = angular.element '<wp-href></wp-href>'
    element = $compile(element) $rootScope
    expect(element.text()).toBe 'this is the wpHref directive'
