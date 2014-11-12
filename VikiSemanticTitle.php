<?php
/*
 * Copyright (c) 2014 The MITRE Corporation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

/**
* To activate the functionality of this extension include the following
* in your LocalSettings.php file:
* include_once("$IP/extensions/VikiSemanticTitle/VikiSemanticTitle.php");
*/

if ( !defined( 'MEDIAWIKI' ) ) {
	die( '<b>Error:</b> This file is part of a MediaWiki extension and cannot be run standalone.' );
}

if ( !defined( 'VIKIJS_VERSION' ) ) {
	die( '<b>Error:</b> The extension VikiSemanticTitle requires ' .
		'VIKI to be installed first. Be sure that VIKI is included '
		. 'on a line ABOVE the line where you\'ve included VikiSemanticTitle.' );
}

if ( version_compare( $wgVersion, '1.22', 'lt' ) ) {
	die( '<b>Error:</b> This version of VikiSemanticTitle '
		. 'is only compatible with MediaWiki 1.22 or above.' );
}

if ( !defined( 'SMW_VERSION' ) ) {
	die( '<b>Error:</b> You need to have ' .
		'<a href="https://semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a>' .
		' installed in order to use VikiSemanticTitle.' );
}

if ( version_compare( SMW_VERSION, '1.9', '<' ) ) {
	die( '<b>Error:</b> VikiSemanticTitle is only compatible with Semantic MediaWiki 1.9 or above.' );
}

$wgExtensionCredits['parserhook'][] = array (
	'name' => 'VikiSemanticTitle',
	'version' => '1.0.1',
	'author' => '[http://www.mediawiki.org/wiki/User:Jji Jason Ji]',
	'descriptionmsg' => 'vikisemantictitle-desc',
	'path' => __FILE__,
	'url' => 'http://www.mediawiki.org/wiki/Extension:VikiSemanticTitle'
);

$wgMessagesDirs['VikiSemanticTitle'] = __DIR__ . '/i18n';

$wgResourceModules['ext.VikiSemanticTitle'] = array(
	'localBasePath' => dirname( __FILE__ ),
	'remoteExtPath' => 'VikiSemanticTitle',
	'scripts' => array(
		'VikiSemanticTitle.js'
	),
	'dependencies' => array(
		'mediawiki.jqueryMsg',
	),
	'messages' => array(
		'vikisemantictitle-error-displaytitle-fetch'
	)
);

$wgAutoloadClasses['ApiGetDisplayTitle'] = __DIR__ . '/ApiGetDisplayTitle.php';

global $wgVIKI_Function_Hooks;

if ( !isset( $wgVIKI_Function_Hooks ) )
	$wgVIKI_Function_Hooks = array();

if ( array_key_exists( 'AfterVisitNodeHook', $wgVIKI_Function_Hooks ) )
	$wgVIKI_Function_Hooks['AfterVisitNodeHook'][] = 'VIKI.VikiSemanticTitle.checkForSemanticTitle';
else
	$wgVIKI_Function_Hooks['AfterVisitNodeHook'] =
		array( 'VIKI.VikiSemanticTitle.checkForSemanticTitle' );

$wgHooks['ParserFirstCallInit'][] = 'efVikiSemanticTitle_AddResource';
$wgAPIModules['getDisplayTitle'] = 'ApiGetDisplayTitle';

function efVikiSemanticTitle_AddResource ( & $parser ) {
	VikiJS::addResourceModule( "ext.VikiSemanticTitle" );
	return true;
}
