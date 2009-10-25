<pre><?php
include_once('D:/serwisy/pocms5.o/libs/Pocms/Tools.php');
/**
 * Importowanie modelu do bazy danych
 * na bazie doctrine
 */

require_once('D:/serwisy/pocms5.o/libs/doctrine/Doctrine.php');
define('POCMS_MODELS_PATH',dirname(__FILE__) . '/models/');

print_r(is_dir(POCMS_MODELS_PATH));
define('PREFIX_','');
spl_autoload_register(array('Doctrine', 'autoload'));
$manager = Doctrine_Manager::getInstance();
$manager->setCollate('utf8_polish_ci');
$manager->setCharset('utf8');

$conn = Doctrine_Manager::connection('mysql://root:ka13829@localhost/qwikioffice-distro','qwiki');

//$conn->setCharset('utf8');
//$manager->setAttribute(Doctrine::ATTR_TBLNAME_FORMAT, PREFIX_.'%s');
//$manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
//$manager->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL);
//$manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);


//$manager->createDatabases();
//
//$manager->dropDatabases();

//Doctrine::createTablesFromModels(POCMS_MODELS_PATH);


//Doctrine::generateModelsFromDb(POCMS_MODELS_PATH, array('qwiki'), array('generateTableClasses' => true));

Doctrine::loadModels(POCMS_MODELS_PATH);

$q = Doctrine_Query::create()->from('QoGroups g')->where('g.name = ?','de2mo');
$row = $q->execute();
pr($row->count());