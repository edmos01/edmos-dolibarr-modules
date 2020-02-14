<?php
/* Copyright (C) 2015      Nicolas Rivera       <nrivera.pro@gmail.com>
 * Copyright (C) 2015-2016 Alexandre Spangaro	<aspangaro@zendsi.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * \file		admin/about.php
 * \ingroup		oblyon
 * \brief		About Page < Oblyon Theme Configurator >
 */
// Dolibarr environment
$res = @include '../../main.inc.php'; // From htdocs directory
if (! $res) {
	$res = @include '../../../main.inc.php'; // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
require_once '../lib/oblyon.lib.php';

dol_include_once('/oblyon/lib/php-markdown/markdown.php');

// Langs
$langs->load("oblyon@oblyon");

// Access control
if (! $user->admin)
  accessforbidden();

// Parameters
$action = GETPOST('action', 'alpha');

/*
 * Actions
 */

/*
 * View
 */
$page_name = "ThemeOblyonAboutTitle";
llxHeader('', $langs->trans($page_name));

// Subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print_fiche_titre($langs->trans($page_name), $linkback);

// Configuration header
$head = oblyon_admin_prepare_head();
dol_fiche_head($head, 'about', $langs->trans("Module113900Name"), 0, 'oblyon@oblyon');

print '<table class="noborder" width="100%">';

print '<tr class="liste_titre"><td colspan="2">' . $langs->trans("Authors") . '</td>';
print '</tr>';

// Alexandre Spangaro
print '<tr><td class="titlefield center"><img src="../img/asilib.png"></td>';
print '<td><b>Alexandre Spangaro</b>&nbsp;-&nbsp;Comptable / développeur';
print '<br>Asilib - Votre gestion informatique en toute liberté !<br>' . $langs->trans("Email") . ' : aspangaro@zendsi.com';
print '<br><a target="_blank" title="Twitter" alt="Twitter" href="http://twitter.com/alexspangaro"><img src="../img/tweet.png" width="20"></a>&nbsp;<a target="_blank" title="Linkedin" alt="Linkedin" href="https://fr.linkedin.com/in/aspangaro"><img src="../img/link.png" width="20"></a>';
print '<br>&nbsp;';
print '</td></tr>';

print '</table>';

print '<br>';

print '<table class="noborder" width="100%">';

print '<tr class="liste_titre"><td colspan="2">' . $langs->trans("OldAuthors") . '</td>';
print '</tr>';

// Nicolas Rivera
print '<tr><td class="titlefield center"><img src="../img/object_oblyon.png"></td>';
print '<td><b>Nicolas Rivera</b>&nbsp;-&nbsp;Développeur';
//print '<br>' . $langs->trans("Email") . ' : nrivera.pro@gmail.com<br>';
print '<br>&nbsp;';
print '</td></tr>';

print '</table>';

print '<br>';

print '<h2>Licence</h2>';
print $langs->trans("LicenseMessage");
print '<h2>Bugs / comments</h2>';
print $langs->trans("AboutMessage");

$buffer = file_get_contents(dol_buildpath('/theme/oblyon/CHANGELOG', 0));
echo Markdown($buffer);

llxFooter();

$db->close();
