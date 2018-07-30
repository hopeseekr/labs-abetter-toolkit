<?php

namespace ABetter\Toolkit;

class Component {

	// --- Private

	protected $scope;

	// --- Public

	// --- Constructor

	public function __construct(array $defined_vars = []) {
		$this->scope = (object) $defined_vars;
		$this->parse();
	}

	// --- Parse

	public function parse() {
		//
	}

}