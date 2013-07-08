'use strict'

urlRegExp = /^([^#]*?:\/\/.*?)(\/.*)?$/
urlRegExp.compile?(urlRegExp)
localizedUrl = (url, lang) ->
	# TODO handle lang already present in url
	return url unless lang
	return "#{r[1]}/#{lang}#{r[2] ? ''}" if r = urlRegExp.exec(url)
	"/#{lang}#{url}"

setWpHref = (attrs, controller, value) ->
	return unless value?
	if controller.lang
		if r = urlRegExp.exec(value)
			value = "#{r[1]}/#{controller.lang}#{r[2] ? ''}"
		else
			value = "/#{controller.lang}#{value}"
	attrs.$set 'href', value
	value

angular.module('App')
	# Directive to specify the current language code
	# The code will be used by the other directive wpHref to localize the href.
	.directive 'wpHrefLang', ->
		restrict: 'A'
		controller: ->
			@lang = null
			@refs = []
			@updateAllRefs = ->
				for attrs in @refs
					url = localizedUrl(attrs.wpHref, @lang)
					attrs.$set 'href', url
		link: (scope, element, attrs, controller) ->
			scope.$watch attrs.wpHrefLang, (lang) ->
				# Update all wpHref on lang change
				controller.lang = lang
				controller.updateAllRefs()

	# If present, will add the specified CSS class (default to `active`) on the
	# element when the current location is the localized href of the element
	# or a children of it.
	.directive 'wpHrefActiveClass', ($location) ->
		restrict: 'A'
		controller: ($element) ->
			@wpHrefAttrs = null
			@cssClass = 'active'
			@checkActive = (url) ->
				return unless url
				currentLocation = $location.absUrl() + '/'
				if currentLocation.indexOf(url) >= 0
					$element.addClass(@cssClass)
				else
					$element.removeClass(@cssClass)
		link: (scope, element, attrs, controller) ->
			controller.cssClass = c if (c = attrs.wpHrefActiveClass)
			scope.$on '$routeChangeSuccess', ->
				controller.checkActive(controller.wpHrefAttrs.wpHref) if controller.wpHrefAttrs?

	# With a wpHrefLang present, interpolate and localize the href of the element.
	.directive 'wpHref', ->
		restrict: 'A'
		require: ['^wpHrefLang', '^?wpHrefActiveClass']
		priority: 99
		link: (scope, element, attrs, controllers) ->
			controllers[0].refs.push attrs
			controllers[1]?.wpHrefAttrs = attrs
			attrs.$observe 'wpHref', (url) ->
				# Update URL on interpolation change
				url = localizedUrl(url, controllers[0].lang)
				attrs.$set 'href', url
				# Update active state on iterpolation change
				controllers[1]?.checkActive(url)
