<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;

  class mc_categories_description {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_index_categories_description_title');
      $this->description = CLICSHOPPING::getDef('module_index_categories_description_description');

      if (defined('MODULE_INDEX_CATEGORIES_DESCRIPTION_STATUS')) {
        $this->sort_order = (int)MODULE_INDEX_CATEGORIES_DESCRIPTION_SORT_ORDER;
        $this->enabled = (MODULE_INDEX_CATEGORIES_DESCRIPTION_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Category = Registry::get('Category');
      $CLICSHOPPING_Template = Registry::get('Template');

      if (CLICSHOPPING::getBaseNameIndex() && $CLICSHOPPING_Category->getPath()) {
        $content_width = (int)MODULE_INDEX_CATEGORIES_DESCRIPTION_CONTENT_WIDTH;

        if ($CLICSHOPPING_Category->getDepth() == 'nested' || $CLICSHOPPING_Category->getDepth() == 'products') {
          if (!empty($CLICSHOPPING_Category->getDescription())) {
            $categories_description = $CLICSHOPPING_Category->getDescription();

            $description = '<!-- Index Categories description start -->' . "\n";

            ob_start();
            require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/categories_description'));
            $description .= ob_get_clean();

            $description .= '<!-- Index Categories  description end -->' . "\n";

            $CLICSHOPPING_Template->addBlock($description, $this->group);
          }
        }
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_INDEX_CATEGORIES_DESCRIPTION_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_DESCRIPTION_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_DESCRIPTION_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_INDEX_CATEGORIES_DESCRIPTION_SORT_ORDER',
          'configuration_value' => '20',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );


      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                               ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_INDEX_CATEGORIES_DESCRIPTION_STATUS',
        'MODULE_INDEX_CATEGORIES_DESCRIPTION_CONTENT_WIDTH',
        'MODULE_INDEX_CATEGORIES_DESCRIPTION_SORT_ORDER'
      );
    }
  }
