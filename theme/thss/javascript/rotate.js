function draw_twitter() {
	var canvas = document.getElementById('rotate');
	canvas.style.cursor = 'pointer';
	var ctx = canvas.getContext('2d');
	ctx.textAlign = 'center';
	ctx.fillText("Follow us on Twitter!", 150, 20);
}

draw_twitter();
