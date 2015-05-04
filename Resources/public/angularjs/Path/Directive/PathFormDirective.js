/**
 * Manages Path form
 */
(function () {
    'use strict';

    angular.module('PathModule').directive('pathForm', [
        'HistoryService',
        function (HistoryService) {
            return {
                restrict: 'E',
                replace: true,
                controller: 'PathFormCtrl',
                controllerAs: 'pathFormCtrl',
                templateUrl: EditorApp.webDir + 'bundles/innovapath/angularjs/Path/Partial/path-form.html',
                scope: {
                    id        : '=', // ID of the path
                    path      : '=', // Data of the path
                    modified  : '=', // Is Path have pending modifications ?
                    published : '='  // Is path published ?
                },
                link: function (scope, element, attrs, pathFormCtrl) {
                    // Set controller variables
                    pathFormCtrl.id        = scope.id;
                    pathFormCtrl.path      = scope.path;
                    pathFormCtrl.modified  = scope.modified;
                    pathFormCtrl.published = scope.published;

                    // Update history each time a path is changed
                    scope.$watch('path', function (newValue) {
                        console.log(newValue);
                        if (!HistoryService.isEmpty()) {
                            // Initialization is already done, so mark path as unsaved for each modification
                            pathFormCtrl.unsaved = true;
                        }

                        HistoryService.update(newValue);
                    }, true);
                }
            };
        }
    ]);
})();