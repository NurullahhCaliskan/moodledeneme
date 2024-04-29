<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/course/lib.php');


redirect_if_major_upgrade_required();

require_login();

if (!is_siteadmin()) { // Eğer kullanıcı site yöneticisi değilse
    throw new moodle_exception('nopermissions', 'error'); // Erişim reddedilir
}

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$context = context_system::instance();

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    throw new Exception('mymoodlesetup');
}

// Start setting up the page.
$PAGE->set_context($context);
$PAGE->set_url('/example/index.php');
$PAGE->add_body_classes(['limitedwidth', 'page-mycourses']);
$PAGE->set_pagelayout('mycourses');

$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title(get_string('mycourses'));
$PAGE->set_heading(get_string('mycourses'));

// No blocks can be edited on this page (including by managers/admins) because:
// - Course overview is a fixed item on the page and cannot be moved/removed.
// - We do not want new blocks on the page.
// - Only global blocks (if any) should be visible on the site panel, and cannot be moved int othe centre pane.
$PAGE->force_lock_all_blocks();

// Force the add block out of the default area.
$PAGE->theme->addblockposition  = BLOCK_ADDBLOCK_POSITION_CUSTOM;


echo $OUTPUT->header();

echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();

// Trigger dashboard has been viewed event.
$eventparams = array('context' => $context);
$event = \core\event\mycourses_viewed::create($eventparams);
$event->trigger();




