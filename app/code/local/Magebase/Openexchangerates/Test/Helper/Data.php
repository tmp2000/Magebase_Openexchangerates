<?php

/**
* Test for class Magebase_Openexchangerates_Helper_Data
*
* @category    Magebase
* @package     Magebase_Openexchangerates
*/
class Magebase_Openexchangerates_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
  /**
  * Tests is extension active
  *
  * @test
  * @loadFixture
  */
  public function testIsExtensionActive()
  {
    $this->assertTrue(
      Mage::helper('mb_openexchangerates')->isExtensionActive(),
      'Extension is not active please check config'
    );
  }
}