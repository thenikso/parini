'use strict'

urlRegExp = /^([^#]*?:\/\/.*?)(\/.*)?$/
urlRegExp.compile?(urlRegExp)
setWpHref = (attrs, controller, value) ->
	return unless value?
	if controller.lang
		if r = urlRegExp.exec(value)
			value = "#{r[1]}/#{controller.lang}#{r[2] ? ''}"
		else
			value = "/#{controller.lang}#{value}"
	attrs.$set 'href', value

angular.module('App')
	.directive 'wpHrefLang', ->
		restrict: 'A'
		controller: ->
			@lang = null
			@refs = []
			@updateAllRefs = ->
				setWpHref(attrs, @, attrs.wpHref) for attrs in @refs
		link: (scope, element, attrs, controller) ->
			scope.$watch attrs.wpHrefLang, (value) ->
				controller.lang = value
				controller.updateAllRefs()

	.directive 'wpHref', ->
		restrict: 'A'
		require: '^wpHrefLang'
		priority: 99
		link: (scope, element, attrs, controller) ->
			controller.refs.push attrs
			attrs.$observe 'wpHref', (value) ->
				setWpHref(attrs, controller, value)
