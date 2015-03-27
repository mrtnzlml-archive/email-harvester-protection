<?php

namespace Test;

use Nette;
use Tester;

$container = require __DIR__ . '/bootstrap.php';

/**
 * Class Harvester
 * @package Test
 * @testCase
 */
class Harvester extends Tester\TestCase {

	/** @var Nette\DI\Container */
	private $container;

	private $tester;

	function __construct(Nette\DI\Container $container) {
		$this->container = $container;
		$this->tester = new PresenterTester($container, 'Homepage');
	}

	//Just naive implementations:

	public function testPlain() {
		$response = $this->tester->testAction('default');
		$html = (string)$response->getSource();

		Tester\Assert::true((bool)preg_match('/[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+/i', $html, $matches));
		Tester\Assert::same('my1@email.net', $matches[0]);
	}

	public function testJavascript() {
		$response = $this->tester->testAction('default');
		$html = (string)$response->getSource();

		Tester\Assert::true((bool)preg_match_all('/(%[a-z0-9]+)+/i', $html, $matches));
		$decoded = hex2bin(preg_replace('/[%]/', '', $matches[0][0]));
		preg_match('/[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+/i', $decoded, $result);
		Tester\Assert::same('my2@email.net', $result[0]);

		$decoded = hex2bin(preg_replace('/[%]/', '', $matches[0][4]));
		preg_match('/[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+/i', $decoded, $result);
		Tester\Assert::same('my5@email.net', $result[0]);
	}

	public function testJavascriptCharcode() {
		$response = $this->tester->testAction('default');
		$html = (string)$response->getSource();

		Tester\Assert::true((bool)preg_match('/fromCharCode\((([0-9]+,?)+)\)/i', $html, $matches));
		$tmp = [];
		foreach (explode(',', $matches[1]) as $part) {
			$tmp[] = chr($part);
		}
		preg_match('/[a-z0-9]+@[a-z0-9]+\.[a-z0-9]+/i', implode($tmp), $matches);
		Tester\Assert::same('my3@email.net', $matches[0]);
	}

	public function testHex() {
		$response = $this->tester->testAction('default');
		$html = (string)$response->getSource();

		Tester\Assert::true((bool)preg_match('/((%[a-z0-9]+)+)@((%[a-z0-9]+)+)\.((%[a-z0-9]+)+)/i', $html, $matches));
		$part1 = hex2bin(preg_replace('/[%]/', '', $matches[1]));
		$part2 = hex2bin(preg_replace('/[%]/', '', $matches[3]));
		$part3 = hex2bin(preg_replace('/[%]/', '', $matches[5]));
		Tester\Assert::same('my4@email.net', $part1 . '@' . $part2 . '.' . $part3);
	}

	public function testDrupal() {
		$response = $this->tester->testAction('default');
		$html = (string)$response->getSource();

		Tester\Assert::true((bool)preg_match('/[a-z0-9]+\[at\][a-z0-9]+\.[a-z0-9]+/i', $html, $matches));
		Tester\Assert::same('my6@email.net', preg_replace('/\[at\]/', '@', $matches[0]));
	}

	public function testTexy() {
		$response = $this->tester->testAction('default');
		$html = (string)$response->getSource();

		Tester\Assert::true((bool)preg_match('/([a-z0-9]+)<!-- ANTISPAM -->&#64;<!-- \/ANTISPAM -->([a-z0-9]+\.[a-z0-9]+)/i', $html, $matches));
		Tester\Assert::same('my7@email.net', $matches[1] . '@' . $matches[2]);
	}

	public function testProtected() {
		$response = $this->tester->testAction('default');

		$dom = @Tester\DomQuery::fromHtml($response->getSource());
		Tester\Assert::true($dom->has('a#protected'));
	}

}

$test = new Harvester($container);
$test->run();
