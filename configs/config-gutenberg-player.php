<?php
if ( ! defined( 'ABSPATH' ) ) exit;
return json_decode(dzscommon_read_from_file_ob(DZSYTB_BASE_PATH . 'configs/config-gutenberg-player.json'), true);
