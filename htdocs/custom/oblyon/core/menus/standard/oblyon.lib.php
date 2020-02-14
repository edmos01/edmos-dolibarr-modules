<?php
/**
 * Copyright (C) 2013-2016	Nicolas Rivera		<nrivera.pro@gmail.com>
 * Copyright (C) 2015-2016	Alexandre Spangaro	<aspangaro@zendsi.com>
 *
 * Copyright (C) 2010-2013	Laurent Destailleur	<eldy@users.sourceforge.net>
 * Copyright (C) 2010		Regis Houssin		<regis.houssin@capnetworks.com>
 * Copyright (C) 2012-2013	Juanjo Menent		<jmenent@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *	\file		htdocs/custom/oblyon/core/menus/standard/oblyon.lib.php
 *	\brief		Library for file Oblyon menus
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';

// Translations
$langs->load ( "oblyon@oblyon" );

/**
 * Core function to output top menu oblyon
 *
 * @param 	DoliDB	$db				Database handler
 * @param 	string	$atarget		Target
 * @param 	int		$type_user	 	0=Menu for backoffice, 1=Menu for front office
 * @param		array	&$tabMenu		 If array with menu entries already loaded, we put this array here (in most cases, it's empty)
 * @param	array	&$menu			Object Menu to return back list of menu entries
 * @param	int		$noout			Disable output (Initialise &$menu only).
 * @return	void
 */
function print_oblyon_menu($db,$atarget,$type_user,&$tabMenu,&$menu,$noout=0,$forcemainmenu='',$forceleftmenu='',$moredata=null) {
	global $user,$conf,$langs,$dolibarr_main_db_name,$mysoc;

	$mainmenu=(empty($_SESSION["mainmenu"])?'':$_SESSION["mainmenu"]);
	$leftmenu=(empty($_SESSION["leftmenu"])?'':$_SESSION["leftmenu"]);

	$id='mainmenu';
	$listofmodulesforexternal=explode(',',$conf->global->MAIN_MODULES_FOR_EXTERNAL);

	// Show logo company
	if (!empty($conf->global->MAIN_MENU_INVERT) && empty($noout) && ! empty($conf->global->MAIN_SHOW_LOGO)) {
	if (empty($conf->dol_optimize_smallscreen)) { 
		$mysoc->logo=$conf->global->MAIN_INFO_SOCIETE_LOGO;
		if (! empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo)) {
			$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode($mysoc->logo);
			$title=$langs->trans("Home");
		} else {
			$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
			$title=$langs->trans("GoIntoSetupToChangeLogo");
		}
	} else {
		$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
		if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini)) {
			$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_mini);
			$title=$langs->trans("Home");
		} else {
			$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
			$title=$langs->trans("GoIntoSetupToChangeLogo");
		}
	}

	print "\n".'<!-- Show logo on menu -->'."\n";
	print '<div class="db-menu__logo">';
	print '<a class="db-menu__logo__link	text-center" href="'.DOL_URL_ROOT.'" alt="'.dol_escape_htmltag($title).'" title="'.dol_escape_htmltag($title).'">';
	print '<img class="db-menu__logo__img" src="'.$urllogo.'" alt="Logo">';
	print '</a>'."\n";
	print '</div>';
	}

	if(DOL_VERSION >='3.9.0')
	{
		if (is_array($moredata) && ! empty($moredata['searchform']))
		{
			print "\n";
			print "<!-- Begin SearchForm -->\n";
			print '<div id="blockvmenusearch" class="blockvmenusearch">'."\n";
			print $moredata['searchform'];
			print '</div>'."\n";
			print "<!-- End SearchForm -->\n";
			print '$&nbsp;';
		}
	}
	
	if ( empty($conf->global->MAIN_MENU_INVERT) && $conf->global->OBLYON_HIDE_LEFTMENU ) {
	print '<div class="pushy-btn" title="'.$langs->trans("ShowLeftMenu").'">&#8801;</div>';
	}

	if (empty($noout)) print_start_menu_array();

	// Home
	$showmode=1;
	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "home") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='home';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("Home"), 1, DOL_URL_ROOT.'/index.php?mainmenu=home&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/index.php?mainmenu=home&amp;leftmenu=', $langs->trans("Home"), 0, $showmode, $atarget, "home", '');

	// Third parties
	$tmpentry=array('enabled'=>(( ! empty($conf->societe->enabled) && (empty($conf->global->SOCIETE_DISABLE_PROSPECTS) || empty($conf->global->SOCIETE_DISABLE_CUSTOMERS))) || ! empty($conf->fournisseur->enabled)), 'perms'=>(! empty($user->rights->societe->lire) || ! empty($user->rights->fournisseur->lire)), 'module'=>'societe|fournisseur');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
	$langs->load("companies");
	$langs->load("suppliers");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "companies") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='companies';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("ThirdParties"), $showmode, DOL_URL_ROOT.'/societe/index.php?mainmenu=companies&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/societe/index.php?mainmenu=companies&amp;leftmenu=', $langs->trans("ThirdParties"), 0, $showmode, $atarget, "companies", '');
	}

	// Products-Services
	$tmpentry=array('enabled'=>(! empty($conf->product->enabled) || ! empty($conf->service->enabled)), 'perms'=>(! empty($user->rights->produit->lire) || ! empty($user->rights->service->lire)), 'module'=>'product|service');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
	$langs->load("products");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "products") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='products';

	$chaine="";
	if (! empty($conf->product->enabled)) {
		$chaine.=$langs->trans("Products");
	}
	if (! empty($conf->product->enabled) && ! empty($conf->service->enabled)) {
		$chaine.="/";
	}
	if (! empty($conf->service->enabled)) {
		$chaine.=$langs->trans("Services");
	}

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($chaine, $showmode, DOL_URL_ROOT.'/product/index.php?mainmenu=products&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/product/index.php?mainmenu=products&amp;leftmenu=', $chaine, 0, $showmode, $atarget, "products", '');
	}

	// Commercial
	$menuqualified=0;
	if (! empty($conf->propal->enabled)) $menuqualified++;
	if (! empty($conf->commande->enabled)) $menuqualified++;
	if (! empty($conf->fournisseur->enabled)) $menuqualified++;
	if (! empty($conf->contrat->enabled)) $menuqualified++;
	if (! empty($conf->ficheinter->enabled)) $menuqualified++;
	$tmpentry=array(
	'enabled'=>$menuqualified, 
	'perms'=>(! empty($user->rights->societe->lire) || ! empty($user->rights->societe->contact->lire)), 
	'module'=>'propal|commande|fournisseur|contrat|ficheinter');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
	$langs->load("commercial");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "commercial") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='commercial';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("Commercial"), $showmode, DOL_URL_ROOT.'/comm/index.php?mainmenu=commercial&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/comm/index.php?mainmenu=commercial&amp;leftmenu=', $langs->trans("Commercial"), 0, $showmode, $atarget, "commercial", "");
	}

	// Financial
	$menuqualified=0;
	if (! empty($conf->comptabilite->enabled)) $menuqualified++;
	if (! empty($conf->accounting->enabled)) $menuqualified++;
	if (! empty($conf->facture->enabled)) $menuqualified++;
	if (! empty($conf->don->enabled)) $menuqualified++;
	if (! empty($conf->tax->enabled)) $menuqualified++;
	if (! empty($conf->salaries->enabled)) $menuqualified++;
	if (! empty($conf->supplier_invoice->enabled)) $menuqualified++;
	if (! empty($conf->loan->enabled)) $menuqualified++;
	$tmpentry=array(
		'enabled'=>$menuqualified,
		'perms'=>(! empty($user->rights->compta->resultat->lire) || ! empty($user->rights->accounting->plancompte->lire) || ! empty($user->rights->facture->lire) || ! empty($user->rights->don->lire) || ! empty($user->rights->tax->charges->lire) || ! empty($user->rights->salaries->read) || ! empty($user->rights->fournisseur->facture->lire) || ! empty($user->rights->loan->read)),
		'module'=>'comptabilite|accounting|facture|supplier_invoice|don|tax|salaries|loan');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
		$langs->load("compta");

		if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "accountancy") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
		else $itemsel=FALSE;
		$idsel='accountancy';

		if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
		if (empty($noout)) print_text_menu_entry($langs->trans("MenuFinancial"), $showmode, DOL_URL_ROOT.'/compta/index.php?mainmenu=accountancy&amp;leftmenu=', $id, $idsel, $atarget);
		if (empty($noout)) print_end_menu_entry($showmode);
			$menu->add('/compta/index.php?mainmenu=accountancy&amp;leftmenu=', $langs->trans("MenuFinancial"), 0, $showmode, $atarget, "accountancy", '');
	}

	// Bank
	$tmpentry=array('enabled'=>(! empty($conf->banque->enabled) || ! empty($conf->prelevement->enabled)),
	'perms'=>(! empty($user->rights->banque->lire) || ! empty($user->rights->prelevement->lire)),
	'module'=>'banque|prelevement');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
	$langs->load("compta");
	$langs->load("banks");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "bank") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='bank';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("MenuBankCash"), $showmode, DOL_URL_ROOT.'/compta/bank/index.php?mainmenu=bank&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/compta/bank/index.php?mainmenu=bank&amp;leftmenu=', $langs->trans("MenuBankCash"), 0, $showmode, $atarget, "bank", '');
	}

	// Projects
	$tmpentry=array('enabled'=>(! empty($conf->projet->enabled)),
	'perms'=>(! empty($user->rights->projet->lire)),
	'module'=>'projet');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
	$langs->load("projects");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "project") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='project';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("Projects"), $showmode, DOL_URL_ROOT.'/projet/index.php?mainmenu=project&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/projet/index.php?mainmenu=project&amp;leftmenu=', $langs->trans("Projects"), 0, $showmode, $atarget, "project", '');
	}
	
	// HRM
	if (DOL_VERSION >='3.9.0') {
		$tmpentry=array('enabled'=>(! empty($conf->hrm->enabled) || ! empty($conf->holiday->enabled) || ! empty($conf->deplacement->enabled) || ! empty($conf->expensereport->enabled)),
			'perms'=>(! empty($user->rights->hrm->employee->read) || ! empty($user->rights->holiday->write) || ! empty($user->rights->deplacement->lire) || ! empty($user->rights->expensereport->lire)),
			'module'=>'hrm|holiday|deplacement|expensereport');
	}
	else if (DOL_VERSION >='3.8.0') {
		$tmpentry=array('enabled'=>(! empty($conf->holiday->enabled) || ! empty($conf->deplacement->enabled) || ! empty($conf->expensereport->enabled)),
			'perms'=>(! empty($user->rights->holiday->write) || ! empty($user->rights->deplacement->lire) || ! empty($user->rights->expensereport->lire)),
			'module'=>'holiday|deplacement|expensereport');
	}
	else {
		$tmpentry=array('enabled'=>(! empty($conf->holiday->enabled) || ! empty($conf->deplacement->enabled)),
			'perms'=>(! empty($user->rights->holiday->write) || ! empty($user->rights->deplacement->lire)),
			'module'=>'holiday|deplacement');
	}

	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);

	if ($showmode) {
	$langs->load("holiday");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "hrm") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='hrm';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) { 
		if ( DOL_VERSION >='5.0.0' ) {
		print_text_menu_entry($langs->trans("HRM"), $showmode, DOL_URL_ROOT.'/hrm/index.php?mainmenu=hrm&amp;leftmenu=', $id, $idsel, $classname, $atarget);
		} else if ( DOL_VERSION >='4.0.0' ) {
		print_text_menu_entry($langs->trans("HRM"), $showmode, DOL_URL_ROOT.'/hrm/hrm.php?mainmenu=hrm&amp;leftmenu=', $id, $idsel, $classname, $atarget);
		} else if ( DOL_VERSION >='3.7.0' ) {
		print_text_menu_entry($langs->trans("HRM"), $showmode, DOL_URL_ROOT.'/compta/hrm.php?mainmenu=hrm&amp;leftmenu=', $id, $idsel, $classname, $atarget);
		} else {
		print_text_menu_entry($langs->trans("HRM"), $showmode, DOL_URL_ROOT.'/holiday/index.php?mainmenu=hrm&amp;leftmenu=', $id, $idsel, $atarget);
		}
	}
	if (empty($noout)) { 
		if ( DOL_VERSION >='5.0.0' ) {
		print_end_menu_entry($showmode);
		$menu->add('/hrm/index.php?mainmenu=hrm&amp;leftmenu=', $langs->trans("HRM"), 0, $showmode, $atarget, "hrm", '');
		} else if ( DOL_VERSION >='4.0.0' ) {
		print_end_menu_entry($showmode);
		$menu->add('/hrm/hrm.php?mainmenu=hrm&amp;leftmenu=', $langs->trans("HRM"), 0, $showmode, $atarget, "hrm", '');
		} else if ( DOL_VERSION >='3.7.0' ) {
		print_end_menu_entry($showmode);
		$menu->add('/compta/hrm.php?mainmenu=hrm&amp;leftmenu=', $langs->trans("HRM"), 0, $showmode, $atarget, "hrm", '');
		} else {
		print_end_menu_entry($showmode);
		$menu->add('/holiday/index.php?mainmenu=holiday&amp;leftmenu=', $langs->trans("HRM"), 0, $showmode, $atarget, "hrm", '');
		}
	}
	}

	// Tools
	if ( DOL_VERSION >='3.4.0' && DOL_VERSION <'3.6.0') {
	$tmpentry=array('enabled'=>(! empty($conf->mailing->enabled) || ! empty($conf->export->enabled) || ! empty($conf->import->enabled)),
	'perms'=>(! empty($user->rights->mailing->lire) || ! empty($user->rights->export->lire) || ! empty($user->rights->import->run)),
	'module'=>'mailing|export|import');
	} else if ( DOL_VERSION >='3.6.0' && DOL_VERSION <'3.7.0') {
	$tmpentry=array('enabled'=>(! empty($conf->mailing->enabled) || ! empty($conf->export->enabled) || ! empty($conf->import->enabled) || ! empty($conf->opensurvey->enabled)),
	'perms'=>(! empty($user->rights->mailing->lire) || ! empty($user->rights->export->lire) || ! empty($user->rights->import->run) || ! empty($user->rights->opensurvey->read)),
	'module'=>'mailing|export|import|opensurvey');
	} else {
	$tmpentry=array('enabled'=>(! empty($conf->barcode->enabled) || ! empty($conf->mailing->enabled) || ! empty($conf->export->enabled) || ! empty($conf->import->enabled) || ! empty($conf->opensurvey->enabled)),
	'perms'=>(! empty($conf->barcode->enabled) || ! empty($user->rights->mailing->lire) || ! empty($user->rights->export->lire) || ! empty($user->rights->import->run) || ! empty($user->rights->opensurvey->read)),
	'module'=>'mailing|export|import|opensurvey');
	}
	
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	
	if ($showmode) {
	$langs->load("other");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "tools") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='tools';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("Tools"), $showmode, DOL_URL_ROOT.'/core/tools.php?mainmenu=tools&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/core/tools.php?mainmenu=tools&amp;leftmenu=', $langs->trans("Tools"), 0, $showmode, $atarget, "tools", '');
	}

	// OSCommerce 1
	$tmpentry=array('enabled'=>(! empty($conf->boutique->enabled)),
	'perms'=>1,
	'module'=>'boutique');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
	$langs->load("shop");

	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "shop") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='shop';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("OSCommerce"), $showmode, DOL_URL_ROOT.'/boutique/index.php?mainmenu=shop&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/boutique/index.php?mainmenu=shop&amp;leftmenu=', $langs->trans("OSCommerce"), 0, $showmode, $atarget, "shop", '');
	}

	// Members
	$tmpentry=array('enabled'=>(! empty($conf->adherent->enabled)),
	'perms'=>(! empty($user->rights->adherent->lire)),
	'module'=>'adherent');
	$showmode=dol_oblyon_showmenu($type_user, $tmpentry, $listofmodulesforexternal);
	if ($showmode) {
	
	if ($_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "members") { $itemsel=TRUE; $_SESSION['idmenu']=''; }
	else $itemsel=FALSE;
	$idsel='members';

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($langs->trans("MenuMembers"), $showmode, DOL_URL_ROOT.'/adherents/index.php?mainmenu=members&amp;leftmenu=', $id, $idsel, $atarget);
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add('/adherents/index.php?mainmenu=members&amp;leftmenu=', $langs->trans("MenuMembers"), 0, $showmode, $atarget, "members", '');
	}


	// Show personalized menus
	$menuArbo = new Menubase($db,'oblyon');
	$newTabMenu = $menuArbo->menuTopCharger('','',$type_user,'oblyon',$tabMenu);	// Return tabMenu with only top entries

	$num = count($newTabMenu);
	for($i = 0; $i < $num; $i++) {
	$idsel=(empty($newTabMenu[$i]['mainmenu'])?'none':$newTabMenu[$i]['mainmenu']);

	$showmode=dol_oblyon_showmenu($type_user,$newTabMenu[$i],$listofmodulesforexternal);
	if ($showmode == 1) {
		$url = $shorturl = $newTabMenu[$i]['url'];
		if (! preg_match("/^(http:\/\/|https:\/\/)/i",$newTabMenu[$i]['url']))
		{
		if ( DOL_VERSION >='3.4.0' && DOL_VERSION <'3.5.0') {
			$param='';
			if (! preg_match('/mainmenu/i',$url) || ! preg_match('/leftmenu/i',$url)) {
			if (! preg_match('/\?/',$url)) $param.='?';
			else $param.='&';
			$param.='mainmenu='.$newTabMenu[$i]['mainmenu'].'&amp;leftmenu=';
			}
			//$url.="idmenu=".$newTabMenu[$i]['rowid'];	// Already done by menuLoad
			$url = dol_buildpath($url,1).$param;
			$shorturl = $newTabMenu[$i]['url'].$param;
		} else { // >v3.4
			$tmp=explode('?',$newTabMenu[$i]['url'],2);
			$url = $shorturl = $tmp[0];
			$param = (isset($tmp[1])?$tmp[1]:'');

			if (! preg_match('/mainmenu/i',$url) || ! preg_match('/leftmenu/i',$url)) $param.=($param?'&':'').'mainmenu='.$newTabMenu[$i]['mainmenu'].'&amp;leftmenu=';
						//$url.="idmenu=".$newTabMenu[$i]['rowid'];	// Already done by menuLoad
			$url = dol_buildpath($url,1).($param?'?'.$param:'');
			$shorturl = $shorturl.($param?'?'.$param:'');
		}
		}
		$url=preg_replace('/__LOGIN__/',$user->login,$url);
		$shorturl=preg_replace('/__LOGIN__/',$user->login,$shorturl);
		$url=preg_replace('/__USERID__/',$user->id,$url);
		$shorturl=preg_replace('/__USERID__/',$user->id,$shorturl);

		// Define the class (top menu selected or not)
		if (! empty($_SESSION['idmenu']) && $newTabMenu[$i]['rowid'] == $_SESSION['idmenu']) $itemsel=TRUE;
		else if (! empty($_SESSION["mainmenu"]) && $newTabMenu[$i]['mainmenu'] == $_SESSION["mainmenu"]) $itemsel=TRUE;
		else $itemsel=FALSE;
	}
	else if ($showmode == 2) $itemsel=FALSE;

	if (empty($noout)) print_start_menu_entry($idsel,$itemsel,$showmode);
	if (empty($noout)) print_text_menu_entry($newTabMenu[$i]['titre'], $showmode, $url, $id, $idsel, ($newTabMenu[$i]['target']?$newTabMenu[$i]['target']:$atarget));
	if (empty($noout)) print_end_menu_entry($showmode);
	$menu->add($shorturl, $newTabMenu[$i]['titre'], 0, $showmode, ($newTabMenu[$i]['target']?$newTabMenu[$i]['target']:$atarget), ($newTabMenu[$i]['mainmenu']?$newTabMenu[$i]['mainmenu']:$newTabMenu[$i]['rowid']), '');
	}

	if ( DOL_VERSION >='3.4.0' && DOL_VERSION <'3.5.0') {
	if (empty($noout)) print_end_menu_array();
	} else { // >v3.4
	$showmode=1;
	if (empty($noout)) print_end_menu_entry($showmode);
	if (empty($noout)) print_end_menu_array();
	}
	 
	if ( DOL_VERSION >='3.6.0' ) {
	return 0;
	}
}


/**
 * Output start menu array
 *
 * @return	void
 */
function print_start_menu_array() {
	global $conf;
	print '<nav class="db-nav	main-nav'.(empty($conf->global->MAIN_MENU_INVERT)?"":"	is-inverted").'">';
	print '<ul class="main-nav__list">';
}

/**
 * Output start menu entry
 *
 * @param	string	$idsel		Text
 * @param	bool	$itemsel	Item Selected = TRUE
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @return	void
 */
function print_start_menu_entry($idsel,$itemsel,$showmode) {
	if ($showmode) {
	print '<li class="main-nav__item'.(($itemsel)?'	is-sel':'').'" id="mainmenutd_'.$idsel.'">';
	}
}

/**
 * Output menu entry
 *
 * @param	string	$text		Text
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @param	string	$url		Url
 * @param	string	$id			Id
 * @param	string	$idsel		Id sel
 * @param	string	$atarget	Target
 * @return	void
 */
function print_text_menu_entry($text, $showmode, $url, $id, $idsel, $atarget)
{
	global $langs;
	global $conf;

	if ($showmode == 1)
	{
		print '<a class="main-nav__link	main-nav__'.$idsel.'" href="'.$url.'"'.($atarget?' target="'.$atarget.'"':'').'>';
		if ($conf->global->OBLYON_ELDY_ICONS || $conf->global->OBLYON_ELDY_OLD_ICONS)
		{
			print '<i class="'.$id.'	'.$idsel.'"></i> ';
		} else {
			print '<i class="icon	icon--'.$idsel.'"></i> ';
		}
		print $text;
		print '</a>';
	}
	if ($showmode == 2)
	{
		print '<a class="main-nav__link is-disabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
		if ($conf->global->OBLYON_ELDY_ICONS || $conf->global->OBLYON_ELDY_OLD_ICONS)
		{
			print '<i class="'.$id.'	'.$idsel.'"></i> ';
		} else {
			print '<i class="icon	icon--'.$idsel.'"></i> ';
		}
		print $text;
		print '</a>';
	}
}

/**
 * Output end menu entry
 *
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @return	void
 */
function print_end_menu_entry($showmode)
{
	if ($showmode)
	{
		print '</li>';
	}
	print "\n";
}

/**
 * Output menu array
 *
 * @return	void
 */
function print_end_menu_array() {
	print '</ul>';
	print '</nav>';
	print "\n";
}

/**
 * Core function to output left menu oblyon
 *
 * @param	DoliDB		$db				 Database handler
 * @param 	array		$menu_array_before	Table of menu entries to show before entries of menu handler (menu->liste filled with menu->add)
 * @param	 array		$menu_array_after	 Table of menu entries to show after entries of menu handler (menu->liste filled with menu->add)
 * @param	array		&$tabMenu		 	If array with menu entries already loaded, we put this array here (in most cases, it's empty)
 * @param	array		&$menu				Object Menu to return back list of menu entries
 * @param	int			$noout				Disable output (Initialise &$menu only).
 * @param	string		$forcemainmenu		'x'=Force mainmenu to mainmenu='x'
 * @param	string		$forceleftmenu		'all'=Force leftmenu to '' (= all)
 * @param	array		$moredata			An array with more data to output
 * @return	void
 */
function print_left_oblyon_menu($db,$menu_array_before,$menu_array_after,&$tabMenu,&$menu,$noout=0,$forcemainmenu='',$forceleftmenu='',$moredata=null)
{
	global $user,$conf,$langs,$dolibarr_main_db_name,$mysoc;

	$newmenu = $menu;

	$mainmenu=($forcemainmenu?$forcemainmenu:$_SESSION["mainmenu"]);
	$leftmenu=($forceleftmenu?'':(empty($_SESSION["leftmenu"])?'none':$_SESSION["leftmenu"]));

	if ( !empty($conf->global->MAIN_MENU_INVERT) && $conf->global->OBLYON_HIDE_LEFTMENU ) {
	print '<div class="pushy-btn" title="'.$langs->trans("ShowLeftMenu").'">&#8801;</div>';
	}

	if ($conf->global->OBLYON_SHOW_COMPNAME && $conf->global->MAIN_INFO_SOCIETE_NOM) {
	print '<div class="db-menu__society	center">';
	if ($conf->global->MAIN_MENU_INVERT) {
		print '<h1><a class="db-menu__society__link" href="'.DOL_URL_ROOT.'" title="'.$langs->trans("Home").'">'.$conf->global->MAIN_INFO_SOCIETE_NOM.'</a></h1>';
	} else {
		print '<h1>'. $conf->global->MAIN_INFO_SOCIETE_NOM .'</h1>';
	}
	print '</div>';
	}
	
	// Show logo company
	if (empty($conf->global->MAIN_MENU_INVERT) && empty($noout) && ! empty($conf->global->MAIN_SHOW_LOGO)) {
	if (empty($conf->dol_optimize_smallscreen)) { 
		$mysoc->logo=$conf->global->MAIN_INFO_SOCIETE_LOGO;
		if (! empty($mysoc->logo) && is_readable($conf->mycompany->dir_output.'/logos/'.$mysoc->logo)) {
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode($mysoc->logo);
		$title=$langs->trans("Home");
		} else {
		$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
		$title=$langs->trans("GoIntoSetupToChangeLogo");
		}
	} else {
		$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
		if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini)) {
		$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=companylogo&amp;file='.urlencode('thumbs/'.$mysoc->logo_mini);
		$title=$langs->trans("Home");
		} else {
		$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
		$title=$langs->trans("GoIntoSetupToChangeLogo");
		}
	}

	
	print "\n".'<!-- Show logo on menu -->'."\n";
	print '<div class="db-menu__logo">';
	print '<a class="db-menu__logo__link	text-center" href="'.DOL_URL_ROOT.'" alt="'.dol_escape_htmltag($title).'" title="'.dol_escape_htmltag($title).'">';
	print '<img src="'.$urllogo.'" class="db-menu__logo__img" alt="Logo">';
	print '</a>'."\n";
	print '</div>';
	}

	if(DOL_VERSION >='3.9.0')
	{
		if (is_array($moredata) && ! empty($moredata['searchform']))
		{
			print "\n";
			print "<!-- Begin SearchForm -->\n";
			print '<div id="blockvmenusearch" class="blockvmenusearch">'."\n";
			print $moredata['searchform'];
			print '</div>'."\n";
			print "<!-- End SearchForm -->\n";
		}
	}

	/**
	 * We update newmenu with entries found into database
	 * --------------------------------------------------
	 */
	if ($mainmenu) {
	/*
	 * Menu HOME
	 */
	if ($mainmenu == 'home') {
		$langs->load("users");

		if ($user->admin) {
		$langs->load("admin");
		$langs->load("help");

		// Setup
		$newmenu->add("/admin/index.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Setup"), 0, 1, '', $mainmenu, 'setup');

			$warnpicto='';
			if (empty($conf->global->MAIN_INFO_SOCIETE_NOM) || empty($conf->global->MAIN_INFO_SOCIETE_COUNTRY)) {
			$langs->load("errors");
			$warnpicto =' '.img_warning($langs->trans("WarningMandatorySetupNotComplete"));
			}
			$newmenu->add("/admin/company.php?mainmenu=home", $langs->trans("MenuCompanySetup").$warnpicto,1);
			$warnpicto='';
		 
			if ( DOL_VERSION >='3.4.0' && DOL_VERSION <'3.5.0') {
			if (count($conf->modules) <= 1) {	// If only user module enabled
				$langs->load("errors");
				$warnpicto = ' '.img_warning($langs->trans("WarningMandatorySetupNotComplete"));
			}
			} else {
			if (count($conf->modules) <= (empty($conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING)?1:$conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING))	// If only user module enabled
			{
				$langs->load("errors");
				$warnpicto = ' '.img_warning($langs->trans("WarningMandatorySetupNotComplete"));
			}
			}
		
			
			$newmenu->add("/admin/modules.php?mainmenu=home", $langs->trans("Modules").$warnpicto,1);
			$newmenu->add("/admin/menus.php?mainmenu=home", $langs->trans("Menus"),1);
			$newmenu->add("/admin/ihm.php?mainmenu=home", $langs->trans("GUISetup"),1);
			if (! in_array($langs->defaultlang,array('en_US','en_GB','en_NZ','en_AU','fr_FR','fr_BE','es_ES','ca_ES'))) {
			$newmenu->add("/admin/translation.php", $langs->trans("Translation"),1);
			}
			$newmenu->add("/admin/boxes.php?mainmenu=home", $langs->trans("Boxes"),1);
			$newmenu->add("/admin/delais.php?mainmenu=home",$langs->trans("Alerts"),1);
			$newmenu->add("/admin/proxy.php?mainmenu=home", $langs->trans("Security"),1);
			$newmenu->add("/admin/limits.php?mainmenu=home", $langs->trans("MenuLimits"),1);
			$newmenu->add("/admin/pdf.php?mainmenu=home", $langs->trans("PDF"),1);
			$newmenu->add("/admin/mails.php?mainmenu=home", $langs->trans("Emails"),1);
			$newmenu->add("/admin/sms.php?mainmenu=home", $langs->trans("SMS"),1);
		
			if ( DOL_VERSION >='3.4.0' && DOL_VERSION <'3.5.0') {
			$newmenu->add("/admin/dict.php?mainmenu=home", $langs->trans("DictionnarySetup"),1);
			} else {
			$newmenu->add("/admin/dict.php?mainmenu=home", $langs->trans("Dictionary"),1);
			}
		
			$newmenu->add("/admin/const.php?mainmenu=home", $langs->trans("OtherSetup"),1);

		// System tools
		$newmenu->add("/admin/tools/index.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("SystemTools"), 0, 1, '', $mainmenu, 'admintools');

			$newmenu->add('/admin/system/dolibarr.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('InfoDolibarr'), 1);
			if (empty($leftmenu) || $leftmenu=='admintools_info') {
				$newmenu->add('/admin/system/modules.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('Modules'), 2);
				$newmenu->add('/admin/triggers.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('Triggers'), 2);
				if ( DOL_VERSION >='5.0.0' ) {
					$newmenu->add('/admin/system/filecheck.php?mainmenu=home&amp;leftmenu=admintools_info', $langs->trans('FileCheck'), 2);
				}
			}
			if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add('/admin/system/browser.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoBrowser'), 1);
			}
			$newmenu->add('/admin/system/os.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoOS'), 1);
			$newmenu->add('/admin/system/web.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoWebServer'), 1);
			$newmenu->add('/admin/system/phpinfo.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoPHP'), 1);
			//if (function_exists('xdebug_is_enabled')) $newmenu->add('/admin/system/xdebug.php', $langs->trans('XDebug'),1);
			$newmenu->add('/admin/system/database.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoDatabase'), 1);
			if (function_exists('eaccelerator_info')) $newmenu->add("/admin/tools/eaccelerator.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("EAccelerator"),1);
			//$newmenu->add("/admin/system/perf.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("InfoPerf"),1);
			$newmenu->add("/admin/tools/purge.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Purge"),1);
			$newmenu->add("/admin/tools/dolibarr_export.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Backup"),1);
			$newmenu->add("/admin/tools/dolibarr_import.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Restore"),1);
			$newmenu->add("/admin/tools/update.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("MenuUpgrade"),1);
			$newmenu->add("/admin/tools/listevents.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Audit"),1);
			$newmenu->add("/admin/tools/listsessions.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Sessions"),1);
			$newmenu->add('/admin/system/about.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('About'), 1);
			$newmenu->add("/support/index.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("HelpCenter"),1,1,'targethelp');


		// Modules system tools
		if (! empty($conf->product->enabled) || ! empty($conf->service->enabled) || ! empty($conf->barcode->enabled)	// TODO We should enabled module system tools entry without hardcoded test, but when at least one modules bringing such entries are on
			|| ! empty($conf->global->MAIN_MENU_ENABLE_MODULETOOLS))	// Some external modules may need to force to have this entry on. 
		{			
			if (empty($user->societe_id)) {
			$newmenu->add("/admin/tools/index.php?mainmenu=home&amp;leftmenu=modulesadmintools", $langs->trans("ModulesSystemTools"), 0, 1, '', $mainmenu, 'modulesadmintools');
			// Special case: This entry can't be embedded into modules because we need it for both module service and products and we don't want duplicate lines.
			if ((empty($leftmenu) || $leftmenu=="modulesadmintools") && $user->admin) {
				$langs->load("products");
				$newmenu->add("/product/admin/product_tools.php?mainmenu=home&amp;leftmenu=modulesadmintools", $langs->trans("ProductVatMassChange"), 1, $user->admin);
			}
			}
		}
		
		} // end if ($user->admin)

		// Users & Groups
		$newmenu->add("/user/home.php?leftmenu=users", $langs->trans("MenuUsersAndGroups"), 0, 1, '', $mainmenu, 'users');

		$newmenu->add("/user/index.php", $langs->trans("Users"), 1, $user->rights->user->user->lire || $user->admin);
		if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add("/user/card.php?action=create", $langs->trans("NewUser"),2, $user->rights->user->user->creer || $user->admin);
		} else {
			$newmenu->add("/user/fiche.php?action=create", $langs->trans("NewUser"),2, $user->rights->user->user->creer || $user->admin);
		}

		$newmenu->add("/user/group/index.php", $langs->trans("Groups"), 1, ($conf->global->MAIN_USE_ADVANCED_PERMS?$user->rights->user->group_advance->read:$user->rights->user->user->lire) || $user->admin);

		if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add("/user/group/card.php?action=create", $langs->trans("NewGroup"), 2, ($conf->global->MAIN_USE_ADVANCED_PERMS?$user->rights->user->group_advance->write:$user->rights->user->user->creer) || $user->admin);
		} else {
			$newmenu->add("/user/group/fiche.php?action=create", $langs->trans("NewGroup"), 2, ($conf->global->MAIN_USE_ADVANCED_PERMS?$user->rights->user->group_advance->write:$user->rights->user->user->creer) || $user->admin);
		}
	} // end Menu Home


	/*
	 * Menu THIRDPARTIES
	 */
	if ($mainmenu == 'companies') {
		// Societes
		if (! empty($conf->societe->enabled)) {
		$langs->load("companies");
		$newmenu->add("/societe/index.php?leftmenu=thirdparties", $langs->trans("ThirdParty"), 0, $user->rights->societe->lire, '', $mainmenu, 'thirdparties');

		if ($user->rights->societe->creer) {
			$newmenu->add("/societe/soc.php?action=create", $langs->trans("MenuNewThirdParty"),1);
			if (! $conf->use_javascript_ajax) $newmenu->add("/societe/soc.php?action=create&amp;private=1",$langs->trans("MenuNewPrivateIndividual"),1);
		}
		}

		if ( DOL_VERSION >='3.9.0') {
			$newmenu->add("/societe/list.php?leftmenu=thirdparties", $langs->trans("List"),1);
		}

		// Prospects
		if (! empty($conf->societe->enabled) && empty($conf->global->SOCIETE_DISABLE_PROSPECTS)) {
		$langs->load("commercial");

		if ( DOL_VERSION <'3.9.0') {
			$newmenu->add("/comm/prospect/list.php?leftmenu=prospects", $langs->trans("ListProspectsShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'prospects');

			$newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=-1", $langs->trans("LastProspectDoNotContact"), 2, $user->rights->societe->lire);
			$newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=0", $langs->trans("LastProspectNeverContacted"), 2, $user->rights->societe->lire);
			$newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=1", $langs->trans("LastProspectToContact"), 2, $user->rights->societe->lire);
			$newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=2", $langs->trans("LastProspectContactInProcess"), 2, $user->rights->societe->lire);
			$newmenu->add("/comm/prospect/list.php?sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;stcomm=3", $langs->trans("LastProspectContactDone"), 2, $user->rights->societe->lire);
		} else {
			$newmenu->add("/societe/list.php?type=p&leftmenu=prospects", $langs->trans("ListProspectsShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'prospects');
		}

		$newmenu->add("/societe/soc.php?leftmenu=prospects&amp;action=create&amp;type=p", $langs->trans("MenuNewProspect"), 2, $user->rights->societe->creer);
		//$newmenu->add("/contact/list.php?leftmenu=customers&amp;type=p", $langs->trans("Contacts"), 2, $user->rights->societe->contact->lire);
		}

		// Customers/Prospects
		if (! empty($conf->societe->enabled) && empty($conf->global->SOCIETE_DISABLE_CUSTOMERS)) {
		$langs->load("commercial");
		if ( DOL_VERSION <'3.9.0') {
			$newmenu->add("/comm/list.php?leftmenu=customers", $langs->trans("ListCustomersShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'customers');
		} else {
			$newmenu->add("/societe/list.php?type=c&leftmenu=customers", $langs->trans("ListCustomersShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'customers');
		}
		$newmenu->add("/societe/soc.php?leftmenu=customers&amp;action=create&amp;type=c", $langs->trans("MenuNewCustomer"), 2, $user->rights->societe->creer);
		//$newmenu->add("/contact/list.php?leftmenu=customers&amp;type=c", $langs->trans("Contacts"), 2, $user->rights->societe->contact->lire);
		}

		// Suppliers
		if (! empty($conf->societe->enabled) && ! empty($conf->fournisseur->enabled)) {
		$langs->load("suppliers");
		if ( DOL_VERSION <'3.7.0') {
			$newmenu->add("/fourn/liste.php?leftmenu=suppliers", $langs->trans("ListSuppliersShort"), 1, $user->rights->fournisseur->lire, '', $mainmenu, 'suppliers');
		} else if ( DOL_VERSION <'3.9.0') {
			$newmenu->add("/fourn/list.php?leftmenu=suppliers", $langs->trans("ListSuppliersShort"), 1, $user->rights->fournisseur->lire, '', $mainmenu, 'suppliers');
		} else {
			$newmenu->add("/societe/list.php?type=f&leftmenu=suppliers", $langs->trans("ListSuppliersShort"), 1, $user->rights->fournisseur->lire, '', $mainmenu, 'suppliers');
		}
		//$newmenu->add("/fourn/list.php?leftmenu=suppliers", $langs->trans("List"), 2, $user->rights->societe->lire && $user->rights->fournisseur->lire);
		//$newmenu->add("/contact/list.php?leftmenu=suppliers&amp;type=f",$langs->trans("Contacts"), 2, $user->rights->societe->lire && $user->rights->fournisseur->lire && $user->rights->societe->contact->lire);
		$newmenu->add("/societe/soc.php?leftmenu=suppliers&amp;action=create&amp;type=f",$langs->trans("MenuNewSupplier"), 2, $user->rights->societe->creer && $user->rights->fournisseur->lire);
		}

		// Contacts
		if ( DOL_VERSION <'3.7.0') {
		$newmenu->add("/contact/list.php?leftmenu=contacts", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("Contacts") : $langs->trans("ContactsAddresses")), 0, $user->rights->societe->contact->lire, '', $mainmenu, 'contacts');
		} else {
		$newmenu->add("/societe/index.php?leftmenu=thirdparties", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("ThirdParty") : $langs->trans("ContactsAddresses")), 0, $user->rights->societe->contact->lire, '', $mainmenu, 'contacts');
		}

		if ( DOL_VERSION <'3.7.0') {
		$newmenu->add("/contact/fiche.php?leftmenu=contacts&amp;action=create", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("NewContact") : $langs->trans("NewContactAddress")), 1, $user->rights->societe->contact->creer);
		} else {
		$newmenu->add("/contact/card.php?leftmenu=contacts&amp;action=create", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("NewContact") : $langs->trans("NewContactAddress")), 1, $user->rights->societe->contact->creer);
		}

		$newmenu->add("/contact/list.php?leftmenu=contacts", $langs->trans("List"), 1, $user->rights->societe->contact->lire);
		if (empty($conf->global->SOCIETE_DISABLE_PROSPECTS)) $newmenu->add("/contact/list.php?leftmenu=contacts&type=p", $langs->trans("Prospects"), 2, $user->rights->societe->contact->lire);
		if (empty($conf->global->SOCIETE_DISABLE_CUSTOMERS)) $newmenu->add("/contact/list.php?leftmenu=contacts&type=c", $langs->trans("Customers"), 2, $user->rights->societe->contact->lire);
		if (! empty($conf->fournisseur->enabled)) $newmenu->add("/contact/list.php?leftmenu=contacts&type=f", $langs->trans("Suppliers"), 2, $user->rights->societe->contact->lire);
		$newmenu->add("/contact/list.php?leftmenu=contacts&type=o", $langs->trans("Others"), 2, $user->rights->societe->contact->lire);
		//$newmenu->add("/contact/list.php?userid=$user->id", $langs->trans("MyContacts"), 1, $user->rights->societe->contact->lire);

		// Categories
		if (! empty($conf->categorie->enabled)) {
		$langs->load("categories");
		if (empty($conf->global->SOCIETE_DISABLE_PROSPECTS) && empty($conf->global->SOCIETE_DISABLE_CUSTOMERS))
		{
			// Categories prospects/customers
			$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=2", $langs->trans("CustomersProspectsCategoriesShort"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
			if ( DOL_VERSION <'3.7.0') {
			$newmenu->add("/categories/fiche.php?action=create&amp;type=2", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
			} else {
			$newmenu->add("/categories/card.php?action=create&amp;type=2", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
			}
		}

		// Categories Contact
		$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=4", $langs->trans("ContactCategoriesShort"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
		if ( DOL_VERSION <'3.7.0') {
		 $newmenu->add("/categories/fiche.php?action=create&amp;type=4", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
		} else {
			$newmenu->add("/categories/card.php?action=create&amp;type=4", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
		}

		// Categories suppliers
		if (! empty($conf->fournisseur->enabled)) {
			$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=1", $langs->trans("SuppliersCategoriesShort"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
			
			if ( DOL_VERSION <'3.7.0') {
			$newmenu->add("/categories/fiche.php?action=create&amp;type=1", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
			} else {
			$newmenu->add("/categories/card.php?action=create&amp;type=1", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
			}
		}
		//if (empty($leftmenu) || $leftmenu=="cat") $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
		}

	}

	
	/*
	 * Menu COMMERCIAL
	 */
	if ($mainmenu == 'commercial') {
		$langs->load("companies");

		// Propal
		if (! empty($conf->propal->enabled)) {
		$langs->load("propal");
		$newmenu->add("/comm/propal/index.php?leftmenu=propals", $langs->trans("Prop"), 0, $user->rights->propale->lire, '', $mainmenu, 'propals');
		if (DOL_VERSION >'4.0.0') {
			$newmenu->add("/comm/propal/card.php?action=create&amp;leftmenu=propals", $langs->trans("NewPropal"), 1, $user->rights->propale->creer);
		} else {
			$newmenu->add("/comm/propal.php?action=create&amp;leftmenu=propals", $langs->trans("NewPropal"), 1, $user->rights->propale->creer);
		}
		$newmenu->add("/comm/propal/list.php?leftmenu=propals", $langs->trans("List"), 1, $user->rights->propale->lire);
		if (empty($leftmenu) || $leftmenu=="propals") $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=0", $langs->trans("PropalsDraft"), 2, $user->rights->propale->lire);
		if (empty($leftmenu) || $leftmenu=="propals") $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=1", $langs->trans("PropalsOpened"), 2, $user->rights->propale->lire);
		if (empty($leftmenu) || $leftmenu=="propals") $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=2", $langs->trans("PropalStatusSigned"), 2, $user->rights->propale->lire);
		if (empty($leftmenu) || $leftmenu=="propals") $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=3", $langs->trans("PropalStatusNotSigned"), 2, $user->rights->propale->lire);
		if (empty($leftmenu) || $leftmenu=="propals") $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=4", $langs->trans("PropalStatusBilled"), 2, $user->rights->propale->lire);
		//$newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=2,3,4", $langs->trans("PropalStatusClosedShort"), 2, $user->rights->propale->lire);
		$newmenu->add("/comm/propal/stats/index.php?leftmenu=propals", $langs->trans("Statistics"), 1, $user->rights->propale->lire);
		}

		// Customers orders
		if (! empty($conf->commande->enabled)) {
		$langs->load("orders");
		$newmenu->add("/commande/index.php?leftmenu=orders", $langs->trans("CustomersOrders"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
		if ( DOL_VERSION <'3.7.0') {
			$newmenu->add("/commande/fiche.php?action=create&amp;leftmenu=orders", $langs->trans("NewOrder"), 1, $user->rights->commande->creer);
			$newmenu->add("/commande/liste.php?leftmenu=orders", $langs->trans("List"), 1, $user->rights->commande->lire);
			$newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=0", $langs->trans("StatusOrderDraftShort"), 2, $user->rights->commande->lire);
			$newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=1", $langs->trans("StatusOrderValidated"), 2, $user->rights->commande->lire);
			if ( ! empty($conf->expedition->enabled)) $newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=2", $langs->trans("StatusOrderSentShort"), 2, $user->rights->commande->lire);
			$newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=3", $langs->trans("StatusOrderToBill"), 2, $user->rights->commande->lire);	// The translation key is StatusOrderToBill but it means StatusDelivered. TODO We should renamed this later
			$newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=4", $langs->trans("StatusOrderProcessed"), 2, $user->rights->commande->lire);
			$newmenu->add("/commande/liste.php?leftmenu=orders&viewstatut=-1", $langs->trans("StatusOrderCanceledShort"), 2, $user->rights->commande->lire);
		} else {
			$newmenu->add("/commande/card.php?action=create&amp;leftmenu=orders", $langs->trans("NewOrder"), 1, $user->rights->commande->creer);
			$newmenu->add("/commande/list.php?leftmenu=orders", $langs->trans("List"), 1, $user->rights->commande->lire);
			$newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=0", $langs->trans("StatusOrderDraftShort"), 2, $user->rights->commande->lire);
			$newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=1", $langs->trans("StatusOrderValidated"), 2, $user->rights->commande->lire);
			if ( ! empty($conf->expedition->enabled)) $newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=2", $langs->trans("StatusOrderSentShort"), 2, $user->rights->commande->lire);
			$newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=3", $langs->trans("StatusOrderToBill"), 2, $user->rights->commande->lire);	// The translation key is StatusOrderToBill but it means StatusDelivered. TODO We should renamed this later
			$newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=4", $langs->trans("StatusOrderProcessed"), 2, $user->rights->commande->lire);
			$newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=-1", $langs->trans("StatusOrderCanceledShort"), 2, $user->rights->commande->lire);
		} 
		
		$newmenu->add("/commande/stats/index.php?leftmenu=orders", $langs->trans("Statistics"), 1, $user->rights->commande->lire);
		}

		// Suppliers orders
		if (! empty($conf->fournisseur->enabled)) {
		$langs->load("orders");
		$newmenu->add("/fourn/commande/index.php?leftmenu=orders_suppliers",$langs->trans("SuppliersOrders"), 0, $user->rights->fournisseur->commande->lire, '', $mainmenu, 'orders_suppliers');
		if ( DOL_VERSION >= '3.7.0') {
			$newmenu->add("/fourn/commande/card.php?action=create&amp;leftmenu=orders_suppliers", $langs->trans("NewOrder"), 1, $user->rights->fournisseur->commande->creer);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers", $langs->trans("List"), 1, $user->rights->fournisseur->commande->lire);
		} else {
			$newmenu->add("/fourn/commande/fiche.php?action=create&amp;leftmenu=orders_suppliers", $langs->trans("NewOrder"), 1, $user->rights->fournisseur->commande->creer);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers", $langs->trans("List"), 1, $user->rights->fournisseur->commande->lire);
		}
		
		if ( DOL_VERSION >='3.6.0' && DOL_VERSION <'3.7.0') {
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=0", $langs->trans("StatusOrderDraftShort"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=1", $langs->trans("StatusOrderValidated"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=2", $langs->trans("StatusOrderApprovedShort"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=3", $langs->trans("StatusOrderOnProcess"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=4", $langs->trans("StatusOrderReceivedPartially"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=5", $langs->trans("StatusOrderReceivedAll"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=6,7", $langs->trans("StatusOrderCanceled"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/liste.php?leftmenu=orders_suppliers&statut=9", $langs->trans("StatusOrderRefused"), 2, $user->rights->fournisseur->commande->lire);
		} 
		if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=0", $langs->trans("StatusOrderDraftShort"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=1", $langs->trans("StatusOrderValidated"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=2", $langs->trans("StatusOrderApprovedShort"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=3", $langs->trans("StatusOrderOnProcessShort"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=4", $langs->trans("StatusOrderReceivedPartially"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=5", $langs->trans("StatusOrderReceivedAll"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=6,7", $langs->trans("StatusOrderCanceled"), 2, $user->rights->fournisseur->commande->lire);
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=9", $langs->trans("StatusOrderRefused"), 2, $user->rights->fournisseur->commande->lire);
		}
		
		$newmenu->add("/commande/stats/index.php?leftmenu=orders_suppliers&amp;mode=supplier", $langs->trans("Statistics"), 1, $user->rights->fournisseur->commande->lire);
		}

		// Contrat
		if (! empty($conf->contrat->enabled)) {
		$langs->load("contracts");
		$newmenu->add("/contrat/index.php?leftmenu=contracts", $langs->trans("Contracts"), 0, $user->rights->contrat->lire, '', $mainmenu, 'contracts');
		if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add("/contrat/card.php?&action=create&amp;leftmenu=contracts", $langs->trans("NewContract"), 1, $user->rights->contrat->creer);
			$newmenu->add("/contrat/list.php?leftmenu=contracts", $langs->trans("List"), 1, $user->rights->contrat->lire);
		} else {
			$newmenu->add("/contrat/fiche.php?&action=create&amp;leftmenu=contracts", $langs->trans("NewContract"), 1, $user->rights->contrat->creer);
			$newmenu->add("/contrat/liste.php?leftmenu=contracts", $langs->trans("List"), 1, $user->rights->contrat->lire);
		}
		
		$newmenu->add("/contrat/services.php?leftmenu=contracts", $langs->trans("MenuServices"), 1, $user->rights->contrat->lire);
		$newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=0", $langs->trans("MenuInactiveServices"), 2, $user->rights->contrat->lire);
		$newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=4", $langs->trans("MenuRunningServices"), 2, $user->rights->contrat->lire);
		$newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=4&amp;filter=expired", $langs->trans("MenuExpiredServices"), 2, $user->rights->contrat->lire);
		$newmenu->add("/contrat/services.php?leftmenu=contracts&amp;mode=5", $langs->trans("MenuClosedServices"), 2, $user->rights->contrat->lire);
		}

		// Interventions
		if (! empty($conf->ficheinter->enabled)) {
		$langs->load("interventions");
		$newmenu->add("/fichinter/list.php?leftmenu=ficheinter", $langs->trans("Interventions"), 0, $user->rights->ficheinter->lire, '', $mainmenu, 'ficheinter');
		if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add("/fichinter/card.php?action=create&amp;leftmenu=ficheinter", $langs->trans("NewIntervention"), 1, $user->rights->ficheinter->creer);
		} else {
			$newmenu->add("/fichinter/fiche.php?action=create&amp;leftmenu=ficheinter", $langs->trans("NewIntervention"), 1, $user->rights->ficheinter->creer);
		}
		$newmenu->add("/fichinter/list.php?leftmenu=ficheinter", $langs->trans("List"), 1, $user->rights->ficheinter->lire);
		}

	}


	/*
	 * Menu COMPTA-FINANCIAL
	 */
	if ($mainmenu == 'accountancy') {
		$langs->load("companies");
		$langs->load("accountancy");

		// Customers invoices
		if (! empty($conf->facture->enabled)) {
			$langs->load("bills");
			$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills",$langs->trans("BillsCustomers"),0,$user->rights->facture->lire, '', $mainmenu, 'customers_bills');
			$newmenu->add("/compta/facture.php?action=create&amp;leftmenu=customers_bills",$langs->trans("NewBill"),1,$user->rights->facture->creer);
			if ( DOL_VERSION >= '3.8.0') {
				$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills",$langs->trans("List"),1,$user->rights->facture->lire);

				if (empty($leftmenu) || ($leftmenu == 'customers_bills'))
				{
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills&amp;search_status=0",$langs->trans("BillShortStatusDraft"),2,$user->rights->facture->lire);
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills&amp;search_status=1",$langs->trans("BillShortStatusNotPaid"),2,$user->rights->facture->lire);
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills&amp;search_status=2",$langs->trans("BillShortStatusPaid"),2,$user->rights->facture->lire);
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills&amp;search_status=3",$langs->trans("BillShortStatusCanceled"),2,$user->rights->facture->lire);
				}
			} else {
				$newmenu->add("/compta/facture/fiche-rec.php?leftmenu=customers_bills",$langs->trans("Repeatables"),1,$user->rights->facture->lire);
			}

			if ( DOL_VERSION <= '3.8.0') {
				$newmenu->add("/compta/facture/impayees.php?leftmenu=customers_bills",$langs->trans("Unpaid"),1,$user->rights->facture->lire);
			}

			if ( DOL_VERSION >='3.7.0' ) {
				$newmenu->add("/compta/paiement/list.php?leftmenu=customers_bills_payments",$langs->trans("Payments"),1,$user->rights->facture->lire);
			} else {
				$newmenu->add("/compta/paiement/liste.php?leftmenu=customers_bills_payments",$langs->trans("Payments"),1,$user->rights->facture->lire);
			}
	
			if (! empty($conf->global->BILL_ADD_PAYMENT_VALIDATION)) {
				$newmenu->add("/compta/paiement/avalider.php?leftmenu=customers_bills_payments",$langs->trans("MenuToValid"),2,$user->rights->facture->lire);
			}
			$newmenu->add("/compta/paiement/rapport.php?leftmenu=customers_bills_payments",$langs->trans("Reportings"),2,$user->rights->facture->lire);

			$newmenu->add("/compta/facture/stats/index.php?leftmenu=customers_bills", $langs->trans("Statistics"),1,$user->rights->facture->lire);
			
			if ( DOL_VERSION >= '3.8.0') {
				$newmenu->add("/compta/facture/mergepdftool.php",$langs->trans("MergingPDFTool"),1,$user->rights->facture->lire);
			}
		}

		// Suppliers
		if (! empty($conf->societe->enabled) && ! empty($conf->fournisseur->enabled)) {
			$langs->load("bills");
			$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills", $langs->trans("BillsSuppliers"),0,$user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills');

			if ( DOL_VERSION >='3.7.0' ) {
				$newmenu->add("/fourn/facture/card.php?action=create",$langs->trans("NewBill"),1,$user->rights->fournisseur->facture->creer);
			} else {
				$newmenu->add("/fourn/facture/fiche.php?action=create",$langs->trans("NewBill"),1,$user->rights->fournisseur->facture->creer);
			} 
			
			$newmenu->add("/fourn/facture/impayees.php", $langs->trans("Unpaid"),1,$user->rights->fournisseur->facture->lire);
			$newmenu->add("/fourn/facture/paiement.php", $langs->trans("Payments"),1,$user->rights->fournisseur->facture->lire);

			$newmenu->add("/compta/facture/stats/index.php?leftmenu=suppliers_bills&mode=supplier", $langs->trans("Statistics"),1,$user->rights->fournisseur->facture->lire);
		}

		// Orders
		if (! empty($conf->commande->enabled)) {
		$langs->load("orders");

		if (! empty($conf->facture->enabled)) {
			if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add("/commande/list.php?leftmenu=orders&amp;viewstatut=-3&amp;billed=0", $langs->trans("MenuOrdersToBill2"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
			} else {
			$newmenu->add("/commande/liste.php?leftmenu=orders&amp;viewstatut=-3", $langs->trans("MenuOrdersToBill2"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
			} 
		}
		// $newmenu->add("/commande/", $langs->trans("StatusOrderToBill"), 1, $user->rights->commande->lire);
		}

		if ( DOL_VERSION >='3.7.0' ) {
		// Supplier Orders
		if (! empty($conf->fournisseur->enabled)) {
			if (! empty($conf->global->SUPPLIER_MENU_ORDER_RECEIVED_INTO_INVOICE)) {
			$langs->load("supplier");
			$newmenu->add("/fourn/commande/list.php?leftmenu=orders&amp;search_status=5", $langs->trans("MenuOrdersSupplierToBill"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
			// if (empty($leftmenu) || $leftmenu=="orders") $newmenu->add("/commande/", $langs->trans("StatusOrderToBill"), 1, $user->rights->commande->lire);
			}
		}
		}

		// Donations
		if (! empty($conf->don->enabled)) {
		$langs->load("donations");
		if ( DOL_VERSION >='3.8.0' ) {
			$newmenu->add("/don/index.php?leftmenu=donations&amp;mainmenu=accountancy",$langs->trans("Donations"), 0, $user->rights->don->lire, '', $mainmenu, 'donations');

			$newmenu->add("/don/card.php?leftmenu=donations&amp;action=create",$langs->trans("NewDonation"), 1, $user->rights->don->creer);
			$newmenu->add("/don/list.php?leftmenu=donations",$langs->trans("List"), 1, $user->rights->don->lire);
		}
		else if ( DOL_VERSION >='3.7.0' ) {
			$newmenu->add("/compta/dons/index.php?leftmenu=donations&amp;mainmenu=accountancy",$langs->trans("Donations"), 0, $user->rights->don->lire, '', $mainmenu, 'donations');

			$newmenu->add("/compta/dons/card.php?action=create",$langs->trans("NewDonation"), 1, $user->rights->don->creer);
			$newmenu->add("/compta/dons/list.php",$langs->trans("List"), 1, $user->rights->don->lire);
		} else {
			$newmenu->add("/compta/dons/index.php?leftmenu=donations&amp;mainmenu=accountancy",$langs->trans("Donations"), 0, $user->rights->don->lire, '', $mainmenu, 'donations');

			$newmenu->add("/compta/dons/fiche.php?action=create",$langs->trans("NewDonation"), 1, $user->rights->don->creer);
			$newmenu->add("/compta/dons/liste.php",$langs->trans("List"), 1, $user->rights->don->lire);
		} 

		//$newmenu->add("/compta/dons/stats.php",$langs->trans("Statistics"), 1, $user->rights->don->lire);
		}

		if ( DOL_VERSION < '3.6.0' ) {
		// Trips and expenses
		if (! empty($conf->deplacement->enabled)) {
			$langs->load("trips");
			$newmenu->add("/compta/deplacement/index.php?leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("TripsAndExpenses"), 0, $user->rights->deplacement->lire, '', $mainmenu, 'tripsandexpenses');
			$newmenu->add("/compta/deplacement/fiche.php?action=create&amp;leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("New"), 1, $user->rights->deplacement->creer);
			$newmenu->add("/compta/deplacement/list.php?leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("List"), 1, $user->rights->deplacement->lire);
			$newmenu->add("/compta/deplacement/stats/index.php?leftmenu=tripsandexpenses&amp;mainmenu=accountancy", $langs->trans("Statistics"), 1, $user->rights->deplacement->lire);
		}

		}

		// Taxes and social contributions
		if (! empty($conf->tax->enabled) || ! empty($conf->salaries->enabled)) {
		if ( DOL_VERSION >='3.6.0' ) {
			global $mysoc;
			$langs->load("compta");

			$permtoshowmenu=((! empty($conf->tax->enabled) && $user->rights->tax->charges->lire) || (! empty($conf->salaries->enabled) && $user->rights->salaries->read));
			$newmenu->add("/compta/charges/index.php?leftmenu=tax&amp;mainmenu=accountancy",$langs->trans("MenuSpecialExpenses"), 0, $permtoshowmenu, '', $mainmenu, 'tax');
		} else {
			$newmenu->add("/compta/charges/index.php?leftmenu=tax&amp;mainmenu=accountancy",$langs->trans("MenuTaxAndDividends"), 0, $user->rights->tax->charges->lire, '', $mainmenu, 'tax');
		} 

		if ( DOL_VERSION >='3.6.0' ) {
			// Salaries
			if (! empty($conf->salaries->enabled)) {
			$langs->load("salaries");
			$newmenu->add("/compta/salaries/index.php?leftmenu=tax_salary&amp;mainmenu=accountancy",$langs->trans("Salaries"),1,$user->rights->salaries->read, '', $mainmenu, 'tax_salary');
			if ( DOL_VERSION < '3.7.0' ) {
				$newmenu->add("/compta/salaries/fiche.php?leftmenu=tax_salary&action=create",$langs->trans("NewPayment"),2,$user->rights->salaries->write);
			} else {
				$newmenu->add("/compta/salaries/card.php?leftmenu=tax_salary&action=create",$langs->trans("NewPayment"),2,$user->rights->salaries->write);
			}

			$newmenu->add("/compta/salaries/index.php?leftmenu=tax_salary",$langs->trans("Payments"),2,$user->rights->salaries->read);
			}
		}

		if ( DOL_VERSION >='3.8.0' ) {
			// Loan
			if (! empty($conf->loan->enabled))
			{
				$langs->load("loan");
				$newmenu->add("/loan/index.php?leftmenu=tax_loan&amp;mainmenu=accountancy",$langs->trans("Loans"),1,$user->rights->loan->read, '', $mainmenu, 'tax_loan');
				if (empty($leftmenu) || preg_match('/^tax_loan/i',$leftmenu)) $newmenu->add("/loan/card.php?leftmenu=tax_loan&action=create",$langs->trans("NewLoan"),2,$user->rights->loan->write);
				//if (empty($leftmenu) || preg_match('/^tax_loan/i',$leftmenu)) $newmenu->add("/loan/payment/list.php?leftmenu=tax_loan",$langs->trans("Payments"),2,$user->rights->loan->read);
				if ((empty($leftmenu) || preg_match('/^tax_loan/i',$leftmenu)) && ! empty($conf->global->LOAN_SHOW_CALCULATOR)) $newmenu->add("/loan/calc.php?leftmenu=tax_loan",$langs->trans("Calculator"),2,$user->rights->loan->calc);
			}
		}

		// Social contributions
		if (! empty($conf->tax->enabled)) {
			$newmenu->add("/compta/sociales/index.php?leftmenu=tax_social",$langs->trans("MenuSocialContributions"),1,$user->rights->tax->charges->lire);
			if ( DOL_VERSION >= '5.0.0') {
				$newmenu->add("/compta/sociales/card.php?leftmenu=tax_social&action=create",$langs->trans("MenuNewSocialContribution"), 2, $user->rights->tax->charges->creer);
				$newmenu->add("/compta/sociales/payments.php?leftmenu=tax_social&amp;mainmenu=accountancy&amp;mode=sconly",$langs->trans("Payments"), 2, $user->rights->tax->charges->lire);
			} else {
				$newmenu->add("/compta/sociales/charges.php?leftmenu=tax_social&action=create",$langs->trans("MenuNewSocialContribution"), 2, $user->rights->tax->charges->creer);
				$newmenu->add("/compta/charges/index.php?leftmenu=tax_social&amp;mainmenu=accountancy&amp;mode=sconly",$langs->trans("Payments"), 2, $user->rights->tax->charges->lire);
			}

			// VAT
			if (empty($conf->global->TAX_DISABLE_VAT_MENUS)) {
				$newmenu->add("/compta/tva/index.php?leftmenu=tax_vat&amp;mainmenu=accountancy",$langs->trans("VAT"),1,$user->rights->tax->charges->lire, '', $mainmenu, 'tax_vat');
				if ( DOL_VERSION >= '3.7.0') {
					$newmenu->add("/compta/tva/card.php?leftmenu=tax_vat&action=create",$langs->trans("NewPayment"),2,$user->rights->tax->charges->creer);
				} else {
					$newmenu->add("/compta/tva/fiche.php?leftmenu=tax_vat&action=create",$langs->trans("NewPayment"),2,$user->rights->tax->charges->creer);
				}
				$newmenu->add("/compta/tva/reglement.php?leftmenu=tax_vat",$langs->trans("Payments"),2,$user->rights->tax->charges->lire);
				$newmenu->add("/compta/tva/clients.php?leftmenu=tax_vat", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
				$newmenu->add("/compta/tva/quadri_detail.php?leftmenu=tax_vat", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);

				global $mysoc;

				if ( DOL_VERSION >= '3.7.0' ) {
					//Local Taxes 1
					if($mysoc->useLocalTax(1) && (isset($mysoc->localtax1_assuj) && $mysoc->localtax1_assuj=="1")) {
						$newmenu->add("/compta/localtax/index.php?leftmenu=tax_1_vat&amp;mainmenu=accountancy&amp;localTaxType=1",$langs->transcountry("LT1",$mysoc->country_code),1,$user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/card.php?leftmenu=tax_1_vat&action=create&amp;localTaxType=1",$langs->trans("NewPayment"),2,$user->rights->tax->charges->creer);
						$newmenu->add("/compta/localtax/reglement.php?leftmenu=tax_1_vat&amp;localTaxType=1",$langs->trans("Payments"),2,$user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/clients.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
					} // end Local Taxes 1

					//Local Taxes 2
					if($mysoc->useLocalTax(2) && (isset($mysoc->localtax2_assuj) && $mysoc->localtax2_assuj=="1")) {
						$newmenu->add("/compta/localtax/index.php?leftmenu=tax_2_vat&amp;mainmenu=accountancy&amp;localTaxType=2",$langs->transcountry("LT2",$mysoc->country_code),1,$user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/card.php?leftmenu=tax_2_vat&action=create&amp;localTaxType=2",$langs->trans("NewPayment"),2,$user->rights->tax->charges->creer);
						$newmenu->add("/compta/localtax/reglement.php?leftmenu=tax_2_vat&amp;localTaxType=2",$langs->trans("Payments"),2,$user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/clients.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
					} // end Local Taxes 2
				} else {
					//Local Taxes
					if($mysoc->country_code=='ES' && (isset($mysoc->localtax2_assuj) && $mysoc->localtax2_assuj=="1")) {
						$newmenu->add("/compta/localtax/index.php?leftmenu=tax_vat&amp;mainmenu=accountancy",$langs->transcountry("LT2",$mysoc->country_code),1,$user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/fiche.php?leftmenu=tax_vat&action=create",$langs->trans("NewPayment"),2,$user->rights->tax->charges->creer);
						$newmenu->add("/compta/localtax/reglement.php?leftmenu=tax_vat",$langs->trans("Payments"),2,$user->rights->tax->charges->lire);
						$newmenu->add("/compta/localtax/clients.php?leftmenu=tax_vat", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
						//$newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_vat", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
					} // end Local Taxes
				}
			} // end VAT
		} // end Social contributions
		} // end Taxes and social contributions

		if(DOL_VERSION >= '3.7.0')
		{
			// Accounting Expert
			if (! empty($conf->accounting->enabled))
			{
				$langs->load("accountancy");

				$permtoshowmenu=(! empty($conf->accounting->enabled) || $user->rights->accounting->ventilation->read || $user->rights->accounting->ventilation->dispatch || $user->rights->compta->resultat->lire);
				if ( DOL_VERSION >= '5.0.0') {
					$newmenu->add("/accountancy/index.php?leftmenu=accountancy",$langs->trans("MenuAccountancy"), 0, $permtoshowmenu, '', $mainmenu, 'accountancy');
				} else {
					$newmenu->add("/accountancy/customer/index.php?leftmenu=accountancy",$langs->trans("MenuAccountancy"), 0, $permtoshowmenu, '', $mainmenu, 'accountancy');
				}

				// Binding
				$newmenu->add("/accountancy/customer/index.php?leftmenu=dispatch_customer&amp;mainmenu=accountancy",$langs->trans("CustomersVentilation"),1,$user->rights->accounting->ventilation->read, '', $mainmenu, 'dispatch_customer');
				if ( DOL_VERSION >= '5.0.0') {
					if (empty($leftmenu) || $leftmenu=="dispatch_customer") $newmenu->add("/accountancy/customer/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer",$langs->trans("ToBind"),2,$user->rights->accounting->ventilation->dispatch);
					if (empty($leftmenu) || $leftmenu=="dispatch_customer") $newmenu->add("/accountancy/customer/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer",$langs->trans("Binded"),2,$user->rights->accounting->ventilation->read);
				} else {
					if (empty($leftmenu) || $leftmenu=="dispatch_customer") $newmenu->add("/accountancy/customer/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer",$langs->trans("ToDispatch"),2,$user->rights->accounting->ventilation->dispatch);
					if (empty($leftmenu) || $leftmenu=="dispatch_customer") $newmenu->add("/accountancy/customer/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer",$langs->trans("Dispatched"),2,$user->rights->accounting->ventilation->read);
				}
				if (! empty($conf->fournisseur->enabled))
				{
					$newmenu->add("/accountancy/supplier/index.php?leftmenu=dispatch_supplier&amp;mainmenu=accountancy",$langs->trans("SuppliersVentilation"),1,$user->rights->accounting->ventilation->read, '', $mainmenu, 'dispatch_supplier');
					if ( DOL_VERSION >= '5.0.0') {
						if (empty($leftmenu) || $leftmenu=="dispatch_supplier") $newmenu->add("/accountancy/supplier/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier",$langs->trans("ToBind"),2,$user->rights->accounting->ventilation->dispatch);
						if (empty($leftmenu) || $leftmenu=="dispatch_supplier") $newmenu->add("/accountancy/supplier/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier",$langs->trans("Binded"),2,$user->rights->accounting->ventilation->read);
					} else {
						if (empty($leftmenu) || $leftmenu=="dispatch_supplier") $newmenu->add("/accountancy/supplier/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier",$langs->trans("ToDispatch"),2,$user->rights->accounting->ventilation->dispatch);
						if (empty($leftmenu) || $leftmenu=="dispatch_supplier") $newmenu->add("/accountancy/supplier/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier",$langs->trans("Dispatched"),2,$user->rights->accounting->ventilation->read);
					}
				}

				// Journals
				if(! empty($conf->accounting->enabled) && ! empty($user->rights->accounting->comptarapport->lire) && $mainmenu == 'accountancy')
				{
					$newmenu->add('',$langs->trans("Journaux"),1,$user->rights->accounting->comptarapport->lire);

					$sql = "SELECT rowid, label, accountancy_journal";
					$sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
					$sql.= " WHERE entity = ".$conf->entity;
					$sql.= " AND clos = 0";
					$sql.= " ORDER BY label";

					$resql = $db->query($sql);
					if ($resql)
					{
						$numr = $db->num_rows($resql);
						$i = 0;

						if ($numr > 0)
						while ($i < $numr)
						{
							$objp = $db->fetch_object($resql);
							$newmenu->add('/accountancy/journal/bankjournal.php?mainmenu=accountancy&leftmenu=accountancy_journal&id_account='.$objp->rowid,$langs->trans("Journal").' - '.$objp->label,2,$user->rights->accounting->comptarapport->lire);
							$i++;
						}
					}
					else dol_print_error($db);
					$db->free($resql);

					// Add other journal
					$newmenu->add("/accountancy/journal/sellsjournal.php?mainmenu=accountancy&amp;leftmenu=accountancy_journal",$langs->trans("SellsJournal"),2,$user->rights->accounting->comptarapport->lire);
					$newmenu->add("/accountancy/journal/purchasesjournal.php?mainmenu=accountancy&amp;leftmenu=accountancy_journal",$langs->trans("PurchasesJournal"),2,$user->rights->accounting->comptarapport->lire);
				}

				// General Ledger
				$newmenu->add("/accountancy/bookkeeping/list.php?mainmenu=accountancy",$langs->trans("Bookkeeping"),1,$user->rights->accounting->mouvements->lire, '', $mainmenu, 'bookkeeping');

				// Balance
				if ( DOL_VERSION >= '3.9.0') {
					$newmenu->add("/accountancy/bookkeeping/balance.php?mainmenu=accountancy",$langs->trans("AccountBalance"),1,$user->rights->accounting->mouvements->lire);
				} else {
					if (empty($leftmenu) || preg_match('/bookkeeping/',$leftmenu)) $newmenu->add("/accountancy/bookkeeping/listbyyear.php?mainmenu=accountancy",$langs->trans("ByYear"),2,$user->rights->accounting->mouvements->lire);
					if (empty($leftmenu) || preg_match('/bookkeeping/',$leftmenu)) $newmenu->add("/accountancy/bookkeeping/balancebymonth.php?mainmenu=accountancy",$langs->trans("AccountBalanceByMonth"),2,$user->rights->accounting->mouvements->lire);
				}

				// Reports
				$langs->load("compta");

				$newmenu->add("/compta/resultat/index.php?leftmenu=report&amp;mainmenu=accountancy",$langs->trans("Reportings"),1,$user->rights->accounting->comptarapport->lire, '', $mainmenu, 'ca');

				if (empty($leftmenu) || preg_match('/report/',$leftmenu)) $newmenu->add("/compta/resultat/index.php?leftmenu=report",$langs->trans("ReportInOut"),2,$user->rights->accounting->comptarapport->lire);
				if (empty($leftmenu) || preg_match('/report/',$leftmenu)) $newmenu->add("/compta/resultat/clientfourn.php?leftmenu=report",$langs->trans("ByCompanies"),3,$user->rights->accounting->comptarapport->lire);
				if (empty($leftmenu) || preg_match('/report/',$leftmenu)) $newmenu->add("/compta/stats/index.php?leftmenu=report",$langs->trans("ReportTurnover"),2,$user->rights->accounting->comptarapport->lire);
				if (empty($leftmenu) || preg_match('/report/',$leftmenu)) $newmenu->add("/compta/stats/casoc.php?leftmenu=report",$langs->trans("ByCompanies"),3,$user->rights->accounting->comptarapport->lire);
				if (empty($leftmenu) || preg_match('/report/',$leftmenu)) $newmenu->add("/compta/stats/cabyuser.php?leftmenu=report",$langs->trans("ByUsers"),3,$user->rights->accounting->comptarapport->lire);
				if (empty($leftmenu) || preg_match('/report/',$leftmenu)) $newmenu->add("/compta/stats/cabyprodserv.php?leftmenu=report", $langs->trans("ByProductsAndServices"),3,$user->rights->accounting->comptarapport->lire);

				// Admin
				$langs->load("admin");
				$newmenu->add("/accountancy/admin/fiscalyear.php?mainmenu=accountancy", $langs->trans("Fiscalyear"),1,$user->rights->accounting->fiscalyear, '', $mainmenu, 'fiscalyear');
				$newmenu->add("/accountancy/admin/account.php?mainmenu=accountancy", $langs->trans("Chartofaccounts"),1,$user->rights->accounting->chartofaccount, '', $mainmenu, 'chartofaccount');
			}
		} 
		else
		{
			// Ventilation
			if (! empty($conf->comptabilite->enabled) && ($conf->global->MAIN_FEATURES_LEVEL >= 2))
			{
				$newmenu->add("/compta/ventilation/index.php?leftmenu=ventil",$langs->trans("Dispatch"),0,$user->rights->compta->ventilation->lire, '', $mainmenu, 'ventil');
				$newmenu->add("/compta/ventilation/liste.php",$langs->trans("ToDispatch"),1,$user->rights->compta->ventilation->lire);
				$newmenu->add("/compta/ventilation/lignes.php",$langs->trans("Dispatched"),1,$user->rights->compta->ventilation->lire);
			}
		}
		
		// Comptabilite
		if (! empty($conf->comptabilite->enabled)) {
		$langs->load("compta");

		// Bilan, resultats
		$newmenu->add("/compta/resultat/index.php?leftmenu=ca&amp;mainmenu=accountancy",$langs->trans("Reportings"),0,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire, '', $mainmenu, 'ca');

		$newmenu->add("/compta/resultat/index.php?leftmenu=ca",$langs->trans("ReportInOut"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
		$newmenu->add("/compta/resultat/clientfourn.php?leftmenu=ca",$langs->trans("ByCompanies"),2,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
		/* On verra ca avec module compabilite expert
		$newmenu->add("/compta/resultat/compteres.php?leftmenu=ca","Compte de resultat",2,$user->rights->compta->resultat->lire);
		$newmenu->add("/compta/resultat/bilan.php?leftmenu=ca","Bilan",2,$user->rights->compta->resultat->lire);
		*/
		$newmenu->add("/compta/stats/index.php?leftmenu=ca",$langs->trans("ReportTurnover"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);

		/*
		$newmenu->add("/compta/stats/cumul.php?leftmenu=ca","Cumule",2,$user->rights->compta->resultat->lire);
		if (! empty($conf->propal->enabled)) {
		$newmenu->add("/compta/stats/prev.php?leftmenu=ca","Previsionnel",2,$user->rights->compta->resultat->lire);
		$newmenu->add("/compta/stats/comp.php?leftmenu=ca","Transforme",2,$user->rights->compta->resultat->lire);
		*/
		$newmenu->add("/compta/stats/casoc.php?leftmenu=ca",$langs->trans("ByCompanies"),2,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
		$newmenu->add("/compta/stats/cabyuser.php?leftmenu=ca",$langs->trans("ByUsers"),2,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
		$newmenu->add("/compta/stats/cabyprodserv.php?leftmenu=ca", $langs->trans("ByProductsAndServices"),2,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);

		// Journaux
		//if ($leftmenu=="ca") $newmenu->add("/compta/journaux/index.php?leftmenu=ca",$langs->trans("Journaux"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
		$newmenu->add("/compta/journal/sellsjournal.php?leftmenu=ca",$langs->trans("SellsJournal"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
		$newmenu->add("/compta/journal/purchasesjournal.php?leftmenu=ca",$langs->trans("PurchasesJournal"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
		} // end Rapports
	} // end Menu COMPTA-FINANCIAL

	/*
	 * Menu BANK
	 */
	if ($mainmenu == 'bank') {
		$langs->load("withdrawals");
		$langs->load("banks");
		$langs->load("bills");

		// Bank-Caisse
		if (! empty($conf->banque->enabled)) {
		$newmenu->add("/compta/bank/index.php?leftmenu=bank&amp;mainmenu=bank",$langs->trans("MenuBankCash"),0,$user->rights->banque->lire, '', $mainmenu, 'bank');
		if ( DOL_VERSION >= '3.7.0') {
			$newmenu->add("/compta/bank/card.php?action=create",$langs->trans("MenuNewFinancialAccount"),1,$user->rights->banque->configurer);
		} else {
			$newmenu->add("/compta/bank/fiche.php?action=create",$langs->trans("MenuNewFinancialAccount"),1,$user->rights->banque->configurer);
		}
		$newmenu->add("/compta/bank/categ.php",$langs->trans("Rubriques"),1,$user->rights->banque->configurer);

		$newmenu->add("/compta/bank/search.php",$langs->trans("ListTransactions"),1,$user->rights->banque->lire);
		$newmenu->add("/compta/bank/budget.php",$langs->trans("ListTransactionsByCategory"),1,$user->rights->banque->lire);

		$newmenu->add("/compta/bank/virement.php",$langs->trans("BankTransfers"),1,$user->rights->banque->transfer);
		}

		// Prelevements
		if (! empty($conf->prelevement->enabled)) {
		$newmenu->add("/compta/prelevement/index.php?leftmenu=withdraw&amp;mainmenu=bank",$langs->trans("StandingOrders"),0,$user->rights->prelevement->bons->lire, '', $mainmenu, 'withdraw');

		//$newmenu->add("/compta/prelevement/demandes.php?status=0&amp;mainmenu=bank",$langs->trans("StandingOrderToProcess"),1,$user->rights->prelevement->bons->lire);

		$newmenu->add("/compta/prelevement/create.php?mainmenu=bank",$langs->trans("NewStandingOrder"),1,$user->rights->prelevement->bons->creer);


		$newmenu->add("/compta/prelevement/bons.php?mainmenu=bank",$langs->trans("WithdrawalsReceipts"),1,$user->rights->prelevement->bons->lire);
		$newmenu->add("/compta/prelevement/liste.php?mainmenu=bank",$langs->trans("WithdrawalsLines"),1,$user->rights->prelevement->bons->lire);
		$newmenu->add("/compta/prelevement/rejets.php?mainmenu=bank",$langs->trans("Rejects"),1,$user->rights->prelevement->bons->lire);
		
		$newmenu->add("/compta/prelevement/stats.php?mainmenu=bank",$langs->trans("Statistics"),1,$user->rights->prelevement->bons->lire);

		//$newmenu->add("/compta/prelevement/config.php",$langs->trans("Setup"),1,$user->rights->prelevement->bons->configurer);
		}

		// Gestion cheques
		if (! empty($conf->banque->enabled) && (! empty($conf->facture->enabled)) || ! empty($conf->global->MAIN_MENU_CHEQUE_DEPOSIT_ON)) {
		$newmenu->add("/compta/paiement/cheque/index.php?leftmenu=checks&amp;mainmenu=bank",$langs->trans("MenuChequeDeposits"),0,$user->rights->banque->cheque, '', $mainmenu, 'checks');
		if ( DOL_VERSION >= '3.7.0') {
			$newmenu->add("/compta/paiement/cheque/card.php?leftmenu=checks&amp;action=new&amp;mainmenu=bank",$langs->trans("NewChequeDeposit"),1,$user->rights->banque->cheque);
			$newmenu->add("/compta/paiement/cheque/list.php?leftmenu=checks&amp;mainmenu=bank",$langs->trans("List"),1,$user->rights->banque->cheque);
		} else {
			$newmenu->add("/compta/paiement/cheque/fiche.php?leftmenu=checks&amp;action=new&amp;mainmenu=bank",$langs->trans("NewChequeDeposit"),1,$user->rights->banque->cheque);
			$newmenu->add("/compta/paiement/cheque/liste.php?leftmenu=checks&amp;mainmenu=bank",$langs->trans("List"),1,$user->rights->banque->cheque);
		}
		}

	}

	/*
	 * Menu PRODUCTS-SERVICES
	 */
	if ($mainmenu == 'products') {
		// Products
		if (! empty($conf->product->enabled)) {
		$newmenu->add("/product/index.php?leftmenu=product&amp;type=0", $langs->trans("Products"), 0, $user->rights->produit->lire, '', $mainmenu, 'product');
		if ( DOL_VERSION < '3.7.0' ) {
			$newmenu->add("/product/fiche.php?leftmenu=product&amp;action=create&amp;type=0", $langs->trans("NewProduct"), 1, $user->rights->produit->creer);
			$newmenu->add("/product/liste.php?leftmenu=product&amp;type=0", $langs->trans("List"), 1, $user->rights->produit->lire);
		} else {
			$newmenu->add("/product/card.php?leftmenu=product&amp;action=create&amp;type=0", $langs->trans("NewProduct"), 1, $user->rights->produit->creer);
			$newmenu->add("/product/list.php?leftmenu=product&amp;type=0", $langs->trans("List"), 1, $user->rights->produit->lire);
		}
		
		if (! empty($conf->propal->enabled)) {
			$newmenu->add("/product/popuprop.php?leftmenu=stats&amp;type=0", $langs->trans("Statistics"), 1, $user->rights->produit->lire && $user->rights->propale->lire);
		}
		if (! empty($conf->stock->enabled)) {
			$newmenu->add("/product/reassort.php?type=0", $langs->trans("Stocks"), 1, $user->rights->produit->lire && $user->rights->stock->lire);
		}
		}

		// Services
		if (! empty($conf->service->enabled)) {
		$newmenu->add("/product/index.php?leftmenu=service&amp;type=1", $langs->trans("Services"), 0, $user->rights->service->lire, '', $mainmenu, 'service');
		if ( DOL_VERSION < '3.7.0' ) {
			$newmenu->add("/product/fiche.php?leftmenu=service&amp;action=create&amp;type=1", $langs->trans("NewService"), 1, $user->rights->service->creer);
			$newmenu->add("/product/liste.php?leftmenu=service&amp;type=1", $langs->trans("List"), 1, $user->rights->service->lire);
		} else {
			$newmenu->add("/product/card.php?leftmenu=service&amp;action=create&amp;type=1", $langs->trans("NewService"), 1, $user->rights->service->creer);
			$newmenu->add("/product/list.php?leftmenu=service&amp;type=1", $langs->trans("List"), 1, $user->rights->service->lire);
		}
		
		if (! empty($conf->propal->enabled)) {
			$newmenu->add("/product/popuprop.php?leftmenu=stats&amp;type=1", $langs->trans("Statistics"), 1, $user->rights->service->lire && $user->rights->propale->lire);
		}
		}

		// Categories
		if (! empty($conf->categorie->enabled)) {
		$langs->load("categories");
		$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=0", $langs->trans("Categories"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/categories/card.php?action=create&amp;type=0", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
			// $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
		} else {		
			$newmenu->add("/categories/fiche.php?action=create&amp;type=0", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
		
			//if (empty($leftmenu) || $leftmenu=="cat") $newmenu->add("/categories/liste.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
		}
		}

		// Warehouse
		if (! empty($conf->stock->enabled)) {
		$langs->load("stocks");
		
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/product/stock/index.php?leftmenu=stock", $langs->trans("Warehouses"), 0, $user->rights->stock->lire, '', $mainmenu, 'stock');
			$newmenu->add("/product/stock/card.php?action=create", $langs->trans("MenuNewWarehouse"), 1, $user->rights->stock->creer);
			$newmenu->add("/product/stock/list.php", $langs->trans("List"), 1, $user->rights->stock->lire);
		} else {		
			$newmenu->add("/product/stock/index.php?leftmenu=stock", $langs->trans("Stocks"), 0, $user->rights->stock->lire, '', $mainmenu, 'stock');
			$newmenu->add("/product/stock/fiche.php?action=create", $langs->trans("MenuNewWarehouse"), 1, $user->rights->stock->creer);
			$newmenu->add("/product/stock/liste.php", $langs->trans("List"), 1, $user->rights->stock->lire);
			$newmenu->add("/product/stock/valo.php", $langs->trans("EnhancedValue"), 1, $user->rights->stock->lire);
		}
		
		$newmenu->add("/product/stock/mouvement.php", $langs->trans("Movements"), 1, $user->rights->stock->mouvement->lire);
		if ($conf->fournisseur->enabled) $newmenu->add("/product/stock/replenish.php", $langs->trans("Replenishment"), 1, $user->rights->stock->mouvement->lire && $user->rights->fournisseur->lire);
		if ($conf->fournisseur->enabled) $newmenu->add("/product/stock/massstockmove.php", $langs->trans("StockTransfer"), 1, $user->rights->stock->mouvement->lire && $user->rights->fournisseur->lire);
		}

		// Expeditions
		if (! empty($conf->expedition->enabled)) {
		$langs->load("sendings");
		$newmenu->add("/expedition/index.php?leftmenu=sendings", $langs->trans("Shipments"), 0, $user->rights->expedition->lire, '', $mainmenu, 'sendings');
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/expedition/card.php?action=create2&amp;leftmenu=sendings", $langs->trans("NewSending"), 1, $user->rights->expedition->creer);
			$newmenu->add("/expedition/list.php?leftmenu=sendings", $langs->trans("List"), 1, $user->rights->expedition->lire);
		} else {
			$newmenu->add("/expedition/fiche.php?action=create2&amp;leftmenu=sendings", $langs->trans("NewSending"), 1, $user->rights->expedition->creer);
			$newmenu->add("/expedition/liste.php?leftmenu=sendings", $langs->trans("List"), 1, $user->rights->expedition->lire);
		}
		
		$newmenu->add("/expedition/stats/index.php?leftmenu=sendings", $langs->trans("Statistics"), 1, $user->rights->expedition->lire);
		}

	}


	/*
	 * Menu SUPPLIERS
	 */
	if ($mainmenu == 'suppliers') {
		$langs->load("suppliers");

		if (! empty($conf->societe->enabled) && ! empty($conf->fournisseur->enabled)) {
		$newmenu->add("/fourn/index.php?leftmenu=suppliers", $langs->trans("Suppliers"), 0, $user->rights->societe->lire && $user->rights->fournisseur->lire, '', $mainmenu, 'suppliers');

		// Security check
		$newmenu->add("/societe/soc.php?leftmenu=suppliers&amp;action=create&amp;type=f",$langs->trans("NewSupplier"), 1, $user->rights->societe->creer && $user->rights->fournisseur->lire);
		
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/fourn/list.php",$langs->trans("List"), 1, $user->rights->societe->lire && $user->rights->fournisseur->lire);
		} else {
			$newmenu->add("/fourn/liste.php",$langs->trans("List"), 1, $user->rights->societe->lire && $user->rights->fournisseur->lire);
		}
		$newmenu->add("/contact/list.php?leftmenu=suppliers&amp;type=f",$langs->trans("Contacts"), 1, $user->rights->societe->contact->lire && $user->rights->fournisseur->lire);
		$newmenu->add("/fourn/stats.php",$langs->trans("Statistics"), 1, $user->rights->societe->lire && $user->rights->fournisseur->lire);
		}

		if (! empty($conf->facture->enabled)) {
		$langs->load("bills");
		$newmenu->add("/fourn/facture/list.php?leftmenu=orders", $langs->trans("Bills"), 0, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'orders');
		
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/fourn/facture/card.php?action=create",$langs->trans("NewBill"), 1, $user->rights->fournisseur->facture->creer);
		} else {
			$newmenu->add("/fourn/facture/fiche.php?action=create",$langs->trans("NewBill"), 1, $user->rights->fournisseur->facture->creer);
		}

		$newmenu->add("/fourn/facture/paiement.php", $langs->trans("Payments"), 1, $user->rights->fournisseur->facture->lire);
		}

		if (! empty($conf->fournisseur->enabled)) {
		$langs->load("orders");
		$newmenu->add("/fourn/commande/index.php?leftmenu=suppliers",$langs->trans("Orders"), 0, $user->rights->fournisseur->commande->lire, '', $mainmenu, 'suppliers');
		$newmenu->add("/societe/societe.php?leftmenu=supplier", $langs->trans("NewOrder"), 1, $user->rights->fournisseur->commande->creer);
		
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/fourn/commande/list.php?leftmenu=suppliers", $langs->trans("List"), 1, $user->rights->fournisseur->commande->lire);
		} else {
			$newmenu->add("/fourn/commande/liste.php?leftmenu=suppliers", $langs->trans("List"), 1, $user->rights->fournisseur->commande->lire);
		}
		}

		if (! empty($conf->categorie->enabled)) {
		$langs->load("categories");
		$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=1", $langs->trans("Categories"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/categories/card.php?action=create&amp;type=1", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
		} else {
			$newmenu->add("/categories/fiche.php?action=create&amp;type=1", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
		}
		// $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
		}

	}

	/*
	 * Menu PROJECTS
	 */
	if ($mainmenu == 'project') {
		if (! empty($conf->projet->enabled)) {
		$langs->load("projects");

		// Project affected to user
		$newmenu->add("/projet/index.php?leftmenu=projects&mode=mine", $langs->trans("MyProjects"), 0, $user->rights->projet->lire, '', $mainmenu, 'projects');
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/projet/card.php?leftmenu=projects&action=create&mode=mine", $langs->trans("NewProject"), 1, $user->rights->projet->creer);
			$newmenu->add("/projet/list.php?leftmenu=projects&mode=mine", $langs->trans("List"), 1, $user->rights->projet->lire);
		} else {
			$newmenu->add("/projet/fiche.php?leftmenu=projects&action=create&mode=mine", $langs->trans("NewProject"), 1, $user->rights->projet->creer);
			$newmenu->add("/projet/liste.php?leftmenu=projects&mode=mine", $langs->trans("List"), 1, $user->rights->projet->lire);
		}
		
		// All project i have permission on
		$newmenu->add("/projet/index.php?leftmenu=projects", $langs->trans("Projects"), 0, $user->rights->projet->lire && $user->rights->projet->lire, '', $mainmenu, 'projects');
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/projet/card.php?leftmenu=projects&action=create", $langs->trans("NewProject"), 1, $user->rights->projet->creer && $user->rights->projet->creer);
			$newmenu->add("/projet/list.php?leftmenu=projects", $langs->trans("List"), 1, $user->rights->projet->lire && $user->rights->projet->lire);
		} else {
			$newmenu->add("/projet/fiche.php?leftmenu=projects&action=create", $langs->trans("NewProject"), 1, $user->rights->projet->creer && $user->rights->projet->creer);
			$newmenu->add("/projet/liste.php?leftmenu=projects", $langs->trans("List"), 1, $user->rights->projet->lire && $user->rights->projet->lire);
		}

		// Project affected to user
		$newmenu->add("/projet/activity/index.php?mode=mine", $langs->trans("MyActivities"), 0, $user->rights->projet->lire, '', $mainmenu, 'tasks');
		$newmenu->add("/projet/tasks.php?action=create&mode=mine", $langs->trans("NewTask"), 1, $user->rights->projet->creer);
		$newmenu->add("/projet/tasks/index.php?mode=mine", $langs->trans("List"), 1, $user->rights->projet->lire);
		$newmenu->add("/projet/activity/list.php?mode=mine", $langs->trans("NewTimeSpent"), 1, $user->rights->projet->creer);

		// All project I have permission on
		$newmenu->add("/projet/activity/index.php", $langs->trans("Activities"), 0, $user->rights->projet->lire && $user->rights->projet->lire, '', $mainmenu, 'tasks');
		$newmenu->add("/projet/tasks.php?action=create", $langs->trans("NewTask"), 1, $user->rights->projet->creer && $user->rights->projet->creer);
		$newmenu->add("/projet/tasks/index.php", $langs->trans("List"), 1, $user->rights->projet->lire && $user->rights->projet->lire);
		$newmenu->add("/projet/activity/list.php", $langs->trans("NewTimeSpent"), 1, $user->rights->projet->creer && $user->rights->projet->creer);
		}
	}

	/*
	 * Menu HRM
	 */
	if ($mainmenu == 'hrm') {
		if ( DOL_VERSION >= '3.9.0' ) {
			// HRM module
			if (! empty($conf->hrm->enabled))
			{
				$langs->load("hrm");

				$newmenu->add("/user/index.php?leftmenu=hrm&mode=employee", $langs->trans("Employees"), 0, $user->rights->hrm->employee->read, '', $mainmenu, 'hrm');
				$newmenu->add("/user/card.php?action=create&employee=1", $langs->trans("NewEmployee"), 1,$user->rights->hrm->employee->write);
				$newmenu->add("/user/index.php?leftmenu=hrm&mode=employee&contextpage=employeelist", $langs->trans("List"), 1,$user->rights->hrm->employee->read);
			}
		}
		// Leave/Holiday/Vacation module
		if (! empty($conf->holiday->enabled)) {
			$langs->load("holiday");

			if ( DOL_VERSION >= '3.8.0' ) {
				$newmenu->add("/holiday/list.php?leftmenu=hrm", $langs->trans("CPTitreMenu"), 0, $user->rights->holiday->read, '', $mainmenu, 'hrm');
			} else {
				$newmenu->add("/holiday/index.php?leftmenu=hrm", $langs->trans("CPTitreMenu"), 0, $user->rights->holiday->write, '', $mainmenu, 'hrm');
			}

			if ( DOL_VERSION >= '3.7.0' ) {
				$newmenu->add("/holiday/card.php?action=request", $langs->trans("MenuAddCP"), 1,$user->rights->holiday->write);
			} else {
				$newmenu->add("/holiday/fiche.php?action=request", $langs->trans("MenuAddCP"), 1,$user->rights->holiday->write);
			}
			
			if ( DOL_VERSION >= '3.8.0' ) {
				$newmenu->add("/holiday/list.php?leftmenu=hrm", $langs->trans("List"), 1,$user->rights->holiday->read);
				$newmenu->add("/holiday/list.php?select_statut=2&leftmenu=hrm", $langs->trans("ListToApprove"), 2, $user->rights->holiday->read);
			}

			$newmenu->add("/holiday/define_holiday.php?action=request", $langs->trans("MenuConfCP"), 1, $user->rights->holiday->define_holiday);
			$newmenu->add("/holiday/view_log.php?action=request", $langs->trans("MenuLogCP"), 1, $user->rights->holiday->view_log);
			if ( DOL_VERSION <= '3.9.0' ) {
				$newmenu->add("/holiday/month_report.php?&action=request", $langs->trans("MenuReportMonth"), 1, $user->rights->holiday->month_report);
			}
		}

		if ( DOL_VERSION >= '3.6.0' ) {
			// Trips and expenses (old module)
			if (! empty($conf->deplacement->enabled)) {
				$langs->load("trips");
				$newmenu->add("/compta/deplacement/index.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("TripsAndExpenses"), 0, $user->rights->deplacement->lire, '', $mainmenu, 'tripsandexpenses');
				if ( DOL_VERSION >= '3.7.0' ) {
					$newmenu->add("/compta/deplacement/card.php?action=create&amp;leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("New"), 1, $user->rights->deplacement->creer);
				} else {
					$newmenu->add("/compta/deplacement/fiche.php?action=create&amp;leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("New"), 1, $user->rights->deplacement->creer);
				}
				$newmenu->add("/compta/deplacement/list.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("List"), 1, $user->rights->deplacement->lire);
				$newmenu->add("/compta/deplacement/stats/index.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("Statistics"), 1, $user->rights->deplacement->lire);
			}
		}
		
		if ( DOL_VERSION >= '5.0.0' ) {
			// Expense report
			if (! empty($conf->expensereport->enabled)) {
				$langs->load("trips");
				$newmenu->add("/expensereport/index.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("TripsAndExpenses"), 0, $user->rights->expensereport->lire, '', $mainmenu, 'expensereport');
				$newmenu->add("/expensereport/card.php?action=create&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("New"), 1, $user->rights->expensereport->creer);
				$newmenu->add("/expensereport/list.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("List"), 1, $user->rights->expensereport->lire);
				$newmenu->add("/expensereport/list.php?search_status=2&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("ListToApprove"), 2, $user->rights->expensereport->approve);
				$newmenu->add("/expensereport/stats/index.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Statistics"), 1, $user->rights->expensereport->lire);
			}
		}
	}

	/*
	 * Menu TOOLS
	 */
	if ($mainmenu == 'tools') {
		if (! empty($conf->mailing->enabled)) {
			$langs->load("mails");
			$newmenu->add("/comm/mailing/index.php?leftmenu=mailing", $langs->trans("EMailings"), 0, $user->rights->mailing->lire, '', $mainmenu, 'mailing');
			if ( DOL_VERSION >= '3.7.0' ) {
				$newmenu->add("/comm/mailing/card.php?leftmenu=mailing&amp;action=create", $langs->trans("NewMailing"), 1, $user->rights->mailing->creer);
				$newmenu->add("/comm/mailing/list.php?leftmenu=mailing", $langs->trans("List"), 1, $user->rights->mailing->lire);
			} else {
				$newmenu->add("/comm/mailing/fiche.php?leftmenu=mailing&amp;action=create", $langs->trans("NewMailing"), 1, $user->rights->mailing->creer);
				$newmenu->add("/comm/mailing/liste.php?leftmenu=mailing", $langs->trans("List"), 1, $user->rights->mailing->lire);
			}
		}
		if (! empty($conf->export->enabled)) {
			$langs->load("exports");
			$newmenu->add("/exports/index.php?leftmenu=export",$langs->trans("FormatedExport"),0, $user->rights->export->lire, '', $mainmenu, 'export');
			$newmenu->add("/exports/export.php?leftmenu=export",$langs->trans("NewExport"),1, $user->rights->export->creer);
			//$newmenu->add("/exports/export.php?leftmenu=export",$langs->trans("List"),1, $user->rights->export->lire);
		}
		if (! empty($conf->import->enabled)) {
			$langs->load("exports");
			$newmenu->add("/imports/index.php?leftmenu=import",$langs->trans("FormatedImport"),0, $user->rights->import->run, '', $mainmenu, 'import');
			$newmenu->add("/imports/import.php?leftmenu=import",$langs->trans("NewImport"),1, $user->rights->import->run);
		}
	}

	/*
	 * Menu MEMBERS
	 */
	if ($mainmenu == 'members') {
		if (! empty($conf->adherent->enabled)) {
		$langs->load("members");
		$langs->load("compta");

		$newmenu->add("/adherents/index.php?leftmenu=members&amp;mainmenu=members",$langs->trans("Members"),0,$user->rights->adherent->lire, '', $mainmenu, 'members');
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/adherents/card.php?leftmenu=members&amp;action=create",$langs->trans("NewMember"),1,$user->rights->adherent->creer);
			$newmenu->add("/adherents/list.php?leftmenu=members",$langs->trans("List"),1,$user->rights->adherent->lire);
			$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=-1",$langs->trans("MenuMembersToValidate"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1",$langs->trans("MenuMembersValidated"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1&amp;filter=uptodate",$langs->trans("MenuMembersUpToDate"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1&amp;filter=outofdate",$langs->trans("MenuMembersNotUpToDate"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=0",$langs->trans("MenuMembersResiliated"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/stats/index.php?leftmenu=members",$langs->trans("MenuMembersStats"),1,$user->rights->adherent->lire);
		} else {
			$newmenu->add("/adherents/fiche.php?leftmenu=members&amp;action=create",$langs->trans("NewMember"),1,$user->rights->adherent->creer);
			$newmenu->add("/adherents/liste.php?leftmenu=members",$langs->trans("List"),1,$user->rights->adherent->lire);
			$newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=-1",$langs->trans("MenuMembersToValidate"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=1",$langs->trans("MenuMembersValidated"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=1&amp;filter=uptodate",$langs->trans("MenuMembersUpToDate"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=1&amp;filter=outofdate",$langs->trans("MenuMembersNotUpToDate"),2,$user->rights->adherent->lire);
			$newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=0",$langs->trans("MenuMembersResiliated"),2,$user->rights->adherent->lire);
		$newmenu->add("/adherents/stats/geo.php?leftmenu=members&mode=memberbycountry",$langs->trans("MenuMembersStats"),1,$user->rights->adherent->lire);
		}
		
		// Subscriptions
		$newmenu->add("/adherents/index.php?leftmenu=members&amp;mainmenu=members",$langs->trans("Subscriptions"),0,$user->rights->adherent->cotisation->lire, '', $mainmenu, 'subscriptions');
		
		if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=-1,1&amp;mainmenu=members",$langs->trans("NewSubscription"),1,$user->rights->adherent->cotisation->creer);
		} else {
			$newmenu->add("/adherents/liste.php?leftmenu=members&amp;statut=-1,1&amp;mainmenu=members",$langs->trans("NewSubscription"),1,$user->rights->adherent->cotisation->creer);
		}
		
		$newmenu->add("/adherents/cotisations.php?leftmenu=members",$langs->trans("List"),1,$user->rights->adherent->cotisation->lire);
		
		$newmenu->add("/adherents/stats/index.php?leftmenu=members",$langs->trans("MenuMembersStats"),1,$user->rights->adherent->lire);


		if (! empty($conf->categorie->enabled)) {
			$langs->load("categories");
			$newmenu->add("/categories/index.php?leftmenu=cat&amp;type=3", $langs->trans("Categories"), 0, $user->rights->categorie->lire, '', $mainmenu, 'cat');
			if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add("/categories/card.php?action=create&amp;type=3", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
			// $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
			} else {
			$newmenu->add("/categories/fiche.php?action=create&amp;type=3", $langs->trans("NewCategory"), 1, $user->rights->categorie->creer);
			// $newmenu->add("/categories/liste.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
			}
		}

		$newmenu->add("/adherents/index.php?leftmenu=export&amp;mainmenu=members",$langs->trans("Exports"),0,$user->rights->adherent->export, '', $mainmenu, 'export');
		if (! empty($conf->export->enabled) ) $newmenu->add("/exports/index.php?leftmenu=export",$langs->trans("Datas"),1,$user->rights->adherent->export);
		$newmenu->add("/adherents/htpasswd.php?leftmenu=export",$langs->trans("Filehtpasswd"),1,$user->rights->adherent->export);
		$newmenu->add("/adherents/cartes/carte.php?leftmenu=export",$langs->trans("MembersCards"),1,$user->rights->adherent->export);

		// Type
		$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members",$langs->trans("MembersTypes"),0,$user->rights->adherent->configurer, '', $mainmenu, 'setup');
		$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members&amp;action=create",$langs->trans("New"),1,$user->rights->adherent->configurer);
		$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members",$langs->trans("List"),1,$user->rights->adherent->configurer);
		}

	}

	// Add personalized menus and modules menus
	$menuArbo = new Menubase($db,'oblyon');
	$newmenu = $menuArbo->menuLeftCharger($newmenu,$mainmenu,$leftmenu,(empty($user->societe_id)?0:1),'oblyon',$tabMenu);

	// We update newmenu for special dynamic menus
	if (!empty($user->rights->banque->lire) && $mainmenu == 'bank')	 {	// Entry for each bank account
		$sql = "SELECT rowid, label, courant, rappro, courant";
		$sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
		$sql.= " WHERE entity = ".$conf->entity;
		$sql.= " AND clos = 0";
		$sql.= " ORDER BY label";

		$resql = $db->query($sql);
		if ($resql) {
		$numr = $db->num_rows($resql);
		$i = 0;

		if ($numr > 0) 	$newmenu->add('/compta/bank/index.php',$langs->trans("BankAccounts"),0,$user->rights->banque->lire, '', $mainmenu, 'bank');

		while ($i < $numr) {
			$objp = $db->fetch_object($resql);
			if ( DOL_VERSION >= '3.7.0' ) {
			$newmenu->add('/compta/bank/card.php?id='.$objp->rowid,$objp->label,1,$user->rights->banque->lire);
			} else {
			$newmenu->add('/compta/bank/fiche.php?id='.$objp->rowid,$objp->label,1,$user->rights->banque->lire);
			} 
			if ($objp->rappro && $objp->courant != 2 && empty($objp->clos)) {	// If not cash account and not closed and can be reconciliate
			$newmenu->add('/compta/bank/rappro.php?account='.$objp->rowid,$langs->trans("Conciliate"),2,$user->rights->banque->consolidate);
			}
			$i++;
		}
		}
		else dol_print_error($db);
		$db->free($resql);
	}
	
	// FTP
	if (!empty($conf->ftp->enabled) && $mainmenu == 'ftp') {
		$MAXFTP=20;
		$i=1;
		while ($i <= $MAXFTP) {
		$paramkey='FTP_NAME_'.$i;
		//print $paramkey;
		if (! empty($conf->global->$paramkey)) {
			$link="/ftp/index.php?idmenu=".$_SESSION["idmenu"]."&numero_ftp=".$i;

			$newmenu->add($link, dol_trunc($conf->global->$paramkey,24));
		}
		$i++;
		} //end while()
	} //end FTP

	} //end if ($mainmenu)


	// Build final $menu_array = $menu_array_before +$newmenu->liste + $menu_array_after
	//var_dump($menu_array_before);exit;
	//var_dump($menu_array_after);exit;
	$menu_array=$newmenu->liste;
	if (is_array($menu_array_before)) $menu_array=array_merge($menu_array_before, $menu_array);
	if (is_array($menu_array_after))	$menu_array=array_merge($menu_array, $menu_array_after);
	//var_dump($menu_array);exit;
	if (! is_array($menu_array)) return 0;

	// Show menu
	$invert=empty($conf->global->MAIN_MENU_INVERT)?"":"	is-inverted";
	if (empty($noout)) {
	$alt=0; 
	$num=count($menu_array);
	
	print '<nav class="db-nav	sec-nav'.$invert.'">'."\n";
	print '<ul class="sec-nav__list">'."\n";
	
	
	for ($i = 0; $i < $num; $i++) {
		$showmenu=true;
		$level= $menu_array[$i]['level'];
	
		if (! empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED) && empty($menu_array[$i]['enabled'])) 	$showmenu=false;
		
		$alt++;	
		
		// Place tabulation
		$tabstring='';
		$tabul=($menu_array[$i]['level'] - 1);
		if ($tabul > 0) {
		for ($j=0; $j < $tabul; $j++) {
			$tabstring.='<span class="caret	'.($langs->trans("DIRECTION")=='ltr'?'caret--left':'caret--right').'"></span> ';
		}
		}

		// For external modules
		$tmp=explode('?',$menu_array[$i]['url'],2);
		$url = $tmp[0];
		$param = (isset($tmp[1])?$tmp[1]:'');    // params in url of the menu link

		// Complete param to force leftmenu to '' to closed opend menu when we click on a link with no leftmenu defined.
		if ((! preg_match('/mainmenu/i',$param)) && (! preg_match('/leftmenu/i',$param)) && ! empty($menu_array[$i]['mainmenu'])) 
		{
			$param.=($param?'&':'').'mainmenu='.$menu_array[$i]['mainmenu'].'&leftmenu=';
		}
		if ((! preg_match('/mainmenu/i',$param)) && (! preg_match('/leftmenu/i',$param)) && empty($menu_array[$i]['mainmenu'])) 
		{
			$param.=($param?'&':'').'leftmenu=';
		}
		$url = dol_buildpath($url,1).($param?'?'.$param:'');

		$url=preg_replace('/__LOGIN__/',$user->login,$url);
		$url=preg_replace('/__USERID__/',$user->id,$url);

		print '<!-- Process menu entry with mainmenu='.$menu_array[$i]['mainmenu'].', leftmenu='.$menu_array[$i]['leftmenu'].', level='.$menu_array[$i]['level'].', enabled='.$menu_array[$i]['enabled'].' -->'."\n";		

		// Level Menu = 0
		if ($level == 0){
			if ($menu_array[$i]['enabled']) {
				print '<li class="sec-nav__item	item-heading">';
				print '<a class="sec-nav__link" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>';
				print (!empty($menu_array[$i]['leftmenu'])?'<i class="icon	icon--'.$menu_array[$i]['leftmenu'].'"></i>': '').$menu_array[$i]['titre'].' '.(!empty($conf->global->MAIN_MENU_INVERT) && !empty($menu_array[$i+1]['level'])?'<span class="caret	caret--top"></span>':'');
				print '</a>'."\n";
			} else if ($showmenu) {
				print '<li class="sec-nav__item	is-disabled"><a class="sec-nav__link	is-disabled" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$menu_array[$i]['titre'].'</a>'."\n";
			}

			if (!empty($menu_array[$i+1]['level'])) {
				print '<ul class="sec-nav__sub-list">'; 
			}
		}

		// Menu Level = 1 or 2
		if ($level > 0) {

		if ($menu_array[$i]['enabled']) {
			print '<li class="sec-nav__sub-item	item-level'.$menu_array[$i]['level'].'">';
			if ($menu_array[$i]['url']) print '<a class="sec-nav__link" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>'.$tabstring;
			print $menu_array[$i]['titre'];
			if ($menu_array[$i]['url']) print '</a>';
		} else if ($showmenu) {
			print '<li class="sec-nav__sub-item	is-disabled"><a class="sec-nav__link	is-disabled" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">'.$tabstring. '' .$menu_array[$i]['titre'].'</a>'."\n";
		}

		if ( empty($menu_array[$i+1]['level']) ) { 
			print "</ul></li> \n "; 
		} else {
		 print "</li> \n "; 
		}
		}

	}//end for

	print '</nav>'."\n";

	}
	return count($menu_array);
}


/**
 * Function to test if an entry is enabled or not
 *
 * @param	string		$type_user					0=We need backoffice menu, 1=We need frontoffice menu
 * @param	array		$menuentry					Array for menu entry
 * @param	array		$listofmodulesforexternal	Array with list of modules allowed to external users
 * @return	int										0=Hide, 1=Show, 2=Show gray
 */
function dol_oblyon_showmenu($type_user, &$menuentry, &$listofmodulesforexternal) {
	global $conf;

	//print 'type_user='.$type_user.' module='.$menuentry['module'].' enabled='.$menuentry['enabled'].' perms='.$menuentry['perms'];
	//print 'ok='.in_array($menuentry['module'], $listofmodulesforexternal);
	if (empty($menuentry['enabled'])) return 0;	// Entry disabled by condition
	if ($type_user && $menuentry['module']) {
	$tmploops=explode('|',$menuentry['module']);
	$found=0;
	foreach($tmploops as $tmploop) {
		if (in_array($tmploop, $listofmodulesforexternal)) {
		$found++; break;
		}
	}
	if (! $found) return 0;	// Entry is for menus all excluded to external users
	}
	if (! $menuentry['perms'] && $type_user) return 0; 											// No permissions and user is external
	if (! $menuentry['perms'] && ! empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED))	return 0;	// No permissions and option to hide when not allowed, even for internal user, is on
	if (! $menuentry['perms']) return 2;															// No permissions and user is external
	return 1;
}