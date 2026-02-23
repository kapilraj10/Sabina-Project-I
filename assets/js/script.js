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

	// Logout fetch handler: intercept logout button and perform POST to logout_ajax
	var logoutBtn = document.getElementById('logout-btn');
	if(logoutBtn){
		logoutBtn.addEventListener('click', function(ev){
			ev.preventDefault();
			var token = this.dataset.csrf || '';
			fetch('/Sabina/auth/logout_ajax.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ csrf_token: token })
			}).then(function(resp){ return resp.json(); })
			.then(function(json){
				if(json && json.success){
					window.location.href = json.redirect || '/Sabina/';
				} else {
					alert('Logout failed');
				}
			}).catch(function(){ alert('Logout failed'); });
		});
	}
});

/* End of script */
