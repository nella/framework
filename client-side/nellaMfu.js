/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

jQuery.fn.nellaMultipleFileUploader = function(options) {
	if (!window.FileReader || !window.FormData) {
		return false;
	}
	
	options = jQuery.extend({
		templates: {
			queue: '<div class="nella-file-upload-queue">', 
			queueItem: '<div class="item">', 
			queueItemImg: '<img alt="item image">', 
			removeItemButton: '<a href="#" class="remove-button"><span>remove</span></a>'
		},
		thumbnail: {
			width: 128,
			height: 128
		}, 
		token: 'xxx', 
		image: false, 
		notImageMessage: 'Only for images', 
		submit: null
	}, options);
	
	this.each(function(queueIndex) {
		if (!$(this).is('input[type=file][multiple]')) {
			return false;
		}
		
		$(this).change(function() {
			var $queue = $(options.templates.queue);
			var $this = $(this);
			
			for (var i in this.files) {
				var file = this.files[i];
				var alerted = false;
				
				if (file instanceof File && isImage(file)) {
					readfile(file, function(file, content) {
						$(options.templates.queueItemImg).attr('src', content).load(function() {
							resizeImage(this, options.thumbnail.width, options.thumbnail.height);
							
							$item = $(options.templates.queueItem).append(
								$(options.templates.removeItemButton).click(function() {
									$(this).parents('.nella-file-upload-queue .item').remove();
									return false;
								})
							).append($(this)).hide();
							$item[0].file = file;
							
							$queue.append($item);
							$item.show('fast');
						});
					});
				} else if (file instanceof File && !options.image) {
					$item = $(options.templates.queueItem).append(
						$(options.templates.removeItemButton).click(function() {
							$(this).parents('.nella-file-upload-queue .item').remove();
							return false;
						})
					).append($('<p>').text(file.name));
					$item[0].file = file;
					
					$queue.append($item);
					$item.show('fast');
				} else if (file instanceof File && !alerted) {
					alert(options.notImageMessage);
					alerted = true;
				}
			}
			
			var $actualQueue = $('#' + options.templates.queueIdPrefix + queueIndex);
			if ($actualQueue.get(0) != undefined) {
				$actualQueue.replaceWith($queue);
			} else {
				$(this).before($queue);
			}
		});
		
		$(this).parents('form:last').submit(function() {
			$(this).find('.nella-file-upload-queue').each(function() {
				$(this).find('.item').each(function() {
					$item = $(this);
					$form = $item.parents('form');
					var fd = new FormData();
					fd.append('file', this.file);
					
					$item.find('.remove-button').remove();
					$item.append($('<div class="progressbar">').progressbar({value: 0}));
					$item.parents('.nella-file-upload-queue').next('input[type=file]').remove();
					
					$.ajax({
						processData : false, 
						url: $form.attr('action'), 
						contentType: false, 
						type: $form.attr('method') || 'get', 
						data: fd, 
						context: $item, 
						headers: {
							'X-Uploader': 'Nella Framework - MFU', 
							'X-Nella-MFU-Token': options.token
						}, 
						success: function() {
							$(this).hide('slow', function() {
								$this = $(this);
								$form = $this.parents('form');
								$this.remove();
								
								if ($form.find('.nella-file-upload-queue .item')[0] == undefined) {
									if (options.submit) {
										options.submit($form);
									} else {
										$form[0].submit();
									}
								}
							}); 
						}, 
						progress: function(jqXHR, event) {
							this.context.find('.progressbar').progressbar({
								value: parseInt((event.total / event.loaded) * 100)
							})
						}
					});
				});
			});
			
			return false;
		});
	});
	
	
	function resizeImage(image, height, width)
	{
		var	actualSize = {
			width: image.naturalWidth, 
			height: image.naturalHeight
		};
		var newSize = {
			width: null, 
			height: null
		};
		
		if ((actualSize.width / width) <= (actualSize.height / height)) {
			newSize.height = (actualSize.height > height) ? height : actualSize.height;
			newSize.width = parseInt((newSize.height * actualSize.width) / actualSize.height);
			
		}
		else {
			newSize.width = (actualSize.width > width) ? width : actualSize.width;
			newSize.height = parseInt((newSize.width * actualSize.height) / actualSize.width);
		}

		image.width = newSize.width;
		image.height = newSize.height;
	}
	
	function isImage(file, allowedImageTypes) {
		allowedImageTypes = allowedImageTypes || ['image/jpg', 'image/gif', 'image/png', 'image/jpeg'];
		for (var i in allowedImageTypes) {
			if (file.type.toLowerCase() == allowedImageTypes[i]) {
				return true;
			}
		}

		return false;
	}
	
	function readfile(file, callback) {
		var reader = new FileReader();
		reader.onload = function(e) {
			callback(file, e.target.result);
		};
		reader.readAsDataURL(file);
		
		reader.onerror = function(e) {
			console.log(e);
		};
	}
};

/*!
 * jQuery ajaxProgress Plugin v0.5.0
 * Requires jQuery v1.5.0 or later
 * 
 * http://www.kpozin.net/ajaxprogress
 *
 * (c) 2011, Konstantin Pozin
 * Licensed under MIT license.
 */
(function($) {

    // Test whether onprogress is supported
    var support = $.support.ajaxProgress = ("onprogress" in $.ajaxSettings.xhr());

    // If it's not supported, we can't do anything
    if (!support) {
        return;
    }

    var NAMESPACE = ".ajaxprogress";

    // Create global "ajaxProgress" event
    $.fn.ajaxProgress = function (f) {
        return this.bind("ajaxProgress", f);
    };

    // Hold on to a reference to the jqXHR object so that we can pass it to the progress callback.
    // Namespacing the handler with ".ajaxprogress"
    $("html").bind("ajaxSend" + NAMESPACE, function(event, jqXHR, ajaxOptions) {
        ajaxOptions.__jqXHR = jqXHR;
    });

    /**
     * @param {XMLHttpRequestProgressEvent} evt
     * @param {Object} options jQuery AJAX options
     */
    function handleOnProgress(evt, options) {

        // Trigger the global event.
        // function handler(jqEvent, progressEvent, jqXHR) {}
        if (options.global) {
            $.event.trigger("ajaxProgress", [evt, options.__jqXHR]);
        }

        // Trigger the local event.
        // function handler(jqXHR, progressEvent)
        if (typeof options.progress === "function") {
            options.progress(options.__jqXHR, evt);
        }
    }


    // We'll work with the original factory method just in case
    var makeOriginalXhr = $.ajaxSettings.xhr.bind($.ajaxSettings);

    // Options to be passed into $.ajaxSetup;
    var newOptions = {};

    // Wrap the XMLHttpRequest factory method
    newOptions.xhr = function () {

        // Reference to the extended options object
        var s = this;

        var newXhr = makeOriginalXhr();
        if (newXhr) {
            newXhr.addEventListener("progress", function(evt) {
                handleOnProgress(evt, s);
            });
        }
        return newXhr;
    };

    $.ajaxSetup(newOptions);

})(jQuery);
