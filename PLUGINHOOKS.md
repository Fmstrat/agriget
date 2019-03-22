
Hook Documentation
============
In progress documentation on hooks for plugins.

This documentation may not be up to date. Please see https://github.com/Fmstrat/agriget/blob/master/classes/pluginhost.php#L21 for the latest list of hooks.

Feed Fetch Hooks
---------------
These hooks run in the background when feed data is fetched.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_FEED_BASIC_INFO` | Used to replace basic feed information prior to fetching from the provider. | `$basic_info` - Blank array that is also returned<br>`$fetch_url` - URL of feed<br>`$owner_uid` - The user who owns the feed<br>`$feed` - Feed object<br>`$auth_login` - Auth user if requred<br>`$auth_pass` - Auth password if requred | `$basic_info` |
| `HOOK_FETCH_FEED` | Used to update feed data right before it is fetched from the provider URL. | `$feed_data` - Blank feed data that is also returned<br> `$fetch_url` - URL of feed<br> `$owner_uid` - The user who owns the feed<br> `$feed` - Feed object<br> `0` - Unknown<br> `$auth_login` - Auth user if requred<br> `$auth_pass` - Auth password if requred | `$feed_data` |
| `HOOK_FEED_FETCHED` | Used to update feed data right after it is fetched from the provider URL. | `$feed_data` - Populated feed data that is also returned<br> `$fetch_url` - URL of feed<br> `$owner_uid` - The user who owns the feed<br> `$feed` - Feed object | `$feed_data` |
| `HOOK_FEED_PARSED` | Unknown. Runs after the Feed Parser inits. | `$rss` - Feed parser object | None |
| `HOOK_ARTICLE_FILTER` | Used to update inidividual articles after the feed is fetched and parsed. | `$article` - Object containing an article | `$article` |
| `HOOK_FILTER_TRIGGERED` | Used to change the filters that match an article. | `$feed` - Feed object<br> `$owner_uid` - The user who owns the feed<br> `$article` - Object containing an article<br> `$matched_filters` - Array of matched filters<br> `$matched_rules` - Array of matched rules<br> `$article_filters` - All filters | None |
| `HOOK_ARTICLE_FILTER_ACTION` | Used to update an article as filter actions are applied. | `$article` - Object containing an article<br> `$pfaction` - The filter action<br> `$matched_rules` - Array of matched rules<br> `$article_filters` - All filters | `$article` |
| `HOOK_HOUSE_KEEPING` | Used to run code at the end of processing. | No inputs | None |


Page Load Hooks
---------------
These hooks run when the page loads.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_MAIN_TOOLBAR_BUTTON` | Used to add HTML/buttons to the main toolbar area at the top left. | No inputs | HTML string |
| `HOOK_TOOLBAR_BUTTON` | Used to add HTML/dropdowns to the main toolbar area at the top right. | No inputs | HTML string |
| `HOOK_ACTION_ITEM` | Used to add options to the upper right action drop-down menu. | No inputs | HTML string |


Grabbing Hooks
---------------
These hooks run when an article is grabbed, reguardless of client (mobile/HTML/etc).

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_RENDER_ARTICLE_API` | Used to alter the article object (and headline object) as it is returned to the client. Format of object can be found here: https://gitlab.com/gothfox/tt-rss/blob/master/classes/api.php#L349 | `array("article" => $article)` - The article object as a single element array | `$article` |
| `HOOK_QUERY_HEADLINES` | Used to alter the headlines as they are queried. | `$line` - The headline object<br> `$excerpt_length` - The length of content to display<br> `true` - Unknown | `$line` |


Feed Display Hooks
---------------
These hooks run when the page loads a feed.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_HEADLINE_TOOLBAR_BUTTON` | Used to add options to the upper-right menu titled Select. | `$feed_id` - The feed id of the current feed displayed<br> `$is_cat` - The category of that feed | HTML code |
| `HOOK_HEADLINES_BEFORE` | Used to add HTML code before the headlines are displayed. | `$feed` - The feed object<br> `$cat_view` - The category view<br> `$qfh_ret` - Unknown | HTML code |
| `HOOK_QUERY_HEADLINES` | Used to alter listing previews as they are added to the display. | `$line` - The object containing that headline's content preview.<br> `250` - Unknown (Character length?)<br> `false` - Unknown | `$line` |
| `HOOK_ARTICLE_EXPORT_FEED` | Runs after HOOK_QUERY_HEADLINES but only for public feeds. | `$line` - Array of lines of HTML code)<br> `$feed` - Object with feed info and the array of articles (populates each article after this runs)<br> `$is_cat` - Category | `$line` |


Article Display Hooks
---------------
These hooks run when the page loads an article.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_ARTICLE_LEFT_BUTTON` | Used to add buttons to the left of each individual article as it is displayed. | `$line` - Object of article data. | `$line["buttons_left"]` |
| `HOOK_ARTICLE_BUTTON` | Used to add buttons to the top of each individual article as it is displayed. | `$line` - Object of article data. | `$line["buttons"]` |
| `HOOK_RENDER_ARTICLE_CDM` | Used to alter article content or anything else in the displayed article. | `$line` - Object of article data. | `$line` |
| `HOOK_RENDER_ARTICLE` | Used to alter article content or anything else in the displayed article before everything has been placed into html. Note: This only runs on public feeds. | `$line` - Object of article data. | `$line` |
| `HOOK_FORMAT_ARTICLE` | Used to alter article content or anything else in the displayed article after everything has been placed into html (including buttons). Note: This only runs on public feeds. | `$rv` - The HTML to be returned for each article<br> `$line` - Object of article data.<br> `true` - Unknown | `$rv` |


Embedded Media Hooks
---------------
Used to alter embedded media containers.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_FORMAT_ENCLOSURES` | Used to add content and replace default enclosures for media (url, content types, width, title, etc). | `$rv` - The HTML code for the enclosure<br> `$result` - Lines of entries<br> `$id` - Unknown<br> `$always_display_enclosures` - Unknown<br> `$article_content` - Content of the article<br> `$hide_images` - Whether images are displayed | `$retval ([0] = $rv, [1] = $result)` |
| `HOOK_ENCLOSURE_ENTRY` | Used to alter each media item. | `$line` - The information on that item (url, width, title, etc) | `$line` |
| `HOOK_RENDER_ENCLOSURE` | Used to alter HTML as the media item is displayed. | `$entry` - The media item<br> `$hide_images` - If hide images is on or off | HTML code placed in the IMG tag |


Interaction Hooks
---------------
Runs when an action is executed int the client.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_SUBSCRIBE_FEED` | Used to alto feed content as a feed is subscribed to, right after the data is pulled. | `$contents` - Contents of the feed after first pull<br> `$url` - URL of the feed<br> `$auth_login` - Auth user if requred<br> `$auth_pass` - Auth password if requred | `$content` |
| `HOOK_UNSUBSCRIBE_FEED` | Used to run code when a feed is unsubscribed. | `$id` - The feed id<br> `$owner_uid` - The owner of the feed | Boolean for success |
| `HOOK_SEARCH` | Used to alter search results. | `$search` - The search object | The search list |
| `HOOK_AUTH_USER` | Used to add authentication methods. | `$login` - Username<br> `$password` - Password | `$user_id` |


Settings Hooks
---------------
These hooks alter settings and preferences.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_HOTKEY_INFO` | Used to alter the information returned about hotkeys. | `$hotkeys` - Object of hotkey info | `$hotkeys` |
| `HOOK_HOTKEY_MAP` | Used to add/alter hotkeys. | `$hotkeys` - Array of hotkeys | `$hotkeys` |
| `HOOK_PREFS_EDIT_FEED` | Used to alter a feed when the settings screen for a feed is displayed. | `$feed_id` - The feed's id | None |
| `HOOK_PREFS_SAVE_FEED` | Used to execute commands when a feed is saved (before commit). | `$feed_id` - The feed's id | None |
| `HOOK_PREFS_TAB_SECTION` | Used to run code when preferences tabs are loaded. | No inputs | None |
| `HOOK_PREFS_TAB` | Used to run code inside a preference tab. | No inputs | None |
| `HOOK_PREFS_TABS` | Used to add HTML tabs to the top of preferences. | No inputs | None |


Misc Hooks
---------------
Other hooks.

| Hook | Description | Inputs | Return |
| ---- | ----------- | ------ | ------ |
| `HOOK_SANITIZE` | Runs whenever content is sanitized. | `$doc` - Unknown<br> `$site_url` - Unknown <br> `$allowed_elements` - HTML ellements allowed in fields<br> `$disallowed_attributes` - HTML elements not allowed in fields<br> `$article_id` - The current article id | `$retval ([0] = $doc, [1] = $allowed_elements, [2] = $disallowed_attributes)` |
| `HOOK_SEND_LOCAL_FILE` | Runs when a local file is sent to a client (such as proxied media). | `$filename` - The file being sent | Boolean for success (not used) |
| `HOOK_SEND_MAIL` | Used to execute an action when email is sent. | `$this` - The current object<br> `$params` - See https://gitlab.com/gothfox/tt-rss/blob/master/classes/mailer.php#L9 | `$rc` (See https://gitlab.com/gothfox/tt-rss/blob/master/classes/mailer.php#L24) |
| `HOOK_UPDATE_TASK` | Unknown | No inputs | None |
