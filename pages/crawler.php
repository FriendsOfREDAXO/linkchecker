<style>
    .lc_log { display: flex;  flex-wrap: nowrap; width: 100%; }
    .lc_log + .lc_log { border-top: 1px solid #bbbbbb; }
    .lc_log div { padding: 4px }
    .lc_log .code200 { background: green; color: #fff; }
    .lc_log .code301 { background: red; color: #fff; }
    .lc_log .code302 { background: red; color: #fff; }
    .lc_log .code404 { background: red; color: #fff; }
    .lc_log .code400 { background: red; color: #fff; }
    .lc_log .codeerr { background: red; color: #fff; }
    .lc_log .codemailto { background: yellow; }
    .lc_log .codetel { background: yellow; }
    .lc_log .clink { width: 100%; }
    .lc_log .statuscode { width: 50px; text-align: center; }
</style>

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
    echo '  </div>
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
$fragment->setVar('title', $this->i18n('linkchecker_crawler'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
