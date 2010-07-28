<?php echo $OUTPUT->doctype() ?>
<html lang="en-ca">
<head>
<?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php echo $PAGE->pagetype ?>" class="<?php echo $PAGE->bodyclasses ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
	<nav class="blackbar">
		<span class="left">
			<b>
				<?php echo $SITE->fullname."\n"; ?>
			</b>
		</span>
	</nav>
	<header>
		<nav>
			<div id="menubar">
				<div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
				<div class="navbutton"><?php echo $PAGE->button; ?></div>
			</div>
		</nav>
	</header>
	<section id="page">
		<div id="content">
<?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
		</div>
	</section>
	
	<footer>
		<p>
            Proudly powered by the <a href="http://www.moodle.org/">Moodle Project</a>.
            <br />
<?php echo $OUTPUT->standard_footer_html(); ?>
		</p>
	</footer>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
