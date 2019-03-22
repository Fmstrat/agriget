<?php
class Bookmarklets extends Plugin {
  private $host;

  function about() {
    return array(1.0,
		 "Easy feed subscription and web page sharing using bookmarklets",
		 "fox",
	     false,
		 "https://git.tt-rss.org/fox/tt-rss/wiki/ShareAnything");
  }

  function init($host) {
    $this->host = $host;

    $host->add_hook($host::HOOK_PREFS_TAB, $this);
  }

  function hook_prefs_tab($args) {
    if ($args == "prefFeeds") {

		print "<div dojoType=\"dijit.layout.AccordionPane\" 
			title=\"<i class='material-icons'>bookmark</i> ".__('Bookmarklets')."\">";

		print "<h3>" . __("Drag the link below to your browser toolbar, open the feed you're interested in in your browser and click on the link to subscribe to it.") . "</h3>";

		$bm_subscribe_url = str_replace('%s', '', Pref_Feeds::subscribe_to_feed_url());

		$confirm_str = str_replace("'", "\'", __('Subscribe to %s in Agriget?'));

		$bm_url = htmlspecialchars("javascript:{if(confirm('$confirm_str'.replace('%s',window.location.href)))window.location.href='$bm_subscribe_url'+window.location.href}");

		print "<p><label class='dijitButton'>";
		print "<a href=\"$bm_url\">" . __('Subscribe in Agriget'). "</a>";
		print "</label></p>";

		print "<h3>" . __("Use this bookmarklet to publish arbitrary pages using Agriget") . "</h3>";

		print "<label class='dijitButton'>";
		$bm_url = htmlspecialchars("javascript:(function(){var d=document,w=window,e=w.getSelection,k=d.getSelection,x=d.selection,s=(e?e():(k)?k():(x?x.createRange().text:0)),f='".get_self_url_prefix()."/public.php?op=sharepopup',l=d.location,e=encodeURIComponent,g=f+'&title='+((e(s))?e(s):e(document.title))+'&url='+e(l.href);function a(){if(!w.open(g,'t','toolbar=0,resizable=0,scrollbars=1,status=1,width=500,height=250')){l.href=g;}}a();})()");
		print "<a href=\"$bm_url\">" . __('Share with Agriget'). "</a>";
		print "</label>";

		print "<button dojoType='dijit.form.Button' class='alt-info' onclick='window.open(\"https://tt-rss.org/wiki/ShareAnything\")'>
					<i class='material-icons'>help</i> ".__("More info...")."</button>";

		print "</div>"; #pane

	 }
  }

	function api_version() {
		return 2;
	}

}
