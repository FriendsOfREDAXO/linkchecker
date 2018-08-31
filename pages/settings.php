<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$config = rex_request('config', array(
    array('baselink', 'string'),
    array('depth', 'string'),
    array('maxlinks', 'string'),
    array('no200', 'int'),
    array('submit', 'boolean'),
));

$form = '';

if ($config['submit']) {
    $this->setConfig('baselink',$config['baselink']);
    $this->setConfig('depth',$config['depth']);
    $this->setConfig('maxlinks',$config['maxlinks']);
    $this->setConfig('no200',$config['no200']);
    $form .= rex_view::info('Werte gespeichert');
}

// open form
$form .= '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <fieldset>
        <legend>Linkchecker - Einstellungen</legend>
';

$fragment = new rex_fragment();

// Ausgangslink
$formElements = [];
$n = [];
$n['label'] = '<label for="linkchecker-baselink">Ausgangslink</label>';
$n['field'] = '<input class="form-control" id="linkchecker-baselink" type="text" name="config[baselink]" value="' . $this->getConfig('baselink') . '" />';
$n['note'] = 'Bitte unbedingt den richtigen und vollständigen Link eingeben (http(s)://(www.).';
$formElements[] = $n;
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/container.php');


// Tiefe
$formElements = [];
$n = [];
$n['label'] = '<label for="linkchecker-depth">Tiefe</label>';
$n['field'] = '<input class="form-control" id="linkchecker-depth" type="text" name="config[depth]" value="' . $this->getConfig('depth') . '" />';
$n['note'] = 'Tiefe (Anzahl der Ebenen), die bei der Linksuche berücksichtigt werden sollen.';
$formElements[] = $n;
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/container.php');

// Maximale Anzahl an Links
$formElements = [];
$n = [];
$n['label'] = '<label for="linkchecker-maxlinks">Maximale Anzahl an Links</label>';
$n['field'] = '<input class="form-control" id="linkchecker-maxlinks" type="text" name="config[maxlinks]" value="' . $this->getConfig('maxlinks') . '" />';
$n['note'] = 'Anzahl der Links, die maximal ausgewertet werden sollen.';
$formElements[] = $n;
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/container.php');


// Maximale Anzahl an Links
$formElements = [];
$n = [];
$n['label'] = '<label for="linkchecker-no200">Keine 200er ausgeben</label>';
$n['field'] = '<input type="checkbox" id="linkchecker-no200" name="config[no200]" value="1" ' . ($this->getConfig('no200') ? ' checked="checked"' : '') . ' />';
$n['note'] = 'Funktioniert noch nicht!';
$formElements[] = $n;
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/container.php');


$form .= '</fieldset>';

$form .= '<fieldset>'
        . '<legend></legend>';



// create submit button
$formElements = array();
$elements = array();
$elements['field'] = '
  <input type="submit" class="btn btn-save rex-form-aligned" name="config[submit]" value="Einstellungen übernehmen" ' . rex::getAccesskey(rex_i18n::msg('linkchecker_config_save'), 'save') . ' />
';
$formElements[] = $elements;

// parse submit element
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/submit.php');

// close form
$form .= '
    </fieldset>
  </form>
';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', 'Einstellung');
$fragment->setVar('body', $form, false);
echo $fragment->parse('core/page/section.php');
