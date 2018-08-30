<?php

$file = rex_file::get(rex_path::addon('linkchecker','README.md'));
$Parsedown = new Parsedown();

$content =  '<div id="linkchecker">'.$Parsedown->text($file).'</div>';

$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('linkchecker_info'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');


