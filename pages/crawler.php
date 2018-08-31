<?php

$content = '';

$config = rex_request('crawler', array(
    array('submit', 'boolean'),
));

if ($config['submit']) {
    echo '<section class="rex-page-section">
            <div class="panel panel-default">
                <header class="panel-heading"><div class="panel-title">Ergebnis</div></header>
                    <div class="panel-body">';
                    $linkchecker = new linkchecker();
                    $linkchecker->run();
    echo '          </div>
                </div>
    </section>';


} else {
    $content .= '
        <form action="' . rex_url::currentBackendPage() . '" method="post">
            <fieldset>
            <legend>Linkchecker - Crawler starten</legend>
    ';
    $fragment = new rex_fragment();
    $formElements = array();
    $elements = array();
    $elements['field'] = '
      <input type="submit" class="btn btn-save rex-form-aligned" name="crawler[submit]" value="Crawler starten" ' . rex_i18n::msg('linkchecker_crawler_start') . ' />
    ';
    $formElements[] = $elements;
    $fragment = new rex_fragment();
    $fragment->setVar('elements', $formElements, false);
    $content .= $fragment->parse('core/form/submit.php');
    $content .= '
        </fieldset>
      </form>
    ';
}

$fragment = new rex_fragment();
if ($config['submit']) {
    $fragment->setVar('title', $this->i18n('linkchecker_crawler'));
}
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
