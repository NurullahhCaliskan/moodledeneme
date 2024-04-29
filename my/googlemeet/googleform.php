<?php

require_once("$CFG->libdir/formslib.php");

class googleform extends moodleform {
    // Form tanımı burada yapılır
    function definition() {
        $mform = $this->_form; // Don't forget the underscore!

        // "ABONE OL" başlığını formun en üstüne ekleyin
        $mform->addElement('html', '<h3 class="form-section-title">Google Meet Oluştur</h3>');
        $mform->addElement('html', '<div class="g-signin2" data-onsuccess="onSignIn"></div>');
        // E-posta adresi için bir alan ekleyin


        $this->add_action_buttons(false, get_string('submit'));




    }
}
