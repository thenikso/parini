'use strict'

describe 'Service: wordpressApi', () ->

  # load the service's module
  beforeEach module 'App'

  # instantiate service
  wordpressApi = {}
  beforeEach inject (_wordpressApi_) ->
    wordpressApi = _wordpressApi_

  it 'should do something', () ->
    expect(!!wordpressApi).toBe true;
