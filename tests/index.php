<?php

define('UDS_BILLBOARD_TESTS_URL', UDS_BILLBOARD_URL . '/tests');

require_once 'fixtures.php';


class UDS_Billboard_Test extends PHPUnit_Framework_TestCase
{	
	protected $fixtures;
	protected $bb_original;
	
	protected function setUp()
	{
		global $uds_bb_fixtures;
		$this->bb_original = get_option(UDS_BILLBOARD_OPTION, array());
	}
	
	protected function tearDown()
	{
		update_option(UDS_SLIDER_OPTION, $this->bb_original);
	}
	
	public function testBillboardAdd()
	{
		global $uds_bb_fixtures;
		
		$bb = new uBillboard($uds_bb_fixtures[0]);
		
		$this->assertEquals('billboard', $bb->name);
		$this->assertEquals(960, $bb->width);
		$this->assertEquals(380, $bb->height);
		$this->assertEquals(100, $bb->squareSize);
		$this->assertEquals(3, count($bb->slides));
	}
	
	public function testBillboardUpgradeFromV2()
	{
		global $uds_bb_original;
		
		// Test upgrading from V3
		$arr = serialize(array('billboard' => new uBillboard()));
		$upgraded_bbs = uBillboard::upgradeFromV2($arr);
		$this->assertFalse($upgraded_bbs);
		
		// Test upgrading from V2
		$upgraded_bbs = uBillboard::upgradeFromV2($uds_bb_original);
		$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $upgraded_bbs);
		$this->assertFalse(empty($upgraded_bbs));
		$this->assertInstanceOf('uBillboard', $upgraded_bbs['billboard']);
		$this->assertEquals('billboard', $upgraded_bbs['billboard']->name);
	}
	
	public function testBillboardEdit()
	{
		global $uds_bb_posts;
		
		// $uds_bb_posts would be $_POST in reality
		$bb = new uBillboard($uds_bb_posts);
		$this->assertEquals('billboard', $bb->name);
		$this->assertEquals('none', $bb->slides[0]->layout);
		$this->assertEquals('left', $bb->slides[1]->layout);
		$this->assertEquals('right', $bb->slides[2]->layout);
	}
}

?>