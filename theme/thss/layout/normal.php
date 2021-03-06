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
		<span class="right">
<?php echo $OUTPUT->blackbar_info(); ?>
		</span>
	</nav>
	<header>
		<nav>
<?php echo tabmenu_nav(false); ?>
			<div id="menubar">
				<div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
				<div class="navbutton"><?php echo $PAGE->button; ?></div>
			</div>
		</nav>
	</header>
	<section id="page">
<?php
	$class = array();
	if ($PAGE->blocks->region_has_content('side-pre', $OUTPUT)) {
		$class[] = "blocksleft";
?>
		<div id="leftside">
<?php echo $OUTPUT->blocks_for_region('side-pre'); ?>
		</div>
<?php
	}
	if ($PAGE->blocks->region_has_content('side-post', $OUTPUT)) {
		$class[] = "blocksright"
?>
		<div id="rightside">
<?php
	echo $OUTPUT->blocks_for_region('side-post');
?>
		</div>
<?php } ?>
		<div id="content" <?php if($class) { echo 'class="'.implode(' ',$class).'"'; } ?>>
<?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
		</div>
	</section>
	
	<footer>
		<p>
			Proudly powered by the <a href="http://www.moodle.org/">Moodle Project</a>. Special thanks to our contributors.
			<br />
<?php echo $OUTPUT->standard_footer_html(); ?>
		</p>
		<span class="right">
			<?php echo $OUTPUT->login_info(); ?>
		</span>
	</footer>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
