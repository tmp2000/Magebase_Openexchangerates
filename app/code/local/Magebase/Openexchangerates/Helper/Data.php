<?php
/**
 * Helper class
 *
 * @category   Magebase
 * @package    Magebase_Openexchangerates
 * @author     Robert Popovic for Magebase
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Magebase_Openexchangerates_Helper_Data extends Mage_Core_Helper_Abstract
{
  /**
  * Path for the config for extension active status
  */
  const CONFIG_EXTENSION_ACTIVE = 'currency/mb_openexchangerates/enable';

  /**
  * Variable for if the extension is active
  *
  * @var bool
  */
  protected $bExtensionActive;

  /**
  * Check to see if the extension is active
  *
  * @return bool
  */
  public function isExtensionActive()
  {
    if ($this->bExtensionActive === null) {
      $this->bExtensionActive = Mage::getStoreConfigFlag(self::CONFIG_EXTENSION_ACTIVE);
    }
    return $this->bExtensionActive;
  }
}