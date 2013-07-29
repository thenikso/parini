'use strict'

getValueFromClass = (classes, startWith) ->
  return c.substr(startWith.length) for c in classes when (s = c.indexOf(startWith)) == 0
  null

angular.module('App')

  # smoothScroll
  # Uses jQuery to smooth scroll to a target element or location.
  # The target should be an elment id or a number and can be specified by both
  # valorizing the `smooth-scroll` attribute or by adding a class starting with:
  # `smooth-scroll-to-<target>`.
  # The speed of the animation to reach the target can also be speficied in a
  # similar fashon by either using the `speed` attribute or a class like:
  # `smooth-scroll-speed-<speed>`
  # Lastly, an offset for element targets can be specified with the attribute
  # `offset` or a class like: `smooth-scroll-offset-<offset>`.
  .directive 'smoothScroll', ->
    restrict: 'AC'
    link: (scope, element, attr) ->
      classes = attr.class.split(' ')
      target = attr.smoothScroll or getValueFromClass(classes, 'smooth-scroll-to-')
      return unless target
      target = t unless isNaN(t = parseInt(target))
      offset = attr.offset or getValueFromClass(classes, 'smooth-scroll-offset-') or 100
      speed = attr.speed or getValueFromClass(classes, 'smooth-scroll-speed-') or 500
      speed = s unless isNaN(s = parseInt(speed))
      element.on 'click', ->
        if angular.isString(target)
          $('html,body').stop().animate({scrollTop: $("##{target}").offset().top - offset}, speed)
        else
          $('html,body').stop().animate({scrollTop: target}, speed)
        no
