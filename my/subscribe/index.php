<?php

require_once('../../config.php');
require_once('subscribeform.php'); // Form sınıfınızın yolunu doğru şekilde ayarlayın
require_once($CFG->dirroot . '/my/lib.php');


redirect_if_major_upgrade_required();

// TODO Add sesskey check to edit
$edit   = optional_param('edit', null, PARAM_BOOL);    // Turn editing on and off
$reset  = optional_param('reset', null, PARAM_BOOL);

require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$strmymoodle = get_string('myhome');





// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    throw new \moodle_exception('mymoodlesetup');
}

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/subscribe/index.php', $params);

$PAGE->add_body_class('limitedwidth');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);



echo $OUTPUT->header();

if (core_userfeedback::should_display_reminder()) {
    core_userfeedback::print_reminder_block();
}

echo $OUTPUT->addblockbutton('content');




$form = new subscribeform();

if ($form->is_cancelled()) {
    // Form iptal edildi
    echo 'iptal eildi';
} else if ($fromform = $form->get_data()) {
    // Form gönderildi ve veriler alındı
    echo 'Başarılı şeklde abone olundu: '. $fromform->email;

    save_subscription($fromform->email);

    redirect($redirect, "Başarılı Şekilde Abone Olundu",null, \core\output\notification::NOTIFY_SUCCESS);

} else {
    // Form gösteriliyor
    $form->display();
}

echo $OUTPUT->footer();

// Trigger dashboard has been viewed event.
$eventparams = array('context' => $context);

$event->trigger();



function save_subscription($email) {
    global $DB,$USER;

    $record = new stdClass();
    $record->email = $email;
    $record->userid = $USER->id;
    $record->timecreated = time();

    $DB->insert_record('subscriber', $record);
}







