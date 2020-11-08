/*!
 * Font Awesome Pro 5.11.2 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license (Commercial License)
 */
(function () {
  'use strict';

  var _WINDOW = {};
  var _DOCUMENT = {};

  try {
    if (typeof window !== 'undefined') _WINDOW = window;
    if (typeof document !== 'undefined') _DOCUMENT = document;
  } catch (e) {}

  var _ref = _WINDOW.navigator || {},
      _ref$userAgent = _ref.userAgent,
      userAgent = _ref$userAgent === void 0 ? '' : _ref$userAgent;

  var WINDOW = _WINDOW;
  var DOCUMENT = _DOCUMENT;
  var IS_BROWSER = !!WINDOW.document;
  var IS_DOM = !!DOCUMENT.documentElement && !!DOCUMENT.head && typeof DOCUMENT.addEventListener === 'function' && typeof DOCUMENT.createElement === 'function';
  var IS_IE = ~userAgent.indexOf('MSIE') || ~userAgent.indexOf('Trident/');

  var NAMESPACE_IDENTIFIER = '___FONT_AWESOME___';
  var PRODUCTION = function () {
    try {
      return "production" === 'production';
    } catch (e) {
      return false;
    }
  }();

  function bunker(fn) {
    try {
      fn();
    } catch (e) {
      if (!PRODUCTION) {
        throw e;
      }
    }
  }

  function _defineProperty(obj, key, value) {
    if (key in obj) {
      Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }

    return obj;
  }

  function _objectSpread(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i] != null ? arguments[i] : {};
      var ownKeys = Object.keys(source);

      if (typeof Object.getOwnPropertySymbols === 'function') {
        ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
          return Object.getOwnPropertyDescriptor(source, sym).enumerable;
        }));
      }

      ownKeys.forEach(function (key) {
        _defineProperty(target, key, source[key]);
      });
    }

    return target;
  }

  var w = WINDOW || {};
  if (!w[NAMESPACE_IDENTIFIER]) w[NAMESPACE_IDENTIFIER] = {};
  if (!w[NAMESPACE_IDENTIFIER].styles) w[NAMESPACE_IDENTIFIER].styles = {};
  if (!w[NAMESPACE_IDENTIFIER].hooks) w[NAMESPACE_IDENTIFIER].hooks = {};
  if (!w[NAMESPACE_IDENTIFIER].shims) w[NAMESPACE_IDENTIFIER].shims = [];
  var namespace = w[NAMESPACE_IDENTIFIER];

  function defineIcons(prefix, icons) {
    var params = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    var _params$skipHooks = params.skipHooks,
        skipHooks = _params$skipHooks === void 0 ? false : _params$skipHooks;
    var normalized = Object.keys(icons).reduce(function (acc, iconName) {
      var icon = icons[iconName];
      var expanded = !!icon.icon;

      if (expanded) {
        acc[icon.iconName] = icon.icon;
      } else {
        acc[iconName] = icon;
      }

      return acc;
    }, {});

    if (typeof namespace.hooks.addPack === 'function' && !skipHooks) {
      namespace.hooks.addPack(prefix, normalized);
    } else {
      namespace.styles[prefix] = _objectSpread({}, namespace.styles[prefix] || {}, normalized);
    }
    /**
     * Font Awesome 4 used the prefix of `fa` for all icons. With the introduction
     * of new styles we needed to differentiate between them. Prefix `fa` is now an alias
     * for `fas` so we'll easy the upgrade process for our users by automatically defining
     * this as well.
     */


    if (prefix === 'fas') {
      defineIcons('fa', icons);
    }
  }

  var icons = {
    "star": [576, 512, [], "f005", "M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"],
    "star-half-alt": [536, 512, [], "f5c0", "M508.55 171.51L362.18 150.2 296.77 17.81C290.89 5.98 279.42 0 267.95 0c-11.4 0-22.79 5.9-28.69 17.81l-65.43 132.38-146.38 21.29c-26.25 3.8-36.77 36.09-17.74 54.59l105.89 103-25.06 145.48C86.98 495.33 103.57 512 122.15 512c4.93 0 10-1.17 14.87-3.75l130.95-68.68 130.94 68.7c4.86 2.55 9.92 3.71 14.83 3.71 18.6 0 35.22-16.61 31.66-37.4l-25.03-145.49 105.91-102.98c19.04-18.5 8.52-50.8-17.73-54.6zm-121.74 123.2l-18.12 17.62 4.28 24.88 19.52 113.45-102.13-53.59-22.38-11.74.03-317.19 51.03 103.29 11.18 22.63 25.01 3.64 114.23 16.63-82.65 80.38z"],
  };

  bunker(function () {
    defineIcons('fsrs-fas', icons); // Changed this to avoid collisions.
  });

}());
