function switchMonospaceEditor(r) {
	e = document.getElementsByName("req_message")[0];
	if (!e) return null;
	if (e.style) {
		ff = e.style['font-family'];
		fs = e.style['font-size'];
		if ((!ff) || ff != 'monospace') {
			e.style['font-family'] = 'monospace';
			e.style['font-size'  ] = 'medium';
		} else {
			e.style['font-family'] = '';
			e.style['font-size'  ] = '';
		}
	}
	return null;
};

