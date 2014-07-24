/*----- Miscellaneous -------------------------------------------------------*/

var TRAVELOTI_BOSH_SERVICE = '/xmpp-httpbind';
var XMPP_HOST = 'localhost';
var XMPP_SYSTEMSENDER = 'admin@localhost';
var XMPP_USER = '';
var XMPP_PASS = '';
var XMPP_PARTNER = '';

if (!Function.prototype.bind) {
	Function.prototype.bind = function(fn) {
		var m = this;
		return function() {
			return m.apply(fn, arguments);
		}
	}
}

/*----- Logger -------------------------------------------------------*/

function Logger(destinationTextarea, gateCheckbox) {
	this.level = Log.level.WARNING;
	this.textarea = destinationTextarea;
	this.check = gateCheckbox;
}
Logger.prototype = {
	out : function(level, text) {
		if (level > this.level) {
			return;
		}
		var logCheck = document.getElementById(this.check);
		if (logCheck.checked) {
			$('#' + this.textarea).append(
					'<p>' + Log.prefix[level] + ' '
							+ text.replace(/</ig, '&lt;') + '</p>');
			var eOut = document.getElementById(this.textarea);
			if (eOut) {
				eOut.scrollTop = eOut.scrollHeight;
			}
		}
	},
	setLevel : function(level) {
		var oldLevel = this.level;
		this.level = level;
		return oldLevel;
	},
	clear : function() {
		$('#' + this.textarea).html('');
	}
};

var Log = {
	// Properties
	instance : null,
	level : {
		NONE : 0,
		FATAL : 1,
		ERROR : 2,
		WARNING : 3,
		USER_ERROR : 4,
		DEBUG : 5,
		INFO : 6,
		IO : 7,
		TRACE : 8,
		VERBOSE : 9
	},
	prefix : [ '', 'FATAL', 'ERROR', 'WARN.', 'USER.', '#####', 'INFO.',
			'IO...', 'TRACE', '.....' ],

	// Methods
	fatal : function(text) {
		this.instance.out(this.level.FATAL, text);
	},
	error : function(text) {
		this.instance.out(this.level.ERROR, text);
	},
	warning : function(text) {
		this.instance.out(this.level.WARNING, text);
	},
	userError : function(text) {
		this.instance.out(this.level.USER_ERROR, text);
	},
	debug : function(text) {
		this.instance.out(this.level.DEBUG, text);
	},
	info : function(text) {
		this.instance.out(this.level.INFO, text);
	},
	io : function(text) {
		this.instance.out(this.level.IO, text);
	},
	trace : function(text) {
		this.instance.out(this.level.TRACE, text);
	},
	verbose : function(text) {
		this.instance.out(this.level.VERBOSE, text);
	},
	isVerbose : function() {
		return (this.instance.level >= this.level.VERBOSE);
	},
	getInstance : function(destinationTextarea, gateCheckbox) {
		if (this.instance == null) {
			this.instance = new Logger(destinationTextarea, gateCheckbox);
		}
		return this.instance;
	}
};

/*----- Cookies -------------------------------------------------------*/

function setCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toUTCString();
	} else {
		var expires = '';
	}
	document.cookie = name + '=' + value + expires + '; path=/';
}

function getCookie(name) {
	var nameEQ = name + '=';
	var ca = document.cookie.split(';');
	for ( var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1, c.length);
		}
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length, c.length);
		}
	}
	return null;
}

function deleteCookie(name) {
	setCookie(name, '', -1);
}

/*----- Observable -------------------------------------------------------*/

function Observable() {
	this.observers = new Object();
}
Observable.prototype = {
	add : function(name, observer) {
		this.observers[name] = observer;
	},
	remove : function(name) {
		delete this.observers[name];
	},
	notify : function(msg) {
		if (Log.isVerbose()) {
			var listeners = '(';
			var first = true;
			for ( var name in this.observers) {
				if (!first) {
					listeners += ',';
				}
				first = false;
				listeners += name;
			}
			listeners += ')';
			Log.verbose('Observable.notify ' + listeners + ' ' + $.param(msg));
		}

		for ( var name in this.observers) {
			try {
				this.observers[name](msg);
			} catch (e) {
				Log.error('Observable.notify ' + name + ' ' + e.message);
			}
		}
	}
};

/*----- Chat -------------------------------------------------------*/

function Chat(content) {
	this.conn = null;
	this.connStatus = 0;
	this.content = content;
	this.ownJid = '';
	this.listeners = new Observable();
	this.pendingSubscriber = null;
	this.username = '';
	this.unique = '';
};
Chat.prototype = {
	/*----- Action methods -------------------------*/

	startup : function() {
		Log.trace('Chat.startup ' + TRAVELOTI_BOSH_SERVICE);
		this.conn = new Strophe.Connection(TRAVELOTI_BOSH_SERVICE);
		if (this.conn) {
			this.conn.xmlInput = function(data) {
				Strophe.forEachChild(data, null, function(child) {
					Log.io('RECV: ' + Strophe.serialize(child));
				});
			};
			this.conn.xmlOutput = function(data) {
				Strophe.forEachChild(data, null, function(child) {
					Log.io('SEND: ' + Strophe.serialize(child));
				});
			};
		}
	},
	shutdown : function() {
		Log.trace('Chat.shutdown');
		if (this.conn) {
			this.disconnect();
		}
	},
	sendChat : function(jid, text) {
		Log.trace('Chat.sendChat ' + text);
		if (text != '') {
			if (this.connStatus == Strophe.Status.CONNECTED) {
				var sendText = text;
				sendText = '[' + this.unique + '-' + this.username + ']' + text;

				var stanza = $msg({
					to : jid,
					type : 'chat'
				}).c('body').t(text).up().c('active', {
					xmlns : 'http://jabber.org/protocol/chatstates'
				});
				this.conn.send(stanza.tree());

				if (this.username != '') {
					this.listeners.notify({
						'method' : 'chatIn',
						'from' : this.username,
						'to' : jid,
						'text' : text
					});
				} else {
					this.listeners.notify({
						'method' : 'chatIn',
						'from' : this.ownJid,
						'to' : jid,
						'text' : text
					});
				}
			}
		}
	},
	sendComposing : function(jid, data) {
		Log.trace('Chat.sendComposing to ' + jid);
		if (!data) {
			if (this.connStatus == Strophe.Status.CONNECTED) {
				var stanza = $msg({
					to : jid,
					type : 'chat'
				}).c('composing', {
					xmlns : 'http://jabber.org/protocol/chatstates'
				});
				this.conn.send(stanza);
			}
		}
	},
	getRoster : function() {
		Log.trace('Chat.getRoster');
		var stanza = $iq({
			type : 'get'
		}).c('query', {
			xmlns : 'jabber:iq:roster'
		});
		this.conn.sendIQ(stanza, this.onRoster.bind(this));
		this.conn.addHandler(this.onRosterChanged.bind(this),
				'jabber:iq:roster', 'iq', 'set');
	},
	addContact : function(data) {
		Log.trace('Chat.addContact ' + data.jid);
		var stanza1 = $iq({
			type : 'set'
		}).c('query', {
			xmlns : 'jabber:iq:roster'
		}).c('item', data);
		var stanza2 = $pres({
			to : data.jid,
			type : 'subscribe'
		});
		this.conn.sendIQ(stanza1);
		this.conn.send(stanza2);
	},
	approveSubscriber : function() {
		Log.trace('Chat.approveSubscriber ' + this.pendingSubscriber);
		var stanza1 = $pres({
			to : this.pendingSubscriber,
			type : 'subscribed'
		});
		var stanza2 = $pres({
			to : this.pendingSubscriber,
			type : 'subscribe'
		});
		this.conn.send(stanza1);
		this.conn.send(stanza2);
		this.pendingSubscriber = null;
	},
	denySubscriber : function() {
		Log.trace('Chat.denySubscriber ' + this.pendingSubscriber);
		var stanza = $pres({
			to : this.pendingSubscriber,
			type : 'unsubscribed'
		});
		this.conn.send(stanza);
		this.pendingSubscriber = null;
	},
	ignoreChat : function(jid) {
		Log.trace('Chat.ignoreChat');
		var stanza = $pres({
			to : jid,
			type : 'unavailble'
		});
		this.conn.send(stanza);
	},

	/*----- Connection methods -------------------------*/

	clientOnline : function() {
		Log.trace('Chat.clientOnline');
		this.getRoster();
	},
	clientOffline : function() {
		Log.trace('Chat.clientOffline');
		this.conn.send($pres({
			type : 'unavailable'
		}));
		this.pendingSubscriber = null;
	},
	connect : function(jid, pass) {
		Log.trace('Chat.connect ' + jid);
		this.ownJid = jid;
		if (this.conn) {
			this.conn.connect(jid, pass, this.onConnectionStatus.bind(this));
		}
	},
	disconnect : function() {
		Log.trace('Chat.disconnect');
		this.clientOffline();
		window.setTimeout(this.hardDisconnect.bind(this), 100);
	},
	hardDisconnect : function() {
		Log.trace('Chat.hardDisconnect');
		if (this.conn) {
			this.conn.disconnect('by Chat.disconnect intentionally');
			delete this.conn;
		}
	},

	/*----- Event methods -------------------------*/

	onConnected : function() {
		Log.trace('Chat.onConnected');
		this.conn.addHandler(this.onPresence.bind(this), null, 'presence');
		this.conn.addHandler(this.onMessage.bind(this), null, 'message');
		this.clientOnline();
	},
	onConnectionStatus : function(status) {
		this.connStatus = status;
		if (status == Strophe.Status.CONNECTING) {
			Log.info('Strophe is connecting');
			this.listeners.notify({
				'method' : 'ConnectionConnecting'
			});
		} else if (status == Strophe.Status.CONNFAIL) {
			Log.info('Strophe failed to connect');
			this.listeners.notify({
				'method' : 'ConnectionFailed'
			});
		} else if (status == Strophe.Status.DISCONNECTING) {
			Log.info('Strophe is disconnecting');
			this.listeners.notify({
				'method' : 'ConnectionDisconnecting'
			});
		} else if (status == Strophe.Status.DISCONNECTED) {
			Log.info('Strophe is disconnected');
			this.onDisconnected();
			this.listeners.notify({
				'method' : 'ConnectionDisconnected'
			});
		} else if (status == Strophe.Status.CONNECTED) {
			Log.info('Strophe is connected');
			this.listeners.notify({
				'method' : 'ConnectionConnected'
			});
			this.onConnected();
		}
	},
	onDisconnected : function() {
		Log.trace('Chat.onDisconnected');
		this.pendingSubscriber = null;
		$('#roster-area ul').empty();
		$('#chat-area ul').empty();
		$('#chat-area div-').remove();
	},
	onMessage : function(stanza) {
		Log.trace('Chat.onMessage');
		var fullJid = $(stanza).attr('from');
		var type = $(stanza).attr('type');
		var jid = Strophe.getBareJidFromJid(fullJid);
		var jidId = this.jidToId(jid);
		var username = this.jidToUsername(jid);

		if (type == 'error') {
			Log
					.warning('Chat.onMessage error message from ' + jid
							+ ' ignored');
			return true;
		}

		if($("#chat-area").hasClass('hidden-element')) {
			$('#chat-area').toggleClass('hidden-element');
		}
		
		if ($('#chat-' + jidId).length == 0) {
			$('#chat-area').tabs('add', '#chat-' + jidId, username);
			$('#chat-' + jidId)
					.append(
							'<div class="chat-messages"></div>'
									+ '<div class="chat-input-container">'
									+ '<input type="text" class="chat-input input-text" />'
									+ '</div>');
		}

		$('#chat-' + jidId).data('jid', fullJid);
		$('#chat-area').tabs('select', '#chat-' + jidId);
		$('#chat-' + jidId + ' input').focus();

		var composing = $(stanza).find('composing');
		if (composing.length > 0) {
			if ($('#chat-' + jidId + ' .chat-event').length == 0) {
				$('#chat-' + jidId + ' .chat-messages').append(
						'<div class="chat-event">'
								+ Strophe.getNodeFromJid(jid)
								+ ' is typing...</div>');
				this.scrollChat(jidId);
			}
		}

		var body = $(stanza).find('html > body');
		if (body.length == 0) {
			body = $(stanza).find('body');
			if (body.length > 0) {
				body = body.text();
			} else {
				body = null;
			}
		} else {
			body = body.contents();

			var span = $('<span></span>');
			body.each(function() {
				if (document.importNode) {
					$(document.importNode(this, true)).append(span);
				} else {
					span.append(this.xml);
				}
			});
			body = span;
		}
		if (body) {
			$('#chat-' + jidId + ' .chat-event').remove();
			$('#chat-' + jidId + ' .chat-messages').append(
					'<div class="chat-message clearfix">' + '<div class="chat-name">'
							+ Strophe.getNodeFromJid(jid) + '</div>'
							+ '<div class="chat-text"></div>' 
							+ '<div class="chat-text-nub"></div></div>');
			$('#chat-' + jidId + ' .chat-message:last .chat-text').append(body);
			this.scrollChat(jidId);
		}
		return true;
	},
	onPresence : function(stanza) {
		Log.trace('Chat.onPresence');
		var type = $(stanza).attr('type');
		var from = $(stanza).attr('from');
		var jidId = this.jidToId(from);

		if (type == 'subscribe') {
			this.pendingSubscriber = from;
			$('#approve-jid').text(Strophe.getBareJidFromJid(from));
			$('#approve-dialog').dialog('open');
		} else {
			var contact = $('#roster-area li#' + jidId + ' .roster-contact')
					.removeClass('online').removeClass('away').removeClass(
							'offline');
			if (type == 'unavailable') {
				contact.addClass('offline');
			} else {
				var show = $(stanza).find('show').text();
				if (show == '' || show == 'chat') {
					contact.addClass('online');
				} else {
					contact.addClass('away');
				}
			}
			var li = contact.parent();
			li.remove();
			this.insertContact(li);
			this.insertThumbnail(from);
		}

		jidId = this.jidToId(from);
		$('#chat-' + jidId).data('jid', Strophe.getBareJidFromJid(from));
		return true;
	},
	onRoster : function(stanza) {
		Log.trace('Chat.onRoster');
		$(stanza)
				.find('item')
				.each(
						function() {
							var img = '';
							var jid = $(this).attr('jid');
							var name = $(this).attr('name') || jid;

							// transform jid into an id
							Log.trace('Building contact list...');
							var jidId = gChat.jidToId(jid);
							var html = '<li id="'
									+ jidId
									+ '" class="roster-item">'
									+ '<a class="roster-contact offline" tab-index="0">'
									+ '<div class="roster-thumbnail-wrapper"></div>'
									+ '<div class="roster-status"></div>'
									+ '<div class="roster-content">'
									+ '<div class="roster-name" jid="' + jid
									+ '">' + name + '</div></div></a></li>';
							gChat.insertContact(html);
							gChat.insertThumbnail(jid);
						});
		this.conn.send($pres());
	},
	onRosterChanged : function(stanza) {
		Log.trace('Chat.onRosterChanged');
		$(stanza)
				.find('item')
				.each(
						function() {
							var sub = $(this).attr('subscription');
							var jid = $(this).attr('jid');
							var name = $(this).attr('name') || jid;
							var jidId = gChat.jidToId(jid);

							if (sub == 'remove') {
								$('#' + jidId).remove();
							} else {
								var html = '<li id="'
										+ jidId
										+ '" class="roster-item">'
										+ '<a class="'
										+ ($('#' + jidId).attr('class') || 'roster-contact offline')
										+ '" tab-index="0">'
										+ '<div class="roster-thumbnail-wrapper"></div>'
										+ '<div class="roster-status"></div>'
										+ '<div class="roster-content">'
										+ '<div class="roster-name" jid="'
										+ jid + '">' + name
										+ '</div></div></a></li>';
								if ($('#' + jidId).length > 0) {
									$('#' + jidId).replaceWith($(html));
								} else {
									gChat.insertContact(html);
									gChat.insertThumbnail(jid);
								}
							}
						});
		return true;
	},

	/*----- Helper methods -------------------------*/

	getUsername : function() {
		return this.username;
	},
	setUsername : function(username) {
		Log.trace('Chat.setUsername: ' + username);
		this.username = username;
	},
	getUnique : function() {
		return this.unique;
	},
	setUnique : function(unique) {
		Log.trace('Chat.setUnique: ' + unique);
		this.unique = unique;
	},
	insertContact : function(elem) {
		Log.trace('Chat.insertContact');
		var jid = $(elem).find('.roster-jid').text();
		var pres = this.presenceValue($(elem).find('.roster-contact'));

		var contacts = $('#roster-area li');
		if (contacts.length > 0) {
			var isInserted = false;
			contacts.each(function() {
				var cmp_pres = gChat.presenceValue($(this).find(
						'.roster-contact'));
				var cmp_jid = $(this).find('.roster-jid').text();

				if (pres > cmp_pres) {
					$(this).before($(elem));
					isInserted = true;
					return false;
				} else if (pres == cmp_pres) {
					if (jid < cmp_jid) {
						$(this).before($(elem));
						isInserted = true;
						return false;
					}
				}
			});
			if (!isInserted) {
				$('#roster-area ul').append($(elem));
			}
		} else {
			$('#roster-area ul').append($(elem));
		}
	},
	insertThumbnail : function(jid) {
		Log.trace('Chat.insertThumbnail');
		var jidId = this.jidToId(jid);
		var username = this.jidToUsername(jid);
		if (username) {
			$
					.ajax({
						url : '/ajax/fetchContactThumbnail/oid/' + username
					})
					.done(
							function(data) {
								if (data != 'failure') {
									$(
											'#roster-area li#'
													+ jidId
													+ ' .roster-contact .roster-thumbnail-wrapper')
											.empty().append(data);
								}
							});
		}
	},
	jidToId : function(jid) {
		var id = Strophe.getBareJidFromJid(jid);
		return id.replace(/@/g, '-').replace(/\./g, '-');
	},
	jidToUsername : function(jid) {
		var value = null;
		if (m = jid.match(/([\w.]+)@/)) {
			value = m[1];
		}
		return value;
	},
	presenceValue : function(elem) {
		if (elem.hasClass('online')) {
			return 2;
		} else if (elem.hasClass('away')) {
			return 1;
		}
		return 0;
	},
	registerView : function(name, callback) {
		this.listeners.add(name, callback);
	},
	unregisterView : function(name) {
		this.listeners.remove(name);
	},
	scrollChat : function(jidId) {
		var div = $('#chat-' + jidId + ' .chat-messages').get(0);
		div.scrollTop = div.scrollHeight;
	}
};

/*----- View -------------------------------------------------------*/

function ChatView(chat) {
	this.chat = chat;
};
ChatView.prototype = {
	Create : function() {
		this.chat.registerView('global', this.onNotification.bind(this));
	},
	Delete : function() {
		this.chat.unregisterView('global');
	},
	onNotification : function(msg) {
		Log.verbose('ChatView.onNotification ' + msg.method);
		switch (msg.method) {
		case 'ConnectionConnected': {
		}
			break;
		case 'ConnectionConnecting': {
		}
			break;
		case 'ConnectionDisconnected': {
		}
			break;
		case 'ConnectionDisconnecting': {
		}
			break;
		case 'ConnectionFailed': {
		}
			break;
		case 'chatIn': {
		}
			break;
		case 'presence': {
		}
			break;
		}
	}
};

$(document).bind('contact_added', function(ev, data) {
	gChat.addContact(data);
});