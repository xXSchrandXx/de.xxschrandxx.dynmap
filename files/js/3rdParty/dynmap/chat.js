
componentconstructors['chat'] = function(dynmap, configuration) {
	var me = this;
	
	if(dynmap.getBoolParameterByName("hidechat"))
		return;
		
	// Provides 'chat'-events by monitoring the world-updates.
	$(dynmap).bind('worldupdate', function(event, update) {
		swtch(update.type, {
			chat: function() {
				$(dynmap).trigger('chat', [{source: update.source, name: update.playerName, text: update.message, account: update.account,
                channel: update.channel}]);
			}
		});
	});
	var pname = null;
	if(configuration.allowurlname) {
		pname = dynmap.getParameterByName("chatname");
		if(pname == "") pname = null;
	}
	
	if (dynmap.options.allowwebchat) {
        const cookies = document.cookie.split(";").map((c) => c.trim());
        const xsrfToken = cookies.find((c) => c.startsWith("XSRF-TOKEN="));
        if (xsrfToken === undefined) {
            return "COOKIE_NOT_FOUND";
        }
        const [_key, value] = xsrfToken.split(/=/, 2);
		// Accepts 'sendchat'-events to send chat messages to the server.
		$(dynmap).bind('sendchat', function(event, message) {
			var data = '{"name":'+JSON.stringify(pname?pname:"")+',"message":'+JSON.stringify(message)+'}';
			$.ajax({
				type: 'POST',
		        contentType: "application/json; charset=utf-8",
				headers: {
                    "X-XSRF-TOKEN": decodeURIComponent(value.trim())
                },
				url: dynmap.options.url.sendmessage,
				data: data,
				dataType: 'json',
				success: function(response) {
					if(response.error != "none") {
						var msg = dynmap.options['msg-chatnotallowed'];
						$(dynmap).trigger('chat', [{source: 'me', name: 'Error', text: msg }]);
					}					
				},
				error: function(xhr) {
					if (xhr.status === 403) {
						$(dynmap).trigger('chat', [{source: 'me', name: 'Error', text: dynmap.options.spammessage.replace('%interval%', dynmap.options['webchat-interval'])}]);
					}
				}
			});
		});
	}
};
