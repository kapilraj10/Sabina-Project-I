/* Custom site JavaScript (assets/js/script.js)
	 Small behaviors: active nav link handling and search routing.
*/

document.addEventListener('DOMContentLoaded', function () {
	// Highlight nav-link that matches current URL (simple heuristic)
	try {
		var navLinks = document.querySelectorAll('.navbar .nav-link');
		var path = window.location.pathname.split('/').pop(); // filename or empty
		navLinks.forEach(function (link) {
			var href = (link.getAttribute('href') || '').split('/').pop();
			if (href && href === path) {
				link.classList.add('active');
			}
			link.addEventListener('click', function () {
				navLinks.forEach(function (l) { l.classList.remove('active'); });
				this.classList.add('active');
			});
		});
	} catch (e) {
		console && console.warn && console.warn('Navbar script init error', e);
	}

	// Enhance the navbar search form: redirect to shop.php?search=... if present
	var searchForm = document.querySelector('.navbar form[role="search"]');
	if (searchForm) {
		searchForm.addEventListener('submit', function (ev) {
			ev.preventDefault();
			var q = this.querySelector('input[type="search"]');
			if (q && q.value.trim()) {
				var url = 'shop.php?search=' + encodeURIComponent(q.value.trim());
				window.location.href = url;
			}
		});
	}
});

/* End of script */
