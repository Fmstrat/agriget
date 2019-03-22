var changeStyle = function(selector, prop, value) {
	var styles = document.styleSheets
	var b = false;
	for (var x = 0; x < styles.length; x++) {
		var style = styles[x].cssRules || styles[x].rules;
		for (var i = 0; i < style.length; i++) {
			if (style[i].selectorText == selector) {
				style[i].style[prop] = value;
				b = true;
				break;
			}
		}
		if (b) break;
	}
}

var toggleToolbar = function() {
	var styles = document.styleSheets
	var b = false;
	for (var x = 0; x < styles.length; x++) {
		var style = styles[x].cssRules || styles[x].rules;
		for (var i = 0; i < style.length; i++) {
			if (style[i].selectorText == '.toolbar-hide') {
				if (style[i].style['visibility'] == 'hidden') {
					style[i].style['visibility'] = 'visible';
					document.getElementById('display-buttons').style.display = 'none';
				} else {
					style[i].style['visibility'] = 'hidden';
					document.getElementById('display-buttons').style.display = 'inline';
				}
				b = true;
				break;
			}
		}
		if (b) break;
	}
}


window.addEventListener("resize", checkCardCount);

function checkCardCount() {
	var hf = $("headlines-frame");
	if (hf) {
		if (App.getViewMode() == "cards") {
			setTimeout(function() {
				w = hf.clientWidth;
				if (w > 1100) {
					$("headlines-frame").removeClassName("grid-cards-one");
					$("headlines-frame").removeClassName("grid-cards-two");
					$("headlines-frame").addClassName("grid-cards-three");
				} else if (w < 780) {
					$("headlines-frame").addClassName("grid-cards-one");
					$("headlines-frame").removeClassName("grid-cards-two");
					$("headlines-frame").removeClassName("grid-cards-three");
				} else {
					$("headlines-frame").removeClassName("grid-cards-one");
					$("headlines-frame").addClassName("grid-cards-two");
					$("headlines-frame").removeClassName("grid-cards-three");
				}
			}, 1);
		}
	}
}
