angular.module('WordpressApp')
	.directive 'bindHtmlCompile', ($sce, $compile) ->
		(scope, element, attr) ->
			element.addClass("ng-binding").data "$binding", attr.bindHtmlCompile
			scope.$watch $sce.parseAsHtml(attr.bindHtmlCompile), ngBindHtmlWatchAction = (value) ->
				element.html ''
				element.append $compile(value)(scope)
