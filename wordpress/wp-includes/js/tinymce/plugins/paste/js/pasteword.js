tinyMCEPopup.requireLangPack();
var PasteWordDialog = {
	init : function() {
		var ed = tinyMCEPopup.editor, el = document.getElementById('iframecontainer'), ifr, doc, css, cssHTML = '';

		// Create iframe
		el.innerHTML = '<iframe id="iframe" src="javascript:\'\';" frameBorder="0" style="border: 1px solid gray"></iframe>';
		ifr = document.getElementById('iframe');
		doc = ifr.contentWindow.document;

		// Force absolute CSS urls
		css = [ed.baseURI.toAbsolute("themes/" + ed.settings.theme + "/skins/" + ed.settings.skin + "/content.css")];
		css = css.concat(tinymce.explode(ed.settings.content_css) || []);
		tinymce.each(css, function(u) {
			cssHTML += '<link href="' + ed.documentBaseURI.toAbsolute('' + u) + '" rel="stylesheet" type="text/css" />';
		});

		// Write content into iframe
		doc.open();
		doc.write('<html><head>' + cssHTML + '</head><body class="mceContentBody" spellcheck="false"></body></html>');
		doc.close();

		doc.designMode = 'on';
		this.resize();

		window.setTimeout(function() {
			ifr.contentWindow.focus();
		}, 10);
	},

	insert : function() {
		var h = document.getElementById('iframe').contentWindow.document.body.innerHTML;
		tinyMCEPopup.editor.execCommand('mceInsertClipboardContent', false, {content : h, wordContent : true});
		tinyMCEPopup.close();
	},

	resize : function() {
		var vp = tinyMCEPopup.dom.getViewPort(window), el;

		el = document.getElementById('iframe');

		if (el) {
			el.style.width  = (vp.w - 20) + 'px';
			el.style.height = (vp.h - 90) + 'px';
		}
	}
};

tinyMCEPopup.onInit.add(PasteWordDialog.init, PasteWordDialog);

// HACK to support MS-Word footnotes in "paste from word" popup
// TODO: move this to a seperate plugin
tinyMCEPopup.oldClose = tinyMCEPopup.close;
tinyMCEPopup.close = function(){
    if (typeof tinyMCEPopup.onclose == "function") {
        tinyMCEPopup.onclose();
    }
    tinyMCEPopup.oldClose();
};
tinyMCEPopup.onclose = function(){
    var editor = tinymce.get('content');
    var content = editor.getContent();
    var i = content.indexOf("<hr");
    var preHr = content.substring(0, i);
    var postHr = content.substring(i);
    postHr = postHr.replace(/href=(["'])#_ftnref/g, "id=$1_ftn");
    content = preHr + postHr;
    editor.setContent(content);
};
