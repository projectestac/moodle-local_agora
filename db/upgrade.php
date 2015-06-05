<?php

// This file keeps track of upgrades to
// the choice module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_local_agora_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014022400) {
    	$DB->set_field('course', 'format', 'simple', array('format' => 'senzill'));
        upgrade_plugin_savepoint(true, 2014022400, 'local', 'agora');
    }

    if ($oldversion < 2014121100) {
        $DB->delete_records('events_handlers', array('eventname' => 'user_logout', 'component' => 'mod_chat'));
        upgrade_plugin_savepoint(true, 2014121100, 'local', 'agora');
    }

    if ($oldversion < 2015011400) {
        $rcommonlogdir = get_admin_datadir_folder();
        set_config('data_store_log', $rcommonlogdir, 'rcommon');

        upgrade_plugin_savepoint(true, 2015011400, 'local', 'agora');
    }

    if ($oldversion < 2015050400) {
        // Preconfigure airnotifier
        $config = get_config('message');
        $providers = $DB->get_records('message_providers');
        foreach ($providers as $provider) {
            $componentproviderbase = $provider->component.'_'.$provider->name;
            foreach (array('loggedin', 'loggedoff') as $state) {
                $linepref = '';
                $componentproviderstate = $componentproviderbase.'_'.$state;
                $name = 'message_provider_'.$provider->component.'_'.$provider->name.'_'.$state;
                if (isset($config->$name) && !empty($config->$name)) {
                    $value = explode(',', $config->$name);
                    $value[] = 'airnotifier';
                    $value = implode(',', $value);
                    set_config($name, $value, 'message');
                }
            }
        }

        upgrade_plugin_savepoint(true, 2015050400, 'local', 'agora');
    }

    if ($oldversion < 2015051900) {
        $DB->set_field('block', 'visible', 0, array('name' => 'participants'));
        $DB->set_field('block', 'visible', 0, array('name' => 'myprofile'));
        $DB->set_field('block', 'visible', 0, array('name' => 'mnet_hosts'));

        upgrade_plugin_savepoint(true, 2015051900, 'local', 'agora');
    }

    if ($oldversion < 2015060500) {
        set_config('customusermenuitems', "messages,message|/message/index.php|message
myfiles,moodle|/user/files.php|download
mybadges,badges|/badges/mybadges.php|award");
        set_config('alternativefullnameformat', 'language');

        set_config('texteditors', 'atto,textarea');
        set_config('autosavefrequency', 60, 'editor_atto');

        set_config('httpurl', 'http://cdn.mathjax.org/mathjax/2.3-latest/MathJax.js', 'filter_mathjaxloader');
        set_config('httpsurl', 'https://cdn.mathjax.org/mathjax/2.3-latest/MathJax.js', 'filter_mathjaxloader');
        set_config('mathjaxconfig', 'MathJax.Hub.Config({
    config: ["Accessible.js", "Safe.js"],
    errorSettings: { message: ["!"] },
    skipStartupTypeset: true,
    messageStyle: "none"
});
', '    ');

        set_config('librarygroup1', '\cdot
\times
\ast
\div
\diamond
\pm
\mp
\oplus
\ominus
\otimes
\oslash
\odot
\circ
\bullet
\asymp
\equiv
\subseteq
\supseteq
\leq
\geq
\preceq
\succeq
\sim
\simeq
\approx
\subset
\supset
\ll
\gg
\prec
\succ
\infty
\in
\ni
\forall
\exists
\neq
', 'atto_equation');

        set_config('librarygroup2', '\leftarrow
\rightarrow
\uparrow
\downarrow
\leftrightarrow
\nearrow
\searrow
\swarrow
\nwarrow
\Leftarrow
\Rightarrow
\Uparrow
\Downarrow
\Leftrightarrow
', 'atto_equation');

        set_config('librarygroup3', '\alpha
\beta
\gamma
\delta
\epsilon
\zeta
\eta
\theta
\iota
\kappa
\lambda
\mu
\nu
\xi
\pi
\rho
\sigma
\tau
\upsilon
\phi
\chi
\psi
\omega
\Gamma
\Delta
\Theta
\Lambda
\Xi
\Pi
\Sigma
\Upsilon
\Phi
\Psi
\Omega
', 'atto_equation');

        set_config('librarygroup4', '\sum{a,b}
\int_{a}^{b}{c}
\iint_{a}^{b}{c}
\iiint_{a}^{b}{c}
\oint{a}
(a)
[a]
\lbrace{a}\rbrace
\left| \begin{matrix} a_1 & a_2 \ a_3 & a_4 \end{matrix} \right|
', 'atto_equation');

        set_config('pathdvisvgm', '/usr/bin/dvisvgm', 'filter_tex');
        set_config('pathmimetex', '', 'filter_tex');

        upgrade_plugin_savepoint(true, 2015060500, 'local', 'agora');
    }



    return true;
}


