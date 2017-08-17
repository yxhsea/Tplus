/**
 * Plupload - multi-runtime File Uploader
 * v2.3.1
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 *
 * Date: 2017-02-06
 */
;(function (global, factory) {
	var extract = function() {
		var ctx = {};
		factory.apply(ctx, arguments);
		return ctx.plupload;
	};
	
	if (typeof define === "function" && define.amd) {
		define("plupload", ['./moxie'], extract);
	} else if (typeof module === "object" && module.exports) {
		module.exports = extract(require('./moxie'));
	} else {
		global.plupload = extract(global.moxie);
	}
}(this || window, function(moxie) {
/**
 * Plupload.js
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

;(function(exports, o, undef) {

var delay = window.setTimeout;
var fileFilters = {};
var u = o.core.utils;
var Runtime = o.runtime.Runtime;

// convert plupload features to caps acceptable by mOxie
function normalizeCaps(settings) {
	var features = settings.required_features, caps = {};

	function resolve(feature, value, strict) {
		// Feature notation is deprecated, use caps (this thing here is required for backward compatibility)
		var map = {
			chunks: 'slice_blob',
			jpgresize: 'send_binary_string',
			pngresize: 'send_binary_string',
			progress: 'report_upload_progress',
			multi_selection: 'select_multiple',
			dragdrop: 'drag_and_drop',
			drop_element: 'drag_and_drop',
			headers: 'send_custom_headers',
			urlstream_upload: 'send_binary_string',
			canSendBinary: 'send_binary',
			triggerDialog: 'summon_file_dialog'
		};

		if (map[feature]) {
			caps[map[feature]] = value;
		} else if (!strict) {
			caps[feature] = value;
		}
	}

	if (typeof(features) === 'string') {
		plupload.each(features.split(/\s*,\s*/), function(feature) {
			resolve(feature, true);
		});
	} else if (typeof(features) === 'object') {
		plupload.each(features, function(value, feature) {
			resolve(feature, value);
		});
	} else if (features === true) {
		// check settings for required features
		if (settings.chunk_size && settings.chunk_size > 0) {
			caps.slice_blob = true;
		}

		if (!plupload.isEmptyObj(settings.resize) || settings.multipart === false) {
			caps.send_binary_string = true;
		}

		if (settings.http_method) {
            caps.use_http_method = settings.http_method;
        }

		plupload.each(settings, function(value, feature) {
			resolve(feature, !!value, true); // strict check
		});
	}

	return caps;
}

/**
 * @module plupload
 * @static
 */
var plupload = {
	/**
	 * Plupload version will be replaced on build.
	 * 当前Plupload版本号
	 *
	 * @property VERSION
	 * @for Plupload
	 * @static
	 * @final
	 */
	VERSION : '2.3.1',

	/**
	 * The state of the queue before it has started and after it has finished
	 * 值为1，代表上传队列还未开始上传或者上传队列中的文件已经上传完毕时plupload实例的state属性值
     *
	 * @property STOPPED
	 * @static
	 * @final
	 */
	STOPPED : 1,

	/**
	 * Upload process is running
	 * 值为2，代表队列中的文件正在上传时plupload实例的state属性值
	 *
	 * @property STARTED
	 * @static
	 * @final
	 */
	STARTED : 2,

	/**
	 * File is queued for upload
	 * 值为1，代表某个文件已经被添加进队列等待上传时该文件对象的status属性值
	 *
	 * @property QUEUED
	 * @static
	 * @final
	 */
	QUEUED : 1,

	/**
	 * File is being uploaded
	 * 值为2，代表某个文件正在上传时该文件对象的status属性值
     *
	 * @property UPLOADING
	 * @static
	 * @final
	 */
	UPLOADING : 2,

	/**
	 * File has failed to be uploaded
	 * 值为4，代表某个文件上传失败后该文件对象的status属性值
     *
	 * @property FAILED
	 * @static
	 * @final
	 */
	FAILED : 4,

	/**
	 * File has been uploaded successfully
	 * 值为5，代表某个文件上传成功后该文件对象的status属性值
	 *
	 * @property DONE
	 * @static
	 * @final
	 */
	DONE : 5,

	// Error constants used by the Error event
	// 发生错误事件定义的错误常量

	/**
	 * Generic error for example if an exception is thrown inside Silverlight.
	 * 值为-100，发生通用错误时的错误代码
	 *
	 * @property GENERIC_ERROR
	 * @static
	 * @final
	 */
	GENERIC_ERROR : -100,

	/**
	 * HTTP transport error. For example if the server produces a HTTP status other than 200.
	 * 值为-200，发生http网络错误时的错误代码，例如服务气端返回的状态码不是200
	 *
	 * @property HTTP_ERROR
	 * @static
	 * @final
	 */
	HTTP_ERROR : -200,

	/**
	 * Generic I/O error. For example if it wasn't possible to open the file stream on local machine.
	 * 值为-300，发生磁盘读写错误时的错误代码，例如本地上某个文件不可读
     *
	 * @property IO_ERROR
	 * @static
	 * @final
	 */
	IO_ERROR : -300,

	/**
	 * 值为-400，发生因为安全问题而产生的错误时的错误代码
	 * @property SECURITY_ERROR
	 * @static
	 * @final
	 */
	SECURITY_ERROR : -400,

	/**
	 * Initialization error. Will be triggered if no runtime was initialized.
	 * 值为-500，初始化时发生错误的错误代码
	 * @property INIT_ERROR
	 * @static
	 * @final
	 */
	INIT_ERROR : -500,

	/**
	 * File size error. If the user selects a file that is too large it will be blocked and an error of this type will be triggered.
	 * 值为-600，当选择的文件太大时的错误代码
	 * @property FILE_SIZE_ERROR
	 * @static
	 * @final
	 */
	FILE_SIZE_ERROR : -600,

	/**
	 * File extension error. If the user selects a file that isn't valid according to the filters setting.
	 * 值为-601，当选择的文件类型不符合要求时的错误代码
	 * @property FILE_EXTENSION_ERROR
	 * @static
	 * @final
	 */
	FILE_EXTENSION_ERROR : -601,

	/**
	 * Duplicate file error. If prevent_duplicates is set to true and user selects the same file again.
	 * 值为-602，当选取了重复的文件而配置中又不允许有重复文件时的错误代码
	 * @property FILE_DUPLICATE_ERROR
	 * @static
	 * @final
	 */
	FILE_DUPLICATE_ERROR : -602,

	/**
	 * Runtime will try to detect if image is proper one. Otherwise will throw this error.
	 * 值为-700，发生图片格式错误时的错误代码
	 * @property IMAGE_FORMAT_ERROR
	 * @static
	 * @final
	 */
	IMAGE_FORMAT_ERROR : -700,

	/**
	 * While working on files runtime may run out of memory and will throw this error.
	 * 当发生内存错误时的错误代码
	 * @since 2.1.2
	 * @property MEMORY_ERROR
	 * @static
	 * @final
	 */
	MEMORY_ERROR : -701,

	/**
	 * Each runtime has an upper limit on a dimension of the image it can handle. If bigger, will throw this error.
	 * 值为-702，当文件大小超过了plupload所能处理的最大值时的错误代码
	 * @property IMAGE_DIMENSIONS_ERROR
	 * @static
	 * @final
	 */
	IMAGE_DIMENSIONS_ERROR : -702,

	/**
	 * Mime type lookup table.
	 *
	 * @property mimeTypes
	 * @type Object
	 * @final
	 */
	mimeTypes : u.Mime.mimes,

	/**
	 * In some cases sniffing is the only way around :(
	 */
	ua: u.Env,

	/**
	 * Gets the true type of the built-in object (better version of typeof).
	 * @credits Angus Croll (http://javascriptweblog.wordpress.com/)
	 *
	 * @method typeOf
	 * @static
	 * @param {Object} o Object to check.
	 * @return {String} Object [[Class]]
	 */
	typeOf: u.Basic.typeOf,

	/**
	 * Extends the specified object with another object.
	 *
	 * @method extend
	 * @static
	 * @param {Object} target Object to extend.
	 * @param {Object..} obj Multiple objects to extend with.
	 * @return {Object} Same as target, the extended object.
	 */
	extend : u.Basic.extend,

	/**
	 * Generates an unique ID. This is 99.99% unique since it takes the current time and 5 random numbers.
	 * The only way a user would be able to get the same ID is if the two persons at the same exact millisecond manages
	 * to get 5 the same random numbers between 0-65535 it also uses a counter so each call will be guaranteed to be page unique.
	 * It's more probable for the earth to be hit with an asteriod. You can also if you want to be 100% sure set the plupload.guidPrefix property
	 * to an user unique key.
	 *
	 * @method guid
	 * @static
	 * @return {String} Virtually unique id.
	 */
	guid : u.Basic.guid,

	/**
	 * Get array of DOM Elements by their ids.
	 *
	 * @method get
	 * @param {String} id Identifier of the DOM Element
	 * @return {Array}
	*/
	getAll : function get(ids) {
		var els = [], el;

		if (plupload.typeOf(ids) !== 'array') {
			ids = [ids];
		}

		var i = ids.length;
		while (i--) {
			el = plupload.get(ids[i]);
			if (el) {
				els.push(el);
			}
		}

		return els.length ? els : null;
	},

	/**
	Get DOM element by id

	@method get
	@param {String} id Identifier of the DOM Element
	@return {Node}
	*/
	get: u.Dom.get,

	/**
	 * Executes the callback function for each item in array/object. If you return false in the
	 * callback it will break the loop.
	 *
	 * @method each
	 * @static
	 * @param {Object} obj Object to iterate.
	 * @param {function} callback Callback function to execute for each item.
	 */
	each : u.Basic.each,

	/**
	 * Returns the absolute x, y position of an Element. The position will be returned in a object with x, y fields.
	 *
	 * @method getPos
	 * @static
	 * @param {Element} node HTML element or element id to get x, y position from.
	 * @param {Element} root Optional root element to stop calculations at.
	 * @return {object} Absolute position of the specified element object with x, y fields.
	 */
	getPos : u.Dom.getPos,

	/**
	 * Returns the size of the specified node in pixels.
	 *
	 * @method getSize
	 * @static
	 * @param {Node} node Node to get the size of.
	 * @return {Object} Object with a w and h property.
	 */
	getSize : u.Dom.getSize,

	/**
	 * Encodes the specified string.
	 *
	 * @method xmlEncode
	 * @static
	 * @param {String} s String to encode.
	 * @return {String} Encoded string.
	 */
	xmlEncode : function(str) {
		var xmlEncodeChars = {'<' : 'lt', '>' : 'gt', '&' : 'amp', '"' : 'quot', '\'' : '#39'}, xmlEncodeRegExp = /[<>&\"\']/g;

		return str ? ('' + str).replace(xmlEncodeRegExp, function(chr) {
			return xmlEncodeChars[chr] ? '&' + xmlEncodeChars[chr] + ';' : chr;
		}) : str;
	},

	/**
	 * Forces anything into an array.
	 *
	 * @method toArray
	 * @static
	 * @param {Object} obj Object with length field.
	 * @return {Array} Array object containing all items.
	 */
	toArray : u.Basic.toArray,

	/**
	 * Find an element in array and return its index if present, otherwise return -1.
	 *
	 * @method inArray
	 * @static
	 * @param {mixed} needle Element to find
	 * @param {Array} array
	 * @return {Int} Index of the element, or -1 if not found
	 */
	inArray : u.Basic.inArray,

	/**
	Recieve an array of functions (usually async) to call in sequence, each  function
	receives a callback as first argument that it should call, when it completes. Finally,
	after everything is complete, main callback is called. Passing truthy value to the
	callback as a first argument will interrupt the sequence and invoke main callback
	immediately.

	@method inSeries
	@static
	@param {Array} queue Array of functions to call in sequence
	@param {Function} cb Main callback that is called in the end, or in case of error
	*/
	inSeries: u.Basic.inSeries,

	/**
	 * Extends the language pack object with new items.
	 *
	 * @method addI18n
	 * @static
	 * @param {Object} pack Language pack items to add.
	 * @return {Object} Extended language pack object.
	 */
	addI18n : o.core.I18n.addI18n,

	/**
	 * Translates the specified string by checking for the english string in the language pack lookup.
	 *
	 * @method translate
	 * @static
	 * @param {String} str String to look for.
	 * @return {String} Translated string or the input string if it wasn't found.
	 */
	translate : o.core.I18n.translate,

	/**
	 * Pseudo sprintf implementation - simple way to replace tokens with specified values.
	 *
	 * @param {String} str String with tokens
	 * @return {String} String with replaced tokens
	 */
	sprintf : u.Basic.sprintf,

	/**
	 * Checks if object is empty.
	 *
	 * @method isEmptyObj
	 * @static
	 * @param {Object} obj Object to check.
	 * @return {Boolean}
	 */
	isEmptyObj : u.Basic.isEmptyObj,

	/**
	 * Checks if specified DOM element has specified class.
	 *
	 * @method hasClass
	 * @static
	 * @param {Object} obj DOM element like object to add handler to.
	 * @param {String} name Class name
	 */
	hasClass : u.Dom.hasClass,

	/**
	 * Adds specified className to specified DOM element.
	 *
	 * @method addClass
	 * @static
	 * @param {Object} obj DOM element like object to add handler to.
	 * @param {String} name Class name
	 */
	addClass : u.Dom.addClass,

	/**
	 * Removes specified className from specified DOM element.
	 *
	 * @method removeClass
	 * @static
	 * @param {Object} obj DOM element like object to add handler to.
	 * @param {String} name Class name
	 */
	removeClass : u.Dom.removeClass,

	/**
	 * Returns a given computed style of a DOM element.
	 *
	 * @method getStyle
	 * @static
	 * @param {Object} obj DOM element like object.
	 * @param {String} name Style you want to get from the DOM element
	 */
	getStyle : u.Dom.getStyle,

	/**
	 * Adds an event handler to the specified object and store reference to the handler
	 * in objects internal Plupload registry (@see removeEvent).
	 *
	 * @method addEvent
	 * @static
	 * @param {Object} obj DOM element like object to add handler to.
	 * @param {String} name Name to add event listener to.
	 * @param {Function} callback Function to call when event occurs.
	 * @param {String} (optional) key that might be used to add specifity to the event record.
	 */
	addEvent : u.Events.addEvent,

	/**
	 * Remove event handler from the specified object. If third argument (callback)
	 * is not specified remove all events with the specified name.
	 *
	 * @method removeEvent
	 * @static
	 * @param {Object} obj DOM element to remove event listener(s) from.
	 * @param {String} name Name of event listener to remove.
	 * @param {Function|String} (optional) might be a callback or unique key to match.
	 */
	removeEvent: u.Events.removeEvent,

	/**
	 * Remove all kind of events from the specified object
	 *
	 * @method removeAllEvents
	 * @static
	 * @param {Object} obj DOM element to remove event listeners from.
	 * @param {String} (optional) unique key to match, when removing events.
	 */
	removeAllEvents: u.Events.removeAllEvents,

	/**
	 * Cleans the specified name from national characters (diacritics). The result will be a name with only a-z, 0-9 and _.
	 *
	 * @method cleanName
	 * @static
	 * @param {String} s String to clean up.
	 * @return {String} Cleaned string.
	 */
	cleanName : function(name) {
		var i, lookup;

		// Replace diacritics
		lookup = [
			/[\300-\306]/g, 'A', /[\340-\346]/g, 'a',
			/\307/g, 'C', /\347/g, 'c',
			/[\310-\313]/g, 'E', /[\350-\353]/g, 'e',
			/[\314-\317]/g, 'I', /[\354-\357]/g, 'i',
			/\321/g, 'N', /\361/g, 'n',
			/[\322-\330]/g, 'O', /[\362-\370]/g, 'o',
			/[\331-\334]/g, 'U', /[\371-\374]/g, 'u'
		];

		for (i = 0; i < lookup.length; i += 2) {
			name = name.replace(lookup[i], lookup[i + 1]);
		}

		// Replace whitespace
		name = name.replace(/\s+/g, '_');

		// Remove anything else
		name = name.replace(/[^a-z0-9_\-\.]+/gi, '');

		return name;
	},

	/**
	 * Builds a full url out of a base URL and an object with items to append as query string items.
	 *
	 * @method buildUrl
	 * @static
	 * @param {String} url Base URL to append query string items to.
	 * @param {Object} items Name/value object to serialize as a querystring.
	 * @return {String} String with url + serialized query string items.
	 */
	buildUrl: function(url, items) {
		var query = '';

		plupload.each(items, function(value, name) {
			query += (query ? '&' : '') + encodeURIComponent(name) + '=' + encodeURIComponent(value);
		});

		if (query) {
			url += (url.indexOf('?') > 0 ? '&' : '?') + query;
		}

		return url;
	},

	/**
	 * Formats the specified number as a size string for example 1024 becomes 1 KB.
	 *
	 * @method formatSize
	 * @static
	 * @param {Number} size Size to format as string.
	 * @return {String} Formatted size string.
	 */
	formatSize : function(size) {

		if (size === undef || /\D/.test(size)) {
			return plupload.translate('N/A');
		}

		function round(num, precision) {
			return Math.round(num * Math.pow(10, precision)) / Math.pow(10, precision);
		}

		var boundary = Math.pow(1024, 4);

		// TB
		if (size > boundary) {
			return round(size / boundary, 1) + " " + plupload.translate('tb');
		}

		// GB
		if (size > (boundary/=1024)) {
			return round(size / boundary, 1) + " " + plupload.translate('gb');
		}

		// MB
		if (size > (boundary/=1024)) {
			return round(size / boundary, 1) + " " + plupload.translate('mb');
		}

		// KB
		if (size > 1024) {
			return Math.round(size / 1024) + " " + plupload.translate('kb');
		}

		return size + " " + plupload.translate('b');
	},


	/**
	 * Parses the specified size string into a byte value. For example 10kb becomes 10240.
	 *
	 * @method parseSize
	 * @static
	 * @param {String|Number} size String to parse or number to just pass through.
	 * @return {Number} Size in bytes.
	 */
	parseSize : u.Basic.parseSizeStr,


	/**
	 * A way to predict what runtime will be choosen in the current environment with the
	 * specified settings.
	 *
	 * @method predictRuntime
	 * @static
	 * @param {Object|String} config Plupload settings to check
	 * @param {String} [runtimes] Comma-separated list of runtimes to check against
	 * @return {String} Type of compatible runtime
	 */
	predictRuntime : function(config, runtimes) {
		var up, runtime;

		up = new plupload.Uploader(config);
		runtime = Runtime.thatCan(up.getOption().required_features, runtimes || config.runtimes);
		up.destroy();
		return runtime;
	},

	/**
	 * Registers a filter that will be executed for each file added to the queue.
	 * If callback returns false, file will not be added.
	 *
	 * Callback receives two arguments: a value for the filter as it was specified in settings.filters
	 * and a file to be filtered. Callback is executed in the context of uploader instance.
	 *
	 * @method addFileFilter
	 * @static
	 * @param {String} name Name of the filter by which it can be referenced in settings.filters
	 * @param {String} cb Callback - the actual routine that every added file must pass
	 */
	addFileFilter: function(name, cb) {
		fileFilters[name] = cb;
	}
};

//过滤选择的文件,判断文件扩展名
plupload.addFileFilter('mime_types', function(filters, file, cb) {
	if (filters.length && !filters.regexp.test(file.name)) {
		this.trigger('Error', {
			code : plupload.FILE_EXTENSION_ERROR,
			message : plupload.translate('File extension error.'),
			file : file
		});
		cb(false);
	} else {
		cb(true);
	}
});

//过滤选择的文件,判断文件大小
plupload.addFileFilter('max_file_size', function(maxSize, file, cb) {
	var undef;

	maxSize = plupload.parseSize(maxSize);

	// Invalid file size
	if (file.size !== undef && maxSize && file.size > maxSize) {
		this.trigger('Error', {
			code : plupload.FILE_SIZE_ERROR,
			message : plupload.translate('File size error.'),
			file : file
		});
		cb(false);
	} else {
		cb(true);
	}
});

//过滤选择的文件,判断是否选择重复的文件
plupload.addFileFilter('prevent_duplicates', function(value, file, cb) {
	if (value) {
		var ii = this.files.length;
		while (ii--) {
			// Compare by name and size (size might be 0 or undefined, but still equivalent for both)
			if (file.name === this.files[ii].name && file.size === this.files[ii].size) {
				this.trigger('Error', {
					code : plupload.FILE_DUPLICATE_ERROR,
					message : plupload.translate('Duplicate file error.'),
					file : file
				});
				cb(false);
				return;
			}
		}
	}
	cb(true);
});


/**
@class Uploader
@constructor

@param {Object} settings For detailed information about each option check documentation.
	@param {String|DOMElement} settings.browse_button id of the DOM element or DOM element itself to use as file dialog trigger.
	@param {Number|String} [settings.chunk_size=0] Chunk size in bytes to slice the file into. Shorcuts with b, kb, mb, gb, tb suffixes also supported. `e.g. 204800 or "204800b" or "200kb"`. By default - disabled.
	@param {String|DOMElement} [settings.container] id of the DOM element or DOM element itself that will be used to wrap uploader structures. Defaults to immediate parent of the `browse_button` element.
	@param {String|DOMElement} [settings.drop_element] id of the DOM element or DOM element itself to use as a drop zone for Drag-n-Drop.
	@param {String} [settings.file_data_name="file"] Name for the file field in Multipart formated message.
	@param {Object} [settings.filters={}] Set of file type filters.
		@param {String|Number} [settings.filters.max_file_size=0] Maximum file size that the user can pick, in bytes. Optionally supports b, kb, mb, gb, tb suffixes. `e.g. "10mb" or "1gb"`. By default - not set. Dispatches `plupload.FILE_SIZE_ERROR`.
		@param {Array} [settings.filters.mime_types=[]] List of file types to accept, each one defined by title and list of extensions. `e.g. {title : "Image files", extensions : "jpg,jpeg,gif,png"}`. Dispatches `plupload.FILE_EXTENSION_ERROR`
		@param {Boolean} [settings.filters.prevent_duplicates=false] Do not let duplicates into the queue. Dispatches `plupload.FILE_DUPLICATE_ERROR`.
	@param {String} [settings.flash_swf_url] URL of the Flash swf.
	@param {Object} [settings.headers] Custom headers to send with the upload. Hash of name/value pairs.
	@param {String} [settings.http_method="POST"] HTTP method to use during upload (only PUT or POST allowed).
	@param {Number} [settings.max_retries=0] How many times to retry the chunk or file, before triggering Error event.
	@param {Boolean} [settings.multipart=true] Whether to send file and additional parameters as Multipart formated message.
	@param {Object} [settings.multipart_params] Hash of key/value pairs to send with every file upload.
	@param {Boolean} [settings.multi_selection=true] Enable ability to select multiple files at once in file dialog.
	@param {String|Object} [settings.required_features] Either comma-separated list or hash of required features that chosen runtime should absolutely possess.
	@param {Object} [settings.resize] Enable resizng of images on client-side. Applies to `image/jpeg` and `image/png` only. `e.g. {width : 200, height : 200, quality : 90, crop: true}`
		@param {Number} [settings.resize.width] If image is bigger, it will be resized.
		@param {Number} [settings.resize.height] If image is bigger, it will be resized.
		@param {Number} [settings.resize.quality=90] Compression quality for jpegs (1-100).
		@param {Boolean} [settings.resize.crop=false] Whether to crop images to exact dimensions. By default they will be resized proportionally.
	@param {String} [settings.runtimes="html5,flash,silverlight,html4"] Comma separated list of runtimes, that Plupload will try in turn, moving to the next if previous fails.
	@param {String} [settings.silverlight_xap_url] URL of the Silverlight xap.
	@param {Boolean} [settings.send_chunk_number=true] Whether to send chunks and chunk numbers, or total and offset bytes.
	@param {Boolean} [settings.send_file_name=true] Whether to send file name as additional argument - 'name' (required for chunked uploads and some other cases where file name cannot be sent via normal ways).
	@param {String} settings.url URL of the server-side upload handler.
	@param {Boolean} [settings.unique_names=false] If true will generate unique filenames for uploaded files.

*/
plupload.Uploader = function(options) {
	/**
	Fires when the current RunTime has been initialized.
    当Plupload初始化完成后触发，监听函数参数：(uploader)，uploader为当前的plupload实例对象
	@event Init
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	 */

	/**
	Fires after the init event incase you need to perform actions there.
    当Init事件发生后触发，监听函数参数：(uploader)，uploader为当前的plupload实例对象
	@event PostInit
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	 */

	/**
	Fires when the option is changed in via uploader.setOption().
    当使用Plupload实例的setOption()方法改变当前配置参数后触发
    监听函数参数：(uploader,option_name,new_value,old_value)
    uploader为当前的plupload实例对象，option_name为发生改变的参数名称，new_value为改变后的值，old_value为改变前的值
	@event OptionChanged
	@since 2.1
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {String} name Name of the option that was changed
	@param {Mixed} value New value for the specified option
	@param {Mixed} oldValue Previous value of the option
	 */

	/**
	Fires when the silverlight/flash or other shim needs to move.
    当调用plupload实例的refresh()方法后会触发该事件，暂时不清楚还有什么其他动作会触发该事件，但据我测试，把文件添加到上传队列后也会触发该事件。
    监听函数参数：(uploader)
    uploader为当前的plupload实例对象
	@event Refresh
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	 */

	/**
	Fires when the overall state is being changed for the upload queue.
    当上传队列的状态发生改变时触发
    监听函数参数：(uploader)
    uploader为当前的plupload实例对象
	@event StateChanged
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	 */

	/**
	Fires when browse_button is clicked and browse dialog shows.

	@event Browse
	@since 2.1.2
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	 */

	/**
	Fires for every filtered file before it is added to the queue.

	@event FileFiltered
	@since 2.1
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {plupload.File} file Another file that has to be added to the queue.
	 */

	/**
	Fires when the file queue is changed. In other words when files are added/removed to the files array of the uploader instance.
    当上传队列发生变化后触发，即上传队列新增了文件或移除了文件。QueueChanged事件会比FilesAdded或FilesRemoved事件先触发
    监听函数参数：(uploader)
    uploader为当前的plupload实例对象
	@event QueueChanged
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	 */

	/**
	Fires after files were filtered and added to the queue.
    当文件添加到上传队列后触发
    监听函数参数：(uploader,files)
    uploader为当前的plupload实例对象，files为一个数组，里面的元素为本次添加到上传队列里的文件对象
	@event FilesAdded
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {Array} files Array of file objects that were added to queue by the user.
	 */

	/**
	Fires when file is removed from the queue.
    当文件从上传队列移除后触发
    监听函数参数：(uploader,files)
    uploader为当前的plupload实例对象，files为一个数组，里面的元素为本次事件所移除的文件对象
	@event FilesRemoved
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {Array} files Array of files that got removed.
	 */

	/**
	Fires just before a file is uploaded. Can be used to cancel the upload for the specified file
	by returning false from the handler.
    当队列中的某一个文件正要开始上传前触发
    监听函数参数：(uploader,file)
    uploader为当前的plupload实例对象，file为触发此事件的文件对象
	@event BeforeUpload
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {plupload.File} file File to be uploaded.
	 */

	/**
	Fires when a file is to be uploaded by the runtime.

	@event UploadFile
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {plupload.File} file File to be uploaded.
	 */

	/**
	Fires while a file is being uploaded. Use this event to update the current file upload progress.
    会在文件上传过程中不断触发，可以用此事件来显示上传进度
    监听函数参数：(uploader,file)
    uploader为当前的plupload实例对象，file为触发此事件的文件对象
	@event UploadProgress
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {plupload.File} file File that is currently being uploaded.
	 */

	/**
	* Fires just before a chunk is uploaded. This event enables you to override settings
	* on the uploader instance before the chunk is uploaded.
	*
	* @event BeforeChunkUpload
	* @param {plupload.Uploader} uploader Uploader instance sending the event.
	* @param {plupload.File} file File to be uploaded.
	* @param {Object} args POST params to be sent.
	* @param {Blob} chunkBlob Current blob.
	* @param {offset} offset Current offset.
	*/

	/**
	Fires when file chunk is uploaded.
    当使用文件小片上传功能时，每一个小片上传完成后触发
    监听函数参数：(uploader,file,responseObject)
    uploader为当前的plupload实例对象，file为触发此事件的文件对象，responseObject为服务器返回的信息对象，它有以下5个属性：
	offset：该文件小片在整体文件中的偏移量
	response：服务器返回的文本
	responseHeaders：服务器返回的头信息
	status：服务器返回的http状态码，比如200
	total：该文件(指的是被切割成了许多文件小片的那个文件)的总大小，单位为字节
	@event ChunkUploaded
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {plupload.File} file File that the chunk was uploaded for.
	@param {Object} result Object with response properties.
		@param {Number} result.offset The amount of bytes the server has received so far, including this chunk.
		@param {Number} result.total The size of the file.
		@param {String} result.response The response body sent by the server.
		@param {Number} result.status The HTTP status code sent by the server.
		@param {String} result.responseHeaders All the response headers as a single string.
	 */

	/**
	Fires when a file is successfully uploaded.
    当队列中的某一个文件上传完成后触发
    监听函数参数：(uploader,file,responseObject)
    uploader为当前的plupload实例对象，file为触发此事件的文件对象，responseObject为服务器返回的信息对象，它有以下3个属性：
    response：服务器返回的文本
    responseHeaders：服务器返回的头信息
    status：服务器返回的http状态码，比如200
	@event FileUploaded
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {plupload.File} file File that was uploaded.
	@param {Object} result Object with response properties.
		@param {String} result.response The response body sent by the server.
		@param {Number} result.status The HTTP status code sent by the server.
		@param {String} result.responseHeaders All the response headers as a single string.
	 */

	/**
	Fires when all files in a queue are uploaded.
    当上传队列中所有文件都上传完成后触发
    监听函数参数：(uploader,files)
    uploader为当前的plupload实例对象，files为一个数组，里面的元素为本次已完成上传的所有文件对象
	@event UploadComplete
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {Array} files Array of file objects that was added to queue/selected by the user.
	 */

	/**
	Fires when a error occurs.
    当发生错误时触发
    监听函数参数：(uploader,errObject)
    uploader为当前的plupload实例对象，errObject为错误对象，它至少包含以下3个属性(因为不同类型的错误，属性可能会不同)：
    code：错误代码，具体请参考plupload上定义的表示错误代码的常量属性
    file：与该错误相关的文件对象
    message：错误信息
	@event Error
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	@param {Object} error Contains code, message and sometimes file and other details.
		@param {Number} error.code The plupload error code.
		@param {String} error.message Description of the error (uses i18n).
	 */

	/**
	Fires when destroy method is called.
    当调用destroy方法时触发
    监听函数参数：(uploader)
    uploader为当前的plupload实例对象
	@event Destroy
	@param {plupload.Uploader} uploader Uploader instance sending the event.
	 */
	var uid = plupload.guid()
	, settings
	, files = []
	, preferred_caps = {}
	, fileInputs = []
	, fileDrops = []
	, startTime
	, total
	, disabled = false
	, xhr
	;


	// Private methods
	// 私有方法
	function uploadNext() {
		var file, count = 0, i;

		if (this.state == plupload.STARTED) {
			// Find first QUEUED file
			for (i = 0; i < files.length; i++) {
				if (!file && files[i].status == plupload.QUEUED) {
					file = files[i];
					if (this.trigger("BeforeUpload", file)) {
						file.status = plupload.UPLOADING;
						this.trigger("UploadFile", file);
					}
				} else {
					count++;
				}
			}

			// All files are DONE or FAILED
			// 所有的文件完成或是失败
			if (count == files.length) {
				if (this.state !== plupload.STOPPED) {
					this.state = plupload.STOPPED;
					this.trigger("StateChanged");
				}
				this.trigger("UploadComplete", files);//文件上传完成
			}
		}
	}


	function calcFile(file) {
		file.percent = file.size > 0 ? Math.ceil(file.loaded / file.size * 100) : 100;
		calc();
	}


	function calc() {
		var i, file;
		var loaded;
		var loadedDuringCurrentSession = 0;

		// Reset stats
		total.reset();

		// Check status, size, loaded etc on all files
		for (i = 0; i < files.length; i++) {
			file = files[i];

			if (file.size !== undef) {
				// We calculate totals based on original file size
				total.size += file.origSize;

				// Since we cannot predict file size after resize, we do opposite and
				// interpolate loaded amount to match magnitude of total
				loaded = file.loaded * file.origSize / file.size;

				if (!file.completeTimestamp || file.completeTimestamp > startTime) {
					loadedDuringCurrentSession += loaded;
				}

				total.loaded += loaded;
			} else {
				total.size = undef;
			}

			if (file.status == plupload.DONE) {
				total.uploaded++;
			} else if (file.status == plupload.FAILED) {
				total.failed++;
			} else {
				total.queued++;
			}
		}

		// If we couldn't calculate a total file size then use the number of files to calc percent
		if (total.size === undef) {
			total.percent = files.length > 0 ? Math.ceil(total.uploaded / files.length * 100) : 0;
		} else {
			total.bytesPerSec = Math.ceil(loadedDuringCurrentSession / ((+new Date() - startTime || 1) / 1000.0));
			total.percent = total.size > 0 ? Math.ceil(total.loaded / total.size * 100) : 0;
		}
	}


	function getRUID() {
		var ctrl = fileInputs[0] || fileDrops[0];
		if (ctrl) {
			return ctrl.getRuntime().uid;
		}
		return false;
	}


	function runtimeCan(file, cap) {
		if (file.ruid) {
			var info = Runtime.getInfo(file.ruid);
			if (info) {
				return info.can(cap);
			}
		}
		return false;
	}

	//监听绑定事件
	function bindEventListeners() {
		//文件添加，文件移除
		this.bind('FilesAdded FilesRemoved', function(up) {
			up.trigger('QueueChanged');
			up.refresh();
		});

		this.bind('CancelUpload', onCancelUpload);//取消上传

		this.bind('BeforeUpload', onBeforeUpload);//上传之前

		this.bind('UploadFile', onUploadFile);//上传文件

		this.bind('UploadProgress', onUploadProgress);//上传之中

		this.bind('StateChanged', onStateChanged);//改变状态

		this.bind('QueueChanged', calc);//改变队列

		this.bind('Error', onError);//错误

		this.bind('FileUploaded', onFileUploaded);//上传完成

		this.bind('Destroy', onDestroy);//销毁
	}


	function initControls(settings, cb) {
		var self = this, inited = 0, queue = [];

		// common settings
		var options = {
			runtime_order: settings.runtimes,
			required_caps: settings.required_features,
			preferred_caps: preferred_caps,
			swf_url: settings.flash_swf_url,
			xap_url: settings.silverlight_xap_url
		};

		// add runtime specific options if any
		plupload.each(settings.runtimes.split(/\s*,\s*/), function(runtime) {
			if (settings[runtime]) {
				options[runtime] = settings[runtime];
			}
		});

		// initialize file pickers - there can be many
		if (settings.browse_button) {
			plupload.each(settings.browse_button, function(el) {
				queue.push(function(cb) {
					var fileInput = new o.file.FileInput(plupload.extend({}, options, {
						accept: settings.filters.mime_types,
						name: settings.file_data_name,
						multiple: settings.multi_selection,
						container: settings.container,
						browse_button: el
					}));

					fileInput.onready = function() {
						var info = Runtime.getInfo(this.ruid);

						// for backward compatibility
						plupload.extend(self.features, {
							chunks: info.can('slice_blob'),
							multipart: info.can('send_multipart'),
							multi_selection: info.can('select_multiple')
						});

						inited++;
						fileInputs.push(this);
						cb();
					};

					fileInput.onchange = function() {
						self.addFile(this.files);
					};

					fileInput.bind('mouseenter mouseleave mousedown mouseup', function(e) {
						if (!disabled) {
							if (settings.browse_button_hover) {
								if ('mouseenter' === e.type) {
									plupload.addClass(el, settings.browse_button_hover);
								} else if ('mouseleave' === e.type) {
									plupload.removeClass(el, settings.browse_button_hover);
								}
							}

							if (settings.browse_button_active) {
								if ('mousedown' === e.type) {
									plupload.addClass(el, settings.browse_button_active);
								} else if ('mouseup' === e.type) {
									plupload.removeClass(el, settings.browse_button_active);
								}
							}
						}
					});

					fileInput.bind('mousedown', function() {
						self.trigger('Browse');
					});

					fileInput.bind('error runtimeerror', function() {
						fileInput = null;
						cb();
					});

					fileInput.init();
				});
			});
		}

		// initialize drop zones
		if (settings.drop_element) {
			plupload.each(settings.drop_element, function(el) {
				queue.push(function(cb) {
					var fileDrop = new o.file.FileDrop(plupload.extend({}, options, {
						drop_zone: el
					}));

					fileDrop.onready = function() {
						var info = Runtime.getInfo(this.ruid);

						// for backward compatibility
						plupload.extend(self.features, {
							chunks: info.can('slice_blob'),
							multipart: info.can('send_multipart'),
							dragdrop: info.can('drag_and_drop')
						});

						inited++;
						fileDrops.push(this);
						cb();
					};

					fileDrop.ondrop = function() {
						self.addFile(this.files);
					};

					fileDrop.bind('error runtimeerror', function() {
						fileDrop = null;
						cb();
					});

					fileDrop.init();
				});
			});
		}


		plupload.inSeries(queue, function() {
			if (typeof(cb) === 'function') {
				cb(inited);
			}
		});
	}


	function resizeImage(blob, params, cb) {
		var img = new o.image.Image();

		try {
			img.onload = function() {
				// no manipulation required if...
				if (params.width > this.width &&
					params.height > this.height &&
					params.quality === undef &&
					params.preserve_headers &&
					!params.crop
				) {
					this.destroy();
					return cb(blob);
				}
				// otherwise downsize
				img.downsize(params.width, params.height, params.crop, params.preserve_headers);
			};

			img.onresize = function() {
				cb(this.getAsBlob(blob.type, params.quality));
				this.destroy();
			};

			img.onerror = function() {
				cb(blob);
			};

			img.load(blob);
		} catch(ex) {
			cb(blob);
		}
	}

	//设置参数
	//设置某个特定的配置参数,option为参数名称，value为要设置的参数值。option也可以为一个由参数名和参数值键/值对组成的对象，这样就可以一次设定多个参数，此时该方法的第二个参数value会被忽略。
	function setOption(option, value, init) {
		var self = this, reinitRequired = false;

		function _setOption(option, value, init) {
			var oldValue = settings[option];

			switch (option) {
				case 'max_file_size':
					if (option === 'max_file_size') {
						settings.max_file_size = settings.filters.max_file_size = value;
					}
					break;

				case 'chunk_size':
					if (value = plupload.parseSize(value)) {
						settings[option] = value;
						settings.send_file_name = true;
					}
					break;

				case 'multipart':
					settings[option] = value;
					if (!value) {
						settings.send_file_name = true;
					}
					break;

				case 'http_method':
					settings[option] = value.toUpperCase() === 'PUT' ? 'PUT' : 'POST';
					break;

				case 'unique_names':
					settings[option] = value;
					if (value) {
						settings.send_file_name = true;
					}
					break;

				case 'filters':
					// for sake of backward compatibility
					if (plupload.typeOf(value) === 'array') {
						value = {
							mime_types: value
						};
					}

					if (init) {
						plupload.extend(settings.filters, value);
					} else {
						settings.filters = value;
					}

					// if file format filters are being updated, regenerate the matching expressions
					if (value.mime_types) {
						if (plupload.typeOf(value.mime_types) === 'string') {
							value.mime_types = o.core.utils.Mime.mimes2extList(value.mime_types);
						}

						value.mime_types.regexp = (function(filters) {
							var extensionsRegExp = [];

							plupload.each(filters, function(filter) {
								plupload.each(filter.extensions.split(/,/), function(ext) {
									if (/^\s*\*\s*$/.test(ext)) {
										extensionsRegExp.push('\\.*');
									} else {
										extensionsRegExp.push('\\.' + ext.replace(new RegExp('[' + ('/^$.*+?|()[]{}\\'.replace(/./g, '\\$&')) + ']', 'g'), '\\$&'));
									}
								});
							});

							return new RegExp('(' + extensionsRegExp.join('|') + ')$', 'i');
						}(value.mime_types));

						settings.filters.mime_types = value.mime_types;
					}
					break;

				case 'resize':
					if (value) {
						settings.resize = plupload.extend({
							preserve_headers: true,
							crop: false
						}, value);
					} else {
						settings.resize = false;
					}
					break;

				case 'prevent_duplicates':
					settings.prevent_duplicates = settings.filters.prevent_duplicates = !!value;
					break;

				// options that require reinitialisation
				case 'container':
				case 'browse_button':
				case 'drop_element':
						value = 'container' === option
							? plupload.get(value)
							: plupload.getAll(value)
							;

				case 'runtimes':
				case 'multi_selection':
				case 'flash_swf_url':
				case 'silverlight_xap_url':
					settings[option] = value;
					if (!init) {
						reinitRequired = true;
					}
					break;

				default:
					settings[option] = value;
			}

			if (!init) {
				self.trigger('OptionChanged', option, value, oldValue);
			}
		}

		if (typeof(option) === 'object') {
			plupload.each(option, function(value, option) {
				_setOption(option, value, init);
			});
		} else {
			_setOption(option, value, init);
		}

		if (init) {
			// Normalize the list of required capabilities
			settings.required_features = normalizeCaps(plupload.extend({}, settings));

			// Come up with the list of capabilities that can affect default mode in a multi-mode runtimes
			preferred_caps = normalizeCaps(plupload.extend({}, settings, {
				required_features: true
			}));
		} else if (reinitRequired) {
			self.trigger('Destroy');

			initControls.call(self, settings, function(inited) {
				if (inited) {
					self.runtime = Runtime.getInfo(getRUID()).type;
					self.trigger('Init', { runtime: self.runtime });
					self.trigger('PostInit');
				} else {
					self.trigger('Error', {
						code : plupload.INIT_ERROR,
						message : plupload.translate('Init error.')
					});
				}
			});
		}
	}


	// Internal event handlers
	// 内部事件处理程序
    function onBeforeUpload(up, file) {
		// Generate unique target filenames
		// 生成唯一的目标文件名
        if (up.settings.unique_names) {
			var matches = file.name.match(/\.([^.]+)$/), ext = "part";
			if (matches) {
				ext = matches[1];
			}
			file.target_name = file.id + '.' + ext;
		}
	}

	//上传文件
	function onUploadFile(up, file) {
		var url = up.settings.url
		, chunkSize = up.settings.chunk_size
		, retries = up.settings.max_retries
		, features = up.features
		, offset = 0
		, blob
		;

		// make sure we start at a predictable offset
		// 确保我们从一个可预测的偏移量开始
        if (file.loaded) {
			offset = file.loaded = chunkSize ? chunkSize * Math.floor(file.loaded / chunkSize) : 0;
		}

		//处理错误
		function handleError() {
			if (retries-- > 0) {
				delay(uploadNextChunk, 1000);
			} else {
				file.loaded = offset; // reset all progress 重置所有的进程

                up.trigger('Error', {
					code : plupload.HTTP_ERROR,
					message : plupload.translate('HTTP Error.'),
					file : file,
					response : xhr.responseText,
					status : xhr.status,
					responseHeaders: xhr.getAllResponseHeaders()
				});
			}
		}

		//上传下一个分片
		function uploadNextChunk() {
			var chunkBlob, args = {}, curChunkSize;

			// make sure that file wasn't cancelled and upload is not stopped in general
			if (file.status !== plupload.UPLOADING || up.state === plupload.STOPPED) {
				return;
			}

			// send additional 'name' parameter only if required
			if (up.settings.send_file_name) {
				args.name = file.target_name || file.name;
			}

			if (chunkSize && features.chunks && blob.size > chunkSize) { // blob will be of type string if it was loaded in memory
				curChunkSize = Math.min(chunkSize, blob.size - offset);
				chunkBlob = blob.slice(offset, offset + curChunkSize);
			} else {
				curChunkSize = blob.size;
				chunkBlob = blob;
			}

			// If chunking is enabled add corresponding args, no matter if file is bigger than chunk or smaller
			if (chunkSize && features.chunks) {
				// Setup query string arguments
				if (up.settings.send_chunk_number) {
					args.chunk = Math.ceil(offset / chunkSize);
					args.chunks = Math.ceil(blob.size / chunkSize);
				} else { // keep support for experimental chunk format, just in case
					args.offset = offset;
					args.total = blob.size;
				}
			}

			if (up.trigger('BeforeChunkUpload', file, args, chunkBlob, offset)) {
				uploadChunk(args, chunkBlob, curChunkSize);
			}
		}

		function uploadChunk(args, chunkBlob, curChunkSize) {
			var formData;

			xhr = new o.xhr.XMLHttpRequest();//http请求

			// Do we have upload progress support
			// 我们要支持上传进度
			if (xhr.upload) {
				xhr.upload.onprogress = function(e) {
					file.loaded = Math.min(file.size, offset + e.loaded);
					up.trigger('UploadProgress', file);
				};
			}

			xhr.onload = function() {
				// check if upload made itself through
				// 检查是否上传
				if (xhr.status >= 400) {
					handleError();
					return;
				}

				retries = up.settings.max_retries; // reset the counter

				// Handle chunk response
				// 处理分片响应
				if (curChunkSize < blob.size) {
					chunkBlob.destroy();

					offset += curChunkSize;
					file.loaded = Math.min(offset, blob.size);

					up.trigger('ChunkUploaded', file, {
						offset : file.loaded,
						total : blob.size,
						response : xhr.responseText,
						status : xhr.status,
						responseHeaders: xhr.getAllResponseHeaders()
					});

					// stock Android browser doesn't fire upload progress events, but in chunking mode we can fake them
					if (plupload.ua.browser === 'Android Browser') {
						// doesn't harm in general, but is not required anywhere else
						up.trigger('UploadProgress', file);
					}
				} else {
					file.loaded = file.size;
				}

				chunkBlob = formData = null; // Free memory 空闲内存

				// Check if file is uploaded
				// 检查文件是否上传
				if (!offset || offset >= blob.size) {
					// If file was modified, destory the copy
					if (file.size != file.origSize) {
						blob.destroy();
						blob = null;
					}

					up.trigger('UploadProgress', file);

					file.status = plupload.DONE;
					file.completeTimestamp = +new Date();

					up.trigger('FileUploaded', file, {
						response : xhr.responseText,
						status : xhr.status,
						responseHeaders: xhr.getAllResponseHeaders()
					});
				} else {
					// Still chunks left
					delay(uploadNextChunk, 1); // run detached, otherwise event handlers interfere
				}
			};

			xhr.onerror = function() {
				handleError();
			};

			xhr.onloadend = function() {
				this.destroy();
				xhr = null;
			};

			// Build multipart request
			// 构建表单请求
            if (up.settings.multipart && features.multipart) {
				xhr.open(up.settings.http_method, url, true);

				// Set custom headers
				plupload.each(up.settings.headers, function(value, name) {
					xhr.setRequestHeader(name, value);
				});

				formData = new o.xhr.FormData();

				// Add multipart params
				plupload.each(plupload.extend(args, up.settings.multipart_params), function(value, name) {
					formData.append(name, value);
				});

				// Add file and send it
				formData.append(up.settings.file_data_name, chunkBlob);
				xhr.send(formData, {
					runtime_order: up.settings.runtimes,
					required_caps: up.settings.required_features,
					preferred_caps: preferred_caps,
					swf_url: up.settings.flash_swf_url,
					xap_url: up.settings.silverlight_xap_url
				});
			} else {
				// if no multipart, send as binary stream
				url = plupload.buildUrl(up.settings.url, plupload.extend(args, up.settings.multipart_params));

				xhr.open(up.settings.http_method, url, true);

				// Set custom headers
				plupload.each(up.settings.headers, function(value, name) {
					xhr.setRequestHeader(name, value);
				});

				// do not set Content-Type, if it was defined previously (see #1203)
				if (!xhr.hasRequestHeader('Content-Type')) {
					xhr.setRequestHeader('Content-Type', 'application/octet-stream'); // Binary stream header
				}

				xhr.send(chunkBlob, {
					runtime_order: up.settings.runtimes,
					required_caps: up.settings.required_features,
					preferred_caps: preferred_caps,
					swf_url: up.settings.flash_swf_url,
					xap_url: up.settings.silverlight_xap_url
				});
			}
		}


		blob = file.getSource();

		// Start uploading chunks
		// 开始上传分片
		if (!plupload.isEmptyObj(up.settings.resize) && runtimeCan(blob, 'send_binary_string') && plupload.inArray(blob.type, ['image/jpeg', 'image/png']) !== -1) {
			// Resize if required
			resizeImage.call(this, blob, up.settings.resize, function(resizedBlob) {
				blob = resizedBlob;
				file.size = resizedBlob.size;
				uploadNextChunk();
			});
		} else {
			uploadNextChunk();
		}
	}


	function onUploadProgress(up, file) {
		calcFile(file);
	}


	function onStateChanged(up) {
		if (up.state == plupload.STARTED) {
			// Get start time to calculate bps
			startTime = (+new Date());
		} else if (up.state == plupload.STOPPED) {
			// Reset currently uploading files
			for (var i = up.files.length - 1; i >= 0; i--) {
				if (up.files[i].status == plupload.UPLOADING) {
					up.files[i].status = plupload.QUEUED;
					calc();
				}
			}
		}
	}


	function onCancelUpload() {
		if (xhr) {
			xhr.abort();
		}
	}


	function onFileUploaded(up) {
		calc();

		// Upload next file but detach it from the error event
		// since other custom listeners might want to stop the queue
		delay(function() {
			uploadNext.call(up);
		}, 1);
	}


	function onError(up, err) {
		if (err.code === plupload.INIT_ERROR) {
			up.destroy();
		}
		// Set failed status if an error occured on a file
		else if (err.code === plupload.HTTP_ERROR) {
			err.file.status = plupload.FAILED;
			err.file.completeTimestamp = +new Date();
			calcFile(err.file);

			// Upload next file but detach it from the error event
			// since other custom listeners might want to stop the queue
			if (up.state == plupload.STARTED) { // upload in progress
				up.trigger('CancelUpload');
				delay(function() {
					uploadNext.call(up);
				}, 1);
			}
		}
	}


	function onDestroy(up) {
		up.stop();

		// Purge the queue
		plupload.each(files, function(file) {
			file.destroy();
		});
		files = [];

		if (fileInputs.length) {
			plupload.each(fileInputs, function(fileInput) {
				fileInput.destroy();
			});
			fileInputs = [];
		}

		if (fileDrops.length) {
			plupload.each(fileDrops, function(fileDrop) {
				fileDrop.destroy();
			});
			fileDrops = [];
		}

		preferred_caps = {};
		disabled = false;
		startTime = xhr = null;
		total.reset();
	}


	// Default settings
	// 默认参数设置
	settings = {
		chunk_size: 0,//分片上传大小限制
		file_data_name: 'file',//上传文件的字段名称
		filters: {
			mime_types: [],
			prevent_duplicates: false,
			max_file_size: 0
		},//文件过滤条件
		flash_swf_url: 'js/Moxie.swf',//flash的url地址
		http_method: 'POST',//http请求方法
		max_retries: 0,
		multipart: true,
		multi_selection: true,//多选对话框
		resize: false,//修改图片属性 resize: {width: 320, height: 240, quality: 90}
		runtimes: Runtime.order,//上传插件初始化选用那种方式的优先级顺序，如果第一个初始化失败就走第二个，依次类推
		send_file_name: true,//
		send_chunk_number: true,
		silverlight_xap_url: 'js/Moxie.xap'
	};


	setOption.call(this, options, null, true);

	// Inital total state
	total = new plupload.QueueProgress();

	// Add public methods
	plupload.extend(this, {

		/**
		 * Unique id for the Uploader instance.
		 * Plupload实例的唯一标识id
		 * @property id
		 * @type String
		 */
		id : uid,
		uid : uid, // mOxie uses this to differentiate between event targets

		/**
		 * Current state of the total uploading progress. This one can either be plupload.STARTED or plupload.STOPPED.
		 * These states are controlled by the stop/start methods. The default value is STOPPED.
		 * 当前的上传状态，可能的值为plupload.STARTED或plupload.STOPPED，该值由Plupload实例的stop()或statr()方法控制。默认为plupload.STOPPED
		 * @property state
		 * @type Number
		 */
		state : plupload.STOPPED,

		/**
		 * Map of features that are available for the uploader runtime. Features will be filled
		 * before the init event is called, these features can then be used to alter the UI for the end user.
		 * Some of the current features that might be in this map is: dragdrop, chunks, jpgresize, pngresize.
		 *
		 * @property features
		 * @type Object
		 */
		features : {},

		/**
		 * Current runtime name.
		 * 当前使用的上传方式
		 * @property runtime
		 * @type String
		 */
		runtime : null,

		/**
		 * Current upload queue, an array of File instances.
		 * 当前的上传队列，是一个由上传队列中的文件对象组成的数组
		 * @property files
		 * @type Array
		 * @see plupload.File
		 */
		files : files,

		/**
		 * Object with name/value settings.
		 * 当前的配置参数对象
		 * @property settings
		 * @type Object
		 */
		settings : settings,

		/**
		 * Total progess information. How many files has been uploaded, total percent etc.
		 * 表示总体进度信息的QueueProgress对象
		 * @property total
		 * @type plupload.QueueProgress
		 */
		total : total,


		/**
		 * Initializes the Uploader instance and adds internal event listeners.
		 * 初始化Plupload实例
		 * @method init
		 */
		init : function() {
			var self = this, opt, preinitOpt, err;

			preinitOpt = self.getOption('preinit');
			if (typeof(preinitOpt) == "function") {
				preinitOpt(self);
			} else {
				plupload.each(preinitOpt, function(func, name) {
					self.bind(name, func);
				});
			}

			bindEventListeners.call(self);

			// Check for required options
			plupload.each(['container', 'browse_button', 'drop_element'], function(el) {
				if (self.getOption(el) === null) {
					err = {
						code : plupload.INIT_ERROR,
						message : plupload.sprintf(plupload.translate("%s specified, but cannot be found."), el)
					}
					return false;
				}
			});

			if (err) {
				return self.trigger('Error', err);
			}


			if (!settings.browse_button && !settings.drop_element) {
				return self.trigger('Error', {
					code : plupload.INIT_ERROR,
					message : plupload.translate("You must specify either browse_button or drop_element.")
				});
			}


			initControls.call(self, settings, function(inited) {
				var initOpt = self.getOption('init');
				if (typeof(initOpt) == "function") {
					initOpt(self);
				} else {
					plupload.each(initOpt, function(func, name) {
						self.bind(name, func);
					});
				}

				if (inited) {
					self.runtime = Runtime.getInfo(getRUID()).type;
					self.trigger('Init', { runtime: self.runtime });
					self.trigger('PostInit');
				} else {
					self.trigger('Error', {
						code : plupload.INIT_ERROR,
						message : plupload.translate('Init error.')
					});
				}
			});
		},

		/**
		 * Set the value for the specified option(s).
		 * 设置某个特定的配置参数,option为参数名称，value为要设置的参数值。option也可以为一个由参数名和参数值键/值对组成的对象，这样就可以一次设定多个参数，此时该方法的第二个参数value会被忽略。
		 * @method setOption
		 * @since 2.1
		 * @param {String|Object} option Name of the option to change or the set of key/value pairs
		 * @param {Mixed} [value] Value for the option (is ignored, if first argument is object)
		 */
		setOption: function(option, value) {
			setOption.call(this, option, value, !this.runtime); // until runtime not set we do not need to reinitialize
		},

		/**
		 * Get the value for the specified option or the whole configuration, if not specified.
		 * 获取当前的配置参数，参数option为需要获取的配置参数名称，如果没有指定option，则会获取所有的配置参数
		 * @method getOption
		 * @since 2.1
		 * @param {String} [option] Name of the option to get
		 * @return {Mixed} Value for the option or the whole set
		 */
		getOption: function(option) {
			if (!option) {
				return settings;
			}
			return settings[option];
		},

		/**
		 * Refreshes the upload instance by dispatching out a refresh event to all runtimes.
		 * This would for example reposition flash/silverlight shims on the page.
		 * 刷新当前的plupload实例，暂时还不明白什么时候需要使用
		 * @method refresh
		 */
		refresh : function() {
			if (fileInputs.length) {
				plupload.each(fileInputs, function(fileInput) {
					fileInput.trigger('Refresh');
				});
			}
			this.trigger('Refresh');
		},

		/**
		 * Starts uploading the queued files.
		 * 开始上传队列中的文件
		 * @method start
		 */
		start : function() {
			if (this.state != plupload.STARTED) {
				this.state = plupload.STARTED;
				this.trigger('StateChanged');

				uploadNext.call(this);
			}
		},

		/**
		 * Stops the upload of the queued files.
		 * 停止队列中的文件上传
		 * @method stop
		 */
		stop : function() {
			if (this.state != plupload.STOPPED) {
				this.state = plupload.STOPPED;
				this.trigger('StateChanged');
				this.trigger('CancelUpload');
			}
		},


		/**
		 * Disables/enables browse button on request.
		 * 禁用或启用plupload的文件浏览按钮,参数disable为true时为禁用，为false时为启用。默认为true
		 * @method disableBrowse
		 * @param {Boolean} disable Whether to disable or enable (default: true)
		 */
		disableBrowse : function() {
			disabled = arguments[0] !== undef ? arguments[0] : true;

			if (fileInputs.length) {
				plupload.each(fileInputs, function(fileInput) {
					fileInput.disable(disabled);
				});
			}

			this.trigger('DisableBrowse', disabled);
		},

		/**
		 * Returns the specified file object by id.
		 * 通过id来获取文件对象
		 * @method getFile
		 * @param {String} id File id to look for.
		 * @return {plupload.File} File object or undefined if it wasn't found;
		 */
		getFile : function(id) {
			var i;
			for (i = files.length - 1; i >= 0; i--) {
				if (files[i].id === id) {
					return files[i];
				}
			}
		},

		/**
		 * Adds file to the queue programmatically. Can be native file, instance of Plupload.File,
		 * instance of mOxie.File, input[type="file"] element, or array of these. Fires FilesAdded,
		 * if any files were added to the queue. Otherwise nothing happens.
		 * 向上传队列中添加文件，如果成功添加了文件，会触发FilesAdded事件。参数file为要添加的文件,可以是一个原生的文件,或者一个plupload文件对象,或者一个input[type="file"]元素,还可以是一个包括前面那几种东西的数组；fileName为给该文件指定的名称
		 * @method addFile
		 * @since 2.0
		 * @param {plupload.File|mOxie.File|File|Node|Array} file File or files to add to the queue.
		 * @param {String} [fileName] If specified, will be used as a name for the file
		 */
		addFile : function(file, fileName) {
			var self = this
			, queue = []
			, filesAdded = []
			, ruid
			;

			function filterFile(file, cb) {
				var queue = [];
				plupload.each(self.settings.filters, function(rule, name) {
					if (fileFilters[name]) {
						queue.push(function(cb) {
							fileFilters[name].call(self, rule, file, function(res) {
								cb(!res);
							});
						});
					}
				});
				plupload.inSeries(queue, cb);
			}

			/**
			 * @method resolveFile
			 * @private
			 * @param {moxie.file.File|moxie.file.Blob|plupload.File|File|Blob|input[type="file"]} file
			 */
			function resolveFile(file) {
				var type = plupload.typeOf(file);

				// moxie.file.File
				if (file instanceof o.file.File) {
					if (!file.ruid && !file.isDetached()) {
						if (!ruid) { // weird case
							return false;
						}
						file.ruid = ruid;
						file.connectRuntime(ruid);
					}
					resolveFile(new plupload.File(file));
				}
				// moxie.file.Blob
				else if (file instanceof o.file.Blob) {
					resolveFile(file.getSource());
					file.destroy();
				}
				// plupload.File - final step for other branches
				else if (file instanceof plupload.File) {
					if (fileName) {
						file.name = fileName;
					}

					queue.push(function(cb) {
						// run through the internal and user-defined filters, if any
						filterFile(file, function(err) {
							if (!err) {
								// make files available for the filters by updating the main queue directly
								files.push(file);
								// collect the files that will be passed to FilesAdded event
								filesAdded.push(file);

								self.trigger("FileFiltered", file);
							}
							delay(cb, 1); // do not build up recursions or eventually we might hit the limits
						});
					});
				}
				// native File or blob
				else if (plupload.inArray(type, ['file', 'blob']) !== -1) {
					resolveFile(new o.file.File(null, file));
				}
				// input[type="file"]
				else if (type === 'node' && plupload.typeOf(file.files) === 'filelist') {
					// if we are dealing with input[type="file"]
					plupload.each(file.files, resolveFile);
				}
				// mixed array of any supported types (see above)
				else if (type === 'array') {
					fileName = null; // should never happen, but unset anyway to avoid funny situations
					plupload.each(file, resolveFile);
				}
			}

			ruid = getRUID();

			resolveFile(file);

			if (queue.length) {
				plupload.inSeries(queue, function() {
					// if any files left after filtration, trigger FilesAdded
					if (filesAdded.length) {
						self.trigger("FilesAdded", filesAdded);
					}
				});
			}
		},

		/**
		 * Removes a specific file.
		 * 从上传队列中移除文件，参数file为plupload文件对象或先前指定的文件名称
		 * @method removeFile
		 * @param {plupload.File|String} file File to remove from queue.
		 */
		removeFile : function(file) {
			var id = typeof(file) === 'string' ? file : file.id;

			for (var i = files.length - 1; i >= 0; i--) {
				if (files[i].id === id) {
					return this.splice(i, 1)[0];
				}
			}
		},

		/**
		 * Removes part of the queue and returns the files removed. This will also trigger the FilesRemoved and QueueChanged events.
		 * 从上传队列中移除一部分文件，start为开始移除文件在队列中的索引，length为要移除的文件的数量，该方法的返回值为被移除的文件。该方法会触发FilesRemoved 和QueueChanged事件
		 * @method splice
		 * @param {Number} start (Optional) Start index to remove from.
		 * @param {Number} length (Optional) Lengh of items to remove.
		 * @return {Array} Array of files that was removed.
		 */
		splice : function(start, length) {
			// Splice and trigger events
			var removed = files.splice(start === undef ? 0 : start, length === undef ? files.length : length);

			// if upload is in progress we need to stop it and restart after files are removed
			var restartRequired = false;
			if (this.state == plupload.STARTED) { // upload in progress
				plupload.each(removed, function(file) {
					if (file.status === plupload.UPLOADING) {
						restartRequired = true; // do not restart, unless file that is being removed is uploading
						return false;
					}
				});

				if (restartRequired) {
					this.stop();
				}
			}

			this.trigger("FilesRemoved", removed);

			// Dispose any resources allocated by those files
			plupload.each(removed, function(file) {
				file.destroy();
			});

			if (restartRequired) {
				this.start();
			}

			return removed;
		},

		/**
		Dispatches the specified event name and its arguments to all listeners.
        触发某个事件。name为要触发的事件名称，Multiple为传给该事件监听函数的参数，是一个对象
		@method trigger
		@param {String} name Event name to fire.
		@param {Object..} Multiple arguments to pass along to the listener functions.
		*/

		// override the parent method to match Plupload-like event logic
		dispatchEvent: function(type) {
			var list, args, result;

			type = type.toLowerCase();

			list = this.hasEventListener(type);

			if (list) {
				// sort event list by priority
				list.sort(function(a, b) { return b.priority - a.priority; });

				// first argument should be current plupload.Uploader instance
				args = [].slice.call(arguments);
				args.shift();
				args.unshift(this);

				for (var i = 0; i < list.length; i++) {
					// Fire event, break chain if false is returned
					if (list[i].fn.apply(list[i].scope, args) === false) {
						return false;
					}
				}
			}
			return true;
		},

		/**
		Check whether uploader has any listeners to the specified event.
        用来判断某个事件是否有监听函数，name为事件名称
		@method hasEventListener
		@param {String} name Event name to check for.
		*/


		/**
		Adds an event listener by name.
        给某个事件绑定监听函数，name为事件名，func为监听函数，scope为监听函数的作用域，也就是监听函数中this的指向
		@method bind
		@param {String} name Event name to listen for.
		@param {function} fn Function to call ones the event gets fired.
		@param {Object} [scope] Optional scope to execute the specified function in.
		@param {Number} [priority=0] Priority of the event handler - handlers with higher priorities will be called first
		*/
		bind: function(name, fn, scope, priority) {
			// adapt moxie EventTarget style to Plupload-like
			plupload.Uploader.prototype.bind.call(this, name, fn, priority, scope);
		},

		/**
		Removes the specified event listener.
        移除事件的监听函数，name为事件名称，func为要移除的监听函数
		@method unbind
		@param {String} name Name of event to remove.
		@param {function} fn Function to remove from listener.
		*/

		/**
		Removes all event listeners.
        移除所有事件的所有监听函数
		@method unbindAll
		*/


		/**
		 * Destroys Plupload instance and cleans after itself.
		 * 销毁plupload实例
		 * @method destroy
		 */
		destroy : function() {
			this.trigger('Destroy');
			settings = total = null; // purge these exclusively
			this.unbindAll();
		}
	});
};

plupload.Uploader.prototype = o.core.EventTarget.instance;

/**
 * Constructs a new file instance.
 * 构造一个新的文件实例
 * @class File
 * @constructor
 *
 * @param {Object} file Object containing file properties
 * @param {String} file.name Name of the file.
 * @param {Number} file.size File size.
 */
plupload.File = (function() {
	var filepool = {};

	function PluploadFile(file) {

		plupload.extend(this, {

			/**
			 * File id this is a globally unique id for the specific file.
			 * 文件id
			 * @property id
			 * @type String
			 */
			id: plupload.guid(),

			/**
			 * File name for example "myfile.gif".
			 * 文件名，例如"myfile.gif"
			 * @property name
			 * @type String
			 */
			name: file.name || file.fileName,

			/**
			 * File type, `e.g image/jpeg`
			 * 文件类型，例如"image/jpeg"
			 * @property type
			 * @type String
			 */
			type: file.type || '',

			/**
			 * File size in bytes (may change after client-side manupilation).
			 * 文件大小，单位为字节，当启用了客户端压缩功能后，该值可能会改变
			 * @property size
			 * @type Number
			 */
			size: file.size || file.fileSize,

			/**
			 * Original file size in bytes.
			 * 文件的原始大小，单位为字节
			 * @property origSize
			 * @type Number
			 */
			origSize: file.size || file.fileSize,

			/**
			 * Number of bytes uploaded of the files total size.
			 * 文件已上传部分的大小，单位为字节
			 * @property loaded
			 * @type Number
			 */
			loaded: 0,

			/**
			 * Number of percentage uploaded of the file.
			 * 文件已上传部分所占的百分比，如50就代表已上传了50%
			 * @property percent
			 * @type Number
			 */
			percent: 0,

			/**
			 * Status constant matching the plupload states QUEUED, UPLOADING, FAILED, DONE.
			 * 文件的状态，可能为以下几个值之一：plupload.QUEUED, plupload.UPLOADING, plupload.FAILED, plupload.DONE
			 * @property status
			 * @type Number
			 * @see plupload
			 */
			status: plupload.QUEUED,

			/**
			 * Date of last modification.
			 * 文件最后修改的时间
			 * @property lastModifiedDate
			 * @type {String}
			 */
			lastModifiedDate: file.lastModifiedDate || (new Date()).toLocaleString(), // Thu Aug 23 2012 19:40:00 GMT+0400 (GET)


			/**
			 * Set when file becomes plupload.DONE or plupload.FAILED. Is used to calculate proper plupload.QueueProgress.bytesPerSec.
			 * @private
			 * @property completeTimestamp
			 * @type {Number}
			 */
			completeTimestamp: 0,

			/**
			 * Returns native window.File object, when it's available.
			 * 获取原生的文件对象
			 * @method getNative
			 * @return {window.File} or null, if plupload.File is of different origin
			 */
			getNative: function() {
				var file = this.getSource().getSource();
				return plupload.inArray(plupload.typeOf(file), ['blob', 'file']) !== -1 ? file : null;
			},

			/**
			 * Returns mOxie.File - unified wrapper object that can be used across runtimes.
			 * 获取mOxie.File 对象，想了解mOxie是什么东西，可以看下https://github.com/moxiecode/moxie/wiki/API
			 * @method getSource
			 * @return {mOxie.File} or null
			 */
			getSource: function() {
				if (!filepool[this.id]) {
					return null;
				}
				return filepool[this.id];
			},

			/**
			 * Destroys plupload.File object.
			 * 销毁文件对象
			 * @method destroy
			 */
			destroy: function() {
				var src = this.getSource();
				if (src) {
					src.destroy();
					delete filepool[this.id];
				}
			}
		});

		filepool[this.id] = file;
	}

	return PluploadFile;
}());


/**
 * Constructs a queue progress.
 * 构建一个队列进程
 * @class QueueProgress
 * @constructor
 */
 plupload.QueueProgress = function() {
	var self = this; // Setup alias for self to reduce code size when it's compressed

	/**
	 * Total queue file size.
	 * 上传队列中所有文件加起来的总大小，单位为字节
     * @property size
	 * @type Number
	 */
	self.size = 0;

	/**
	 * Total bytes uploaded.
	 * 队列中当前已上传文件加起来的总大小,单位为字节
     * @property loaded
	 * @type Number
	 */
	self.loaded = 0;

	/**
	 * Number of files uploaded.
	 * 已完成上传的文件的数量
     * @property uploaded
	 * @type Number
	 */
	self.uploaded = 0;

	/**
	 * Number of files failed to upload.
	 * 上传失败的文件数量
     * @property failed
	 * @type Number
	 */
	self.failed = 0;

	/**
	 * Number of files yet to be uploaded.
	 * 队列中剩下的(也就是除开已经完成上传的文件)需要上传的文件数量
	 * @property queued
	 * @type Number
	 */
	self.queued = 0;

	/**
	 * Total percent of the uploaded bytes.
	 * 整个队列的已上传百分比，如50就代表50%
     * @property percent
	 * @type Number
	 */
	self.percent = 0;

	/**
	 * Bytes uploaded per second.
	 * 上传速率，单位为 byte/s，也就是 字节/秒
     * @property bytesPerSec
	 * @type Number
	 */
	self.bytesPerSec = 0;

	/**
	 * Resets the progress to its initial values.
	 * 将上传进度重置为初始值
	 * @method reset
	 */
	self.reset = function() {
		self.size = self.loaded = self.uploaded = self.failed = self.queued = self.percent = self.bytesPerSec = 0;
	};
};

exports.plupload = plupload;

}(this, moxie));

}));