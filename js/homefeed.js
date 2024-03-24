jQuery(function() {
    function twf(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = "//platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js, fjs);
        }
    }
    twf(document, "script", "twitter-wjs");

    function randomizingAnimationInterval(additionalTime) {
        var minRand = 1;
        var maxRand = 10;
        var addTime = additionalTime || 0;
        var randomSeconds = (minRand + Math.floor(Math.random() * maxRand));
        return (randomSeconds + addTime) * 1000;
    };

    function randomNumber(min, max) {
        return (min + Math.floor(Math.random() * max));
    };

    function getDiagonalY(coordinateX, imageHeight, imageWidth) {
        return (imageWidth / imageHeight) * coordinateX;
    };

    function setImageCoordinates(selector, x, y) {
        $(selector).css({
            'background-position': x + 'px ' + y + 'px'
        });
    };

    var randomNumberValue = randomNumber(0, 110);
    setImageCoordinates('.plane-left', 300 - getDiagonalY(randomNumberValue, 91, 260), randomNumberValue);
    
    jQuery(document).ready(function() {
        var spec = {
            messagesLeft: {
                remoteParameters: {
                    type: "events"
                },
                element: jQuery("#outer"),
                class: ".live-feed-container-left"
            },
            messagesRight: {
                remoteParameters: {
                    type: "events"
                },
                element: jQuery("#outer"),
                class: ".live-feed-container-right"
            }//,
            // chat: {
            //     remoteParameters: {
            //         type: "chat"
            //     },
            //     element: jQuery(".livebox")
            // }
        };
        runLiveBoxes(spec); 
    });

    function runLiveBoxes(spec) {
        var delayEventsBy = 3,
            serverPollWait = 10,
            bubbleFadeDuration = 1,
            bubbleDuration = 3,
            maxSimultaneousBubbles = 10;
        var boxes = {};
        for (var name in spec) {
            boxes[name] = {};
            boxes[name].box = jQuery(spec[name].element).find(spec[name].class);
            boxes[name].queue = [];
            boxes[name].lastSeen = null;
            boxes[name].remoteParameters = spec[name].remoteParameters;
        }

        function enqueue(livebox, objects) {
            if (!objects || objects.length == 0)
                return;
            objects.sort(function(a, b) {
                return b.secondsago - a.secondsago;
            });
            var now = (new Date()).getTime(),
                i;
            for (i = objects.length - 1; i >= 0; --i) {
                if (objects[i].id == livebox.lastSeen)
                    break;
            }
            ++i;
            for (; i < objects.length; ++i)
                insertBubble(livebox, objects[i]);
            livebox.lastSeen = objects[objects.length - 1].id;
        }
        var firstFetch = true;

        function positionBubble(livebox, bubble) {
            var w = bubble.width(),
                h = bubble.height(),
                x = 0,
                y = 0;
            var others = [];
            livebox.box.children().each(function() {
                var layout = $(this).data("layout");
                if (layout) {
                    var l = layout.split(":");
                    others.push({
                        x1: +l[0],
                        y1: +l[1],
                        x2: +l[2],
                        y2: +l[3]
                    });
                }
            });
            var maxTries = 100,
                boundingBox = {
                    w: livebox.box.width(),
                    h: 500
                };
            for (var i = 0; i < maxTries; ++i) {
                x = Math.floor(Math.random() * (boundingBox.w - w));
                y = Math.floor(Math.random() * (boundingBox.h - h));
                var space = true;
                for (var j = 0; j < others.length; ++j) {
                    var o = others[j];
                    if (!(x + w <= o.x1 || x >= o.x2 || y + h <= o.y1 || y >= o.y2)) {
                        space = false;
                        break;
                    }
                }
                if (space)
                    break;
            }
            bubble.data("layout", x + ":" + y + ":" + (x + w) + ":" + (y + h));
            bubble.css({
                'margin-left': x,
                'margin-top': y
            });
        }

        function insertBubble(livebox, o) {
            var delay = delayEventsBy * 1000 - o.secondsago * 1000; // 60,000 - 56,000
            if (delay < -delayEventsBy * 1000 / 2)
                return;
            if (delay < 0)
                delay = Math.random() * delayEventsBy * 1000;
            if (livebox.box.children().length >= maxSimultaneousBubbles)
                return;
            var d = jQuery('<div class="live-feed-item" style="display:none;">' + o.message + '</div>');
            livebox.box.append(d);

            d
                .delay(delay)
                .queue(function(next) { positionBubble(livebox, $(this)); next(); })
                .fadeIn(bubbleFadeDuration * 1000)
                .delay(bubbleDuration * 1000)
                .fadeOut(bubbleFadeDuration * 1000, function() { $(this).remove(); });
        }
        var isRequestInProgress;

        function onDataFetched(res) {
            isRequestInProgress = 0;
            for (var name in boxes)
                if (res[name])
                    enqueue(boxes[name], res[name]);
        }

        function resetInProgress() {
            isRequestInProgress = 0;
        }

        function fetchData() {
            var timeout;
            if (isRequestInProgress == 1) {
                setTimeout(fetchData, serverPollWait);
                return;
            }
            var b = {},
                c = 0;
            for (var name in boxes) {
                var livebox = boxes[name];
                if (livebox.box.length == 0 || livebox.box.is(":hidden"))
                    continue;
                b[name] = {};
                ++c;
                for (var attr in livebox.remoteParameters)
                    b[name][attr] = livebox.remoteParameters[attr];
            }
            if (c > 0) {
                jQuery.ajax({
                    url: location.protocol + '//' + location.hostname + "/json_homefeed.php",
                    success: onDataFetched,
                    timeout: resetInProgress,
                    error: resetInProgress,
                    parsererror: resetInProgress,
                    data: {
                        boxes: jQuery.toJSON(b)
                    },
                    dataType: "json",
                    cache: false
                });
                isRequestInProgress = 1;
                timeout = serverPollWait;
            } else {
                timeout = 1;
            }
            timeout *= (0.3 + 0.6 * Math.random());
            setTimeout(fetchData, timeout * 1000);
        }

        fetchData();
    };
    (function(jQuery) {
        jQuery.toJSON = function(o) {
            if (typeof(JSON) == 'object' && JSON.stringify)
                return JSON.stringify(o);
            var type = typeof(o);
            if (o === null)
                return "null";
            if (type == "undefined")
                return undefined;
            if (type == "number" || type == "boolean")
                return o + "";
            if (type == "string")
                return jQuery.quoteString(o);
            if (type == 'object') {
                if (typeof o.toJSON == "function")
                    return jQuery.toJSON(o.toJSON());
                if (o.constructor === Date) {
                    var month = o.getUTCMonth() + 1;
                    if (month < 10) month = '0' + month;
                    var day = o.getUTCDate();
                    if (day < 10) day = '0' + day;
                    var year = o.getUTCFullYear();
                    var hours = o.getUTCHours();
                    if (hours < 10) hours = '0' + hours;
                    var minutes = o.getUTCMinutes();
                    if (minutes < 10) minutes = '0' + minutes;
                    var seconds = o.getUTCSeconds();
                    if (seconds < 10) seconds = '0' + seconds;
                    var milli = o.getUTCMilliseconds();
                    if (milli < 100) milli = '0' + milli;
                    if (milli < 10) milli = '0' + milli;
                    return '"' + year + '-' + month + '-' + day + 'T' +
                        hours + ':' + minutes + ':' + seconds + '.' + milli + 'Z"';
                }
                if (o.constructor === Array) {
                    var ret = [];
                    for (var i = 0; i < o.length; i++)
                        ret.push(jQuery.toJSON(o[i]) || "null");
                    return "[" + ret.join(",") + "]";
                }
                var pairs = [];
                for (var k in o) {
                    var name;
                    var type = typeof k;
                    if (type == "number")
                        name = '"' + k + '"';
                    else if (type == "string")
                        name = jQuery.quoteString(k);
                    else
                        continue;
                    if (typeof o[k] == "function")
                        continue;
                    var val = jQuery.toJSON(o[k]);
                    pairs.push(name + ":" + val);
                }
                return "{" + pairs.join(", ") + "}";
            }
        };
        jQuery.evalJSON = function(src) {
            if (typeof(JSON) == 'object' && JSON.parse)
                return JSON.parse(src);
            return eval("(" + src + ")");
        };
        jQuery.secureEvalJSON = function(src) {
            if (typeof(JSON) == 'object' && JSON.parse)
                return JSON.parse(src);
            var filtered = src;
            filtered = filtered.replace(/\\["\\\/bfnrtu]/g, '@');
            filtered = filtered.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
            filtered = filtered.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
            if (/^[\],:{}\s]*$/.test(filtered))
                return eval("(" + src + ")");
            else
                throw new SyntaxError("Error parsing JSON, source is not valid.");
        };
        jQuery.quoteString = function(string) {
            if (string.match(_escapeable)) {
                return '"' + string.replace(_escapeable, function(a) {
                    var c = _meta[a];
                    if (typeof c === 'string') return c;
                    c = a.charCodeAt();
                    return '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16);
                }) + '"';
            }
            return '"' + string + '"';
        };
        var _escapeable = /["\\\x00-\x1f\x7f-\x9f]/g;
        var _meta = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"': '\\"',
            '\\': '\\\\'
        };
    })(jQuery);
});