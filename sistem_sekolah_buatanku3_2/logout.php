<?php
/**
 * logout.php — Logout administrator
 * 
 * Hancurkan session dan redirect ke login.php
 */

require_once __DIR__ . '/config/session.php';

logout_admin();

header('Location: login.php?pesan=logout');
exit;