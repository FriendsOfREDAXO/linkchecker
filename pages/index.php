<?php
echo rex_view::title($this->i18n('linkchecker_title'));

//include rex_be_controller::getCurrentPageObject()->getSubPath();
rex_be_controller::includeCurrentPageSubPath();
