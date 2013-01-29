<?php
/**
 * @author Antoine Hedgecock <antoine@pmg.se>
 * @author Jonas Eriksson <jonas@pmg.se>
 *
 * @copyright PMG Media Group AB
 */

namespace MCNTest;

class EmptyTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertTrue()
    {
        $this->assertTrue(true);
    }

    public function testFailed()
    {
	$this->assertTrue(false);
    }
}
