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

class ApiGetDisplayTitle extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		global $wgSemanticTitleProperties;

		$pageTitle = $this->getMain()->getVal( 'pageTitle' );
		$displayName = $pageTitle;

		// Get namespace for this page title via MW API

		$api = new ApiMain(
			new DerivativeRequest(
				$this->getRequest(),
				array(
					'action' => 'query',
					'prop' => 'info',
					'titles' => $pageTitle
				)
			),
			false
		);

		$api->execute();
		$data = $api->getResultData();

		$key = array_shift( array_keys( $data["query"]["pages"] ) );
		$namespace = $data["query"]["pages"][$key]["ns"];

		// If the namespace is in $wgSemanticTitleProperties, extract the title property.

		if ( array_key_exists( $namespace, $wgSemanticTitleProperties ) ) {

			$displayNameProperty = $wgSemanticTitleProperties[$namespace];

			$api = new ApiMain(
				new DerivativeRequest(
					$this->getRequest(),
					array(
						'action' => 'askargs',
						'conditions' => $pageTitle,
						'printouts' => $displayNameProperty
					)
				),
				false
			);

			$api->execute();
			$data = $api->getResultData();

			$displayName = $data["query"]["results"][$pageTitle]["printouts"][$displayNameProperty][0];
			if ( $displayName == null )
				$displayName = $pageTitle;

		}

		$this->getResult()->addValue( null, $this->getModuleName(),
			array( 'pageTitle' => $pageTitle,
				'result' => $displayName )
		);

		return true;
	}

	public function getDescription() {
		return 'Returns the semantic display title for a given page name,' .
			' or empty string if it does not use content free page naming.';
	}
	public function getAllowedParams() {
		return array(
			'pageTitle' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true
			)
		);
	}
	public function getParamDescription() {
		return array(
			'pageTitle' => 'page name of the page whose semantic title is to be looked up (e.g. Item:1)'
		);
	}

	public function getExamples() {
		return array( 'api.php?action=getDisplayTitle&pageTitle=Item:1&format=jsonfm' );
	}
	public function getHelpUrls() {
		return '';
	}
}