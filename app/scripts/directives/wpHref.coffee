'use strict'

urlRegExp = /^([^#]*?:\/\/.*?)(?:\/([a-z]{2})(?=\/))?(\/.*)?$/
urlRegExp.compile?(urlRegExp)
localizedUrl = (url, lang) ->
	return url unless lang
	return "#{r[1]}/#{lang}#{r[3] ? ''}" if r = urlRegExp.exec(url)
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
		controller: ($location) ->
			@lang = null
			@allWpHrefAttrs = []
			@allWpHrefActiveClass = []
			@allWpHrefChange = []
			@updateAllHref = ->
				for attrs in @allWpHrefAttrs
					url = localizedUrl(attrs.wpHref, @lang)
					attrs.$set 'href', url
			@updateAllActiveClass = ->
				for c in @allWpHrefActiveClass
					c.checkActive()
			@updateAllHrefChange = ->
				for attrs in @allWpHrefChange
					url = localizedUrl attrs.wpHrefChange or $location.absUrl(), attrs.lang
					attrs.$set 'href', url
		link: (scope, element, attrs, controller) ->
			# Update all wpHref on lang change
			attrs.$observe 'wpHrefLang', (lang) ->
				controller.lang = lang
				controller.updateAllHref()
				controller.updateAllActiveClass()
			# Watch route change to update active classes
			scope.$on '$routeChangeSuccess', ->
				controller.updateAllActiveClass()
				controller.updateAllHrefChange()

	# If present, will add the specified CSS class (default to `active`) on the
	# element when the current location is the localized href of the element
	# or a children of it.
	.directive 'wpHrefActiveClass', ($location) ->
		restrict: 'A'
		controller: ($element) ->
			@wpHrefAttrs = null
			@cssClass = 'active'
			@checkActive = (url) ->
				return unless (url or= @wpHrefAttrs.wpHref)
				currentLocation = $location.absUrl() + '/'
				if currentLocation.indexOf(url) >= 0
					$element.addClass(@cssClass)
				else
					$element.removeClass(@cssClass)
		link: (scope, element, attrs, controller) ->
			controller.cssClass = c if (c = attrs.wpHrefActiveClass)

	# With a wpHrefLang present, interpolate and localize the href of the element.
	.directive 'wpHref', ->
		restrict: 'A'
		require: ['^wpHrefLang', '^?wpHrefActiveClass']
		priority: 99
		link: (scope, element, attrs, controllers) ->
			controllers[0].allWpHrefAttrs.push attrs
			if controllers[1]?
				controllers[0].allWpHrefActiveClass.push controllers[1]
				controllers[1]?.wpHrefAttrs = attrs
			attrs.$observe 'wpHref', (url) ->
				# Update URL on interpolation change
				url = localizedUrl url, controllers[0].lang
				attrs.$set 'href', url
				# Update active state on iterpolation change
				controllers[1]?.checkActive(url)

	.directive 'wpHrefChange', ($location) ->
		restrict: 'A'
		require: '^wpHrefLang'
		priority: 99
		link: (scope, element, attrs, controller) ->
			return unless attrs.lang?
			controller.allWpHrefChange.push attrs
			attrs.$observe 'wpHrefChange', (url) ->
				url = $location.absUrl() unless url
				url = localizedUrl url, attrs.lang
				attrs.$set 'href', url

