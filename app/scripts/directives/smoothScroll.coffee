'use strict'

angular.module('App')
  .directive 'smoothScroll', ->
    restrict: 'A'
    link: (scope, element, attr) ->
      element.on 'click', ->
        if attr.smoothScroll
          offset = attr.offset or 100
          target = $(attr.smoothScroll)
          speed = attr.speed or 500
          speed = s unless isNaN(s = parseInt(speed))
          $('html,body').stop().animate({scrollTop: target.offset().top - offset}, speed)
        else
          $('html,body').stop().animate({scrollTop: 0}, speed);
