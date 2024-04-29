<?php

require_once('../../config.php');
require_once('googleform.php'); // Form sınıfınızın yolunu doğru şekilde ayarlayın
require_once($CFG->dirroot . '/my/lib.php');
require_once("{$CFG->dirroot}/vendor/autoload.php");



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




$form = new googleform();

if ($form->is_cancelled()) {
    // Form iptal edildi
    echo 'iptal eildi';
} else if ($fromform = $form->get_data()) {
    // Form gönderildi ve veriler alındı

    create_meet();

} else {
    // Form gösteriliyor
    $form->display();
}

echo $OUTPUT->footer();

// Trigger dashboard has been viewed event.
$eventparams = array('context' => $context);

create_meet();


function create_meet() {
    global $DB,$USER;




    $client = new Google_Client();
    $client->setAuthConfig('../../googlemeetcred.json');
    $client->addScope(Google_Service_Calendar::CALENDAR);

    if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
        // Erişim tokenı ile istemciyi ayarla
        $client->setAccessToken($_SESSION['access_token']);

        // Bir Google Calendar servis örneği oluştur
        $calendar = new Google_Service_Calendar($client);

        // Etkinlik detayları
        $event = new Google_Service_Calendar_Event([
            'summary' => 'Google Meet Toplantısı',
            'description' => 'Bu bir test toplantısıdır.',
            'start' => [
                'dateTime' => '2024-05-01T09:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ],
            'end' => [
                'dateTime' => '2024-05-01T10:00:00-07:00',
                'timeZone' => 'America/Los_Angeles',
            ],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => 'sample123123123123123123', // Benzersiz bir ID olmalı
                    'conferenceSolutionKey' => [
                        'type' => 'hangoutsMeet'
                    ],
                ],
            ],
            'reminders' => [
                'useDefault' => FALSE,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 10],
                ],
            ],
        ]);

        // Etkinliği eklerken conferenceDataVersion=1 parametresini kullan
        $event = $calendar->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
        echo 'Etkinlik oluşturuldu. Google Meet Linki: ' . $event->hangoutLink;
    } else {
        // Eğer erişim tokenı yoksa, yetkilendirme sayfasına yönlendir
        $redirect_uri = new moodle_url('/oauth2callback');
        redirect($redirect_uri);
    }

}







