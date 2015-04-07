<?php

/**
 * @file plugins/generic/publicProfiles/index.php
 *
 * Copyright (c) 2015 Carola Fanselow, FU Berlin
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Wrapper for the public profiles plugin.
 *
 */


require_once('PublicProfilesPlugin.inc.php');

return new PublicProfilesPlugin();

?>
