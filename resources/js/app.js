document.addEventListener('DOMContentLoaded', function () {
	var profileToggle = document.getElementById('profileToggle');
	var profileDropdown = document.getElementById('profileDropdown');

	if (profileToggle && profileDropdown) {
		profileToggle.addEventListener('click', function (event) {
			event.preventDefault();
			profileDropdown.classList.toggle('hidden');
		});

		document.addEventListener('click', function (event) {
			if (!profileToggle.contains(event.target) && !profileDropdown.contains(event.target)) {
				profileDropdown.classList.add('hidden');
			}
		});
	}

	var themeToggle = document.getElementById('themeToggle');
	if (!themeToggle) {
		return;
	}

	var root = document.documentElement;
	var storedTheme = localStorage.getItem('fs-theme');
	var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
	var initialTheme = storedTheme || (prefersDark ? 'dark' : 'light');

	function updateThemeButton(isDark) {
		var lightLabel = themeToggle.getAttribute('data-label-light') || 'Dark mode';
		var darkLabel = themeToggle.getAttribute('data-label-dark') || 'Light mode';
		themeToggle.textContent = isDark ? darkLabel : lightLabel;
		themeToggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
		themeToggle.title = isDark ? 'Alternar para light mode' : 'Alternar para dark mode';
	}

	function applyTheme(theme) {
		var isDark = theme === 'dark';
		root.classList.toggle('dark', isDark);
		localStorage.setItem('fs-theme', isDark ? 'dark' : 'light');
		updateThemeButton(isDark);
	}

	applyTheme(initialTheme);

	themeToggle.addEventListener('click', function () {
		applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
	});
});
