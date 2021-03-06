<?php

////////////////////////////////////////////////////////////////////////////
/// This file contains a few configuration variables that control
/// how Moodle uses this theme.
////////////////////////////////////////////////////////////////////////////

$THEME->name = 'thss';

$THEME->parents = array('base');

// TODO: All old styles are now moved into this standard theme because
//       we need to go through all these and fix them.
//       This means we will gradually put these back into plugins
//       directories
$THEME->sheets = array(
    'site',
    'misc',
    'header-footer',
    'form',
    'blocks',
    'course',
    'fixesIE',
    'projector'
    
);

$THEME->editor_sheets = array();

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->csspostprocess = 'thss_process_css';

$THEME->layouts = array(
    // Most backwards compatible layout without the blocks - this is the layout used by default
    'base' => array(
        'theme' => 'base',
        'file' => 'normal.php',
        'regions' => array(),
    ),
    // Standard layout with blocks, this is recommended for most pages with general information
    'standard' => array(
        'theme' => 'thss',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post',
    ),
    // Main course page
    'course' => array(
        'theme' => 'thss',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post',
        'options' => array('langmenu'=>true),
    ),
    'coursecategory' => array(
        'theme' => 'thss',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post',
    ),
    // Standard module pages - default page layout if $cm specified in require_login()
    'module' => array(
        'theme' => 'thss',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post',
    ),
    // The site home page.
    'frontpage' => array(
        'theme' => 'thss',
        'file' => 'home.php',
        'regions' => array('side-pre', 'centre-top', 'side-post'),
        'defaultregion' => 'side-post',
    ),
    // Server administration scripts.
    'admin' => array(
        'theme' => 'thss',
        'file' => 'normal.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // My dashboard page
    'mydashboard' => array(
        'theme' => 'thss',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post',
        'options' => array('langmenu'=>true),
    ),
    // My public page
    'mypublic' => array(
        'theme' => 'thss',
        'file' => 'normal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'login' => array(
        'theme' => 'thss',
        'file' => 'home.php',
        'regions' => array(),
        'options' => array('langmenu'=>true),
    ),
    
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'theme' => 'base',
        'file' => 'minimal.php',
        'regions' => array(),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'theme' => 'base',
        'file' => 'frametop.php',
        'regions' => array(),
    ),
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible
    'embedded' => array(
        'theme' => 'base',
        'file' => 'embedded.php',
        'regions' => array(),
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, and it is good idea if it does not have links to
    // other places - for example there should not be a home link in the footer...
    'maintenance' => array(
        'theme' => 'base',
        'file' => 'minimal.php',
        'regions' => array(),
    ),
);

/** List of javascript files that need to included on each page */
//$THEME->javascripts_footer = array('blocks');
