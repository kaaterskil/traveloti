/*----- Global Variables ------------------------------------------------*/

// Chat globals
var gChat = null;
var gView = null;
var gLogger = null;
var gEnvironment = 'dev';
// var gEnvironment = 'prod';

/*----- Traveloti -------------------------------------------------------*/

var Traveloti = {
	init : function() {
		// Test if placeholders are supported
		if (Traveloti.supportsInputPlaceholder() == false) {
			$('input').each(function() {
				if (($(this).attr('placeholder') != null) && ($(this).attr('name') != 'query')) {
					$(this).before('<div class="placeholder">' + $(this).attr('placeholder') + '</div>');
				}
			});
		}
		/*----- Jewel button listeners -----*/
		$('.button-confirm').each(function(i) {
			$(this).bind('click', Traveloti.confirmFriendRequest);
		});
		$('.button-hide').each(function(i) {
			$(this).bind('click', Traveloti.ignoreFriendRequest);
		});
		$('.jewel-button').each(function(i) {
			$(this).bind('click', Traveloti.toggleJewel);
		});

		/*----- Chat listeners -----*/
		$('.chat-list-item').each(function(i) {
			$(this).bind('click', Traveloti.openChat);
		});
		$('.dock-chat-button').each(function(i) {
			$(this).bind('click', Traveloti.toggleDockChat);
		});

		/*----- News Feed listeners -----*/
		$('.like-link').each(function(i) {
			$(this).bind('click', Traveloti.inlineLike);
		});

		/*----- Find Traveloti listeners -----*/
		$('.browser-form-element').each(function(i) {
			$(this).bind('change', Traveloti.inlineSubmit2);
		});
		$('.add-traveloti-button').each(function(i) {
			$(this).bind('click', Traveloti.sendFriendRequest);
		});

		/*----- Cover Picture listeners -----*/
		$('#change-cover-btn').click(function() {
			$('#cover-upload-element').click();
		});
		$('#cover-upload-element').change(function() {
			if ($(this).val() != null) {
				$('#upload-cover-form').trigger('submit');
			}
		});
		$('#change-profile-btn').click(function() {
			$('#profile-upload-element').click();
		});
		$('#profile-upload-element').change(function() {
			if ($(this).val() != null) {
				$('#upload-profile-form').trigger('submit');
			}
		});

		/*----- Photo listeners -----*/
		$('#upload-photo-button').click(function() {
			if ($('#photo-upload').hasClass('hidden-element')) {
				$('#photo-upload').toggleClass('hidden-element');
			}
		});
		$('.photo-upload-cancel-button').click(function() {
			if (!$('#photo-upload').hasClass('hidden-element')) {
				$('#photo-upload').toggleClass('hidden-element');
			}
		});

		$('.media-text-content').each(function(i) {
			$(this).click(function() {
				var m = $(this).attr('id').match(/.+(\d)$/);
				switch (m[1]) {
				case '3':
					return Traveloti.inlineLike1(this);
				case '4':
					return Traveloti.inlineComment1(this);
				}
			});
		});
	},

	confirmFriend : function(senderId, requestId, btnContainerId, resultId) {
		$.ajax({
			url : 'base/confirmFriend',
			data : {
				uid : $('#' + senderId).value,
				oid : $('#' + requestId).value
			}
		}).done(function(data) {
			switch (data) {
			case 'success':
				$('#' + btnContainerId).addClass('hidden-element');
				$('#' + resultId).removeClass('hidden-element');
				break;
			case 'duplicate':
				alert("A friend request has already been sent.");
				break;
			case 'failure':
			default:
				alert("Confirm failed! " + data.toString());
			}
		});
	},
	confirmFriendRequest : function() {
		var elementId = this.id.substr(0, 8);
		var uid = $('#' + elementId + '2').val();
		var oid = $('#' + elementId + '1').val();
		$
				.ajax({
					url : 'ajax/confirmFriend/uid/' + uid + '/oid/' + oid
				})
				.done(
						function(data) {
							$('#' + elementId + '5').addClass('hidden-element');
							$('#' + elementId + '6').addClass('hidden-element');
							switch (data) {
							case 'success':
								$('#' + elementId + '7').removeClass(
										'hidden-element');
								break;
							case 'duplicate':
								$('#' + elementId + '7').removeClass(
										'hidden-element');
								$('#' + elementId + '7').text(
										'Duplicate request.');
								break;
							case 'failure':
							default:
								$('#' + elementId + '7').removeClass(
										'hidden-element');
								$('#' + elementId + '7')
										.text(
												'There was a problem processing your directive.');
							}
						});
	},
	composerFocusPhotoUpload : function(formId, uploadId, messageId, fileId) {
		if ($('#' + uploadId).hasClass('hidden-element')
				&& $('#' + fileId).hasClass('hidden-element')) {
			this.toggleHiddenElement(uploadId);
			this.toggleHiddenElement(formId);
		}
	},
	composerFocusUpdateStatus : function(formId, uploadId, messageId, fileId) {
		if ($('#' + formId).hasClass('hidden-element')) {
			this.toggleHiddenElement(formId);
			this.toggleHiddenElement(uploadId);
		}
		if (!$('#' + fileId).hasClass('hidden-element')) {
			$('#' + fileId).addClass('hidden-element');
			$('#' + fileId).attr('value', null);
		}
		$('#' + messageId).attr('placeholder', "What's on your mind...");
		$('#' + messageId).focus();
	},
	composerTogglePhotoUpload : function(formId, uploadId, messageId, fileId) {
		this.toggleHiddenElement(formId);
		this.toggleHiddenElement(fileId);
		this.toggleHiddenElement(uploadId);
		$('#' + messageId).attr('placeholder',
				'Say something about this image...');
	},
	deleteFriendRequest : function(senderId, requestId, btnContainerId,
			resultId) {
		var uid = $('#' + senderId).value;
		var oid = $('#' + requestId).value;
		$.ajax({
			url : 'ajax/deleteFriendRequest/uid/' + uid + '/oid/' + oid
		}).done(function(data) {
			$('#' + btnContainerId).addClass('hidden-element');
			$('#' + resultId).removeClass('hidden-element');
			if (data == 'success') {
				$('#' + resultId).text('Friend request deleted.');
			} else {
				$('#' + resultId).text('There was a problem: ' + data);
			}
		});
	},
	equalizeColumnHeights : function() {
		var col1 = $('#content-column');
		var col2 = $('#roster-container');
		var tallest = Math.max(col1.height(), col2.height());
		col1.height(tallest);
		col2.height(tallest);
	},
	ignoreFriendRequest : function() {
		var elementId = this.id.substr(0, 8);
		var uid = $('#' + elementId + '2').val();
		var oid = $('#' + elementId + '1').val();
		$.ajax({
			url : 'ajax/ignoreFriendRequest/uid/' + uid + '/oid/' + oid
		}).done(function(data) {
			$('#' + elementId + '5').addClass('hidden-element');
			$('#' + elementId + '6').addClass('hidden-element');
			switch (data) {
			case 'success':
				$('#' + elementId + '8').removeClass('hidden-element');
				break;
			case 'duplicate':
				$('#' + elementId + '8').removeClass('hidden-element');
				$('#' + elementId + '8').text('Duplicate request.');
				break;
			case 'failure':
			default:
				$('#' + elementId + '8').removeClass('hidden-element');
				$('#' + elementId + '8').text('There was a problem: ' + data);
			}
		});
	},
	inlineLike : function() {
		var parentId = $(this).attr('id').substr(0, 8);
		var m = $('#' + parentId).attr('data-id').match(/(\w+)-(\d+)/);
		$.ajax({
			url : '/ajax/addLike/uid/' + m[2] + '/oid/' + m[1]
		}).done(function(data) {
			if (data == 'success') {
				location.reload();
			} else {
				alert('Application error: ' + data);
			}
		});
	},
	inlineLike1 : function(e) {
		var pId = e.id.substr(0, 8);
		var m = $('#' + pId).attr('data-id').match(/(\w+)-(\d+)/);
		$.ajax({
			url : '/ajax/addLike/uid/' + m[2] + '/oid/' + m[1]
		}).done(function(data) {
			if (data == 'success') {
				var re = $('#' + pId + '5').find('.media-meta-like');
				re.text(parseInt(re.text()) + 1);
			} else {
				alert('Application error: ' + data);
			}
		});
	},
	inlineSubmit : function(event, eId) {
		if (event && event.keyCode == 13) {
			$('#' + eId).trigger('submit');
		}
		return;
	},
	inlineSubmit2 : function() {
		$(this).closest('form').submit();
	},
	openFileUploadElement : function(eId) {
		$('#' + eId).click();
	},
	openChat : function() {
		var uid = $(this).attr('data-id');
		$.ajax({
			url : '/ajax/fetchChatReceiver/uid/' + uid
		}).done(function(data) {
			var obj = jQuery.parseJSON(data);
			if (!obj.error) {
				// Set recipient
				XMPP_PARTNER = obj.username + '@' + XMPP_HOST;
				// Create room
				var time = new Date();
				var roomJid = time.getTime() + '@' + XMPP_HOST;
				room = new Room(gChat, roomJid);
				// Open chat flyout
				$('.chat-titlebar-text').html(obj.displayName);
				$('.chat-titlebar-text').attr('href', obj.link);
				if ($('#dock-chat-flyout').hasClass('toggle-target-closed')) {
					$('#dock-chat-flyout').toggleClass('toggle-target-closed');
					$('#dock-chat').toggleClass('open-flyout');
				}
				$('#chatIn').focus();
			}
		});
	},
	sendFriendRequest : function() {
		var elementId = this.id.substr(0, 8);
		$.ajax({
			url : '/ajax/sendFriendRequest',
			data : {
				uid : $('#' + elementId).val()
			}
		}).done(function(data) {
			switch (data) {
			case 'success':
				$('#' + elementId + '2').addClass('hidden-element');
				$('#' + elementId + '3').removeClass('hidden-element');
				break;
			case 'duplicate':
				alert("A friend request has already been sent.");
				break;
			case 'failure':
			default:
				alert("Request failed! " + data);
			}
		});
	},
	submitForm : function(eId) {
		return $('#' + eId).trigger('submit');
	},
	supportsInputPlaceholder : function() {
		var e = document.createElement('input');
		return 'placeholder' in e;
	},
	timedRefresh : function(interval) {
		setTimeout(function() {
			location.reload(true);
		}, interval);
	},
	toggleAlbumLocContainer : function(e1Id, e2Id) {
		toggleHiddenElement(e1Id);
		$('#' + e1Id).toggleClass('places-typeahead-view-populated');
		if (e2Id != null) {
			toggleLocationList(e2Id);
		}
	},
	toggleHiddenElement : function(eId) {
		$('#' + eId).toggleClass('hidden-element');
	},

	toggleImgLocContainer : function(e1Id, e2Id, e3Id, e4Id) {
		$('#' + e1Id).toggleClass('show-controls focused-input');
		toggleLocationList(e2Id);
		toggleHiddenElement(e3Id);
		if (e4Id != null) {
			$('#' + e4Id).focus();
		}
	},
	toggleDockChat : function() {
		$('#dock-chat-flyout').toggleClass('toggle-target-closed');
		$('#dock-chat').toggleClass('open-flyout');
	},
	toggleJewel : function() {
		var jewelType = $(this).parent().attr('id');
		switch (jewelType) {
		case 'requests-jewel':
			$('#requests-flyout').toggleClass('toggle-target-closed');
			$('#requests-jewel').toggleClass('open-flyout');
			if (!$('#notifications-flyout').hasClass('toggle-target-closed')) {
				$('#notifications-flyout').addClass('toggle-target-closed');
				$('#notifications-jewel').removeClass('open-flyout');
			}
			break;
		case 'notifications-jewel':
			$('#notifications-flyout').toggleClass('toggle-target-closed');
			$('#notifications-jewel').toggleClass('open-flyout');
			if (!$('requests-flyout').hasClass('toggle-target-closed')) {
				$('#requests-flyout').addClass('toggle-target-closed');
				$('#requests-jewel').removeClass('open-flyout');
			}
			break;
		default:
		}
	},
	toggleJewel1 : function(type) {
		$('#' + type + '-flyout').toggleClass('toggle-closed');
		$('#' + type + '-jewel').toggleClass('toggle-open');
	},
	toggleLocationList : function(eId) {
		$('#' + eId).toggleClass('place places-typeahead-focused');
	},
	toggleParentClass : function(e, clazz) {
		var pId = e.parentNode.id;
		$('#' + pId).toggleClass(clazz);
	}
};

/*----- Chat Initialization -------------------------------------------*/

var TravelotiChatInit = {
	init : function() {
		/*----- Toggle debug window -----*/
		$('#debug').click(function() {
			$('#control-wrapper').toggleClass('hidden-element');
		});
		/*----- Toggle Chat window -----*/
		$('#chat-flyout').click(function() {
			$('#chat-area').toggleClass('hidden-element');
		});
		/*----- Sort Chat window tabs -----*/
		$('#chat-area').tabs().find('.ui-tabs-nav').sortable({
			axis : 'x'
		});
		/*----- Start Chat -----*/
		$('.roster-contact')
				.live(
						'click',
						function() {
							var jid = $(this).find('.roster-name').attr('jid');
							var name = $(this).find('.roster-name').text();
							var jidId = gChat.jidToId(jid);

							if ($('#chat-' + jidId).length == 0) {
								if ($('#chat-area').hasClass('hidden-element')) {
									$('#chat-area').toggleClass(
											'hidden-element');
								}
								$('#chat-area').tabs('add', '#chat-' + jidId,
										name);
								$('#chat-' + jidId)
										.append(
												'<div class="chat-messages"></div>'
														+ '<div class="chat-input-container">'
														+ '<input type="text" class="chat-input input-text" />'
														+ '</div>');
								$('#chat-' + jidId).data('jid', jid);
							}
							$('#chat-area').tabs('select', '#chat-' + jidId);
							$('#chat-' + jidId + ' input').focus();
						});
		/*----- Compose chat message -----*/
		$('.chat-input')
				.live(
						'keypress',
						function(ev) {
							var jid = $(this).parent().parent().data('jid');
							if (ev.which == 13) {
								ev.preventDefault();
								var text = $(this).val();
								gChat.sendChat(jid, text);
								$(this)
										.parent()
										.siblings('.chat-messages')
										.append(
												'<div class="chat-message me clearfix">'
														+ '<div class="chat-name">'
														+ Strophe
																.getNodeFromJid(gChat.conn.jid)
														+ '</div><div class="chat-text">'
														+ text
														+ '</div><div class="chat-text-nub"></div></div>');
								gChat.scrollChat(gChat.jidToId(jid));

								$(this).val('');
								$(this).parent().data('composing', false);
							} else {
								var composing = $(this).parent().data(
										'comoposing');
								gChat.sendComposing(jid, composing);
								$(this).parent().data('composing', true);
							}
						});

		/*----- Functions not used -----*/
		$('#disconnect').click(function() {
			gChat.disconnect();
		});
		$('#chat-dialog')
				.dialog(
						{
							autoOpen : false,
							draggable : false,
							modal : true,
							title : 'Start a Chat',
							buttons : {
								'Start' : function() {
									var jid = $('#chat-jid').val()
											.toLowerCase();
									var jidId = gChat.jidToId(jid);

									$('#chat-area').tabs('add',
											'#chat-' + jidId, jid);
									$('#chat-' + jidId)
											.append(
													'<div class="chat-messages"></div>'
															+ '<div class="chat-input-container">'
															+ '<input type="text" class="chat-input input-text" />'
															+ '</div>');
									$('#chat-' + jidId).data('jid', jid);
									$('#chat-area').tabs('select',
											'#chat-' + jidId);
									$('#chat-' + jidId + ' input').focus();
									$('#chat-jid').val('');
									$(this).dialog('close');
								}
							}
						});
		$('#new-chat').click(function() {
			$('#chat-dialog').dialog('open');
		});
		$('#contact-dialog').dialog({
			autoOpen : false,
			draggable : false,
			modal : true,
			title : 'Add Contact',
			buttons : {
				'Add' : function() {
					$(document).trigger('contact_added', {
						jid : $('#contact-jid').val().toLowerCase(),
						name : $('#contact-name').val()
					});
					$('#contact-jid').val('');
					$('#contact-name').val('');
					$(this).dialog('close');
				}
			}
		});
		$('#new-contact').click(function(ev) {
			$('#contact-dialog').dialog('open');
		});
		$('#approve-dialog').dialog({
			autoOpen : false,
			draggable : false,
			modal : true,
			title : 'Subscription Request',
			buttons : {
				'Deny' : function() {
					gChat.denySubscriber();
					$(this).dialog('close');
				},
				'Approve' : function() {
					gChat.approveSubscriber();
					$(this).dialog('close');
				}
			}
		});
	}
};

/*----- Actions -------------------------------------------------------*/

$(document).ready(function() {
	Traveloti.init();
	Traveloti.equalizeColumnHeights();

	$(document).click(function(event) {
		if (Traveloti.composerController) {
			Traveloti.composerController(event);
		}
	});

	// Google Places
	$('#query').geocomplete();
	$('#location').geocomplete();
	$('#album_location').geocomplete();
	$('.input-location').geocomplete();

	// Chat
	if (window.location.hash == '#debug') {
		$('#control-wrapper').fadeIn();
	}
	var gLogger = Log.getInstance('logger-log', 'logCheck');
	Log.instance.setLevel(Log.level.VERBOSE);
	Log.instance.clear();
	if (gChat == null) {
		gChat = new Chat();
		gView = new ChatView(gChat);
		gView.Create();

		if (gEnvironment == 'dev') {
			gChat.startup();
		} else {
			// Google.handleClientLoad();
		}
	}

	var nickname = getCookie('nickname');
	if (nickname != null && nickname != '') {
		XMPP_USER = nickname + '@' + XMPP_HOST;
		XMPP_PASS = nickname;
		gChat.setUsername(nickname);
		$('#nickname').val(nickname);

		gChat.connect(XMPP_USER, XMPP_PASS);
	}

	var unique = getCookie('unique');
	if (unique == null || unique == '') {
		unique = Math.floor(Math.random() * 10000000);
		setCookie('unique', unique, 365);
	}
	if (unique != null && unique != '') {
		gChat.setUnique(unique);
	}
	TravelotiChatInit.init();
});

// Chat
$(window).unload(function() {
	if (gChat) {
		gChat.shutdown();
		gChat.unregisterView('global');
		gView.Delete();
		delete gView;
		delete gChat;
	}
});